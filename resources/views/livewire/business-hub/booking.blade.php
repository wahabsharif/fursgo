<?php

use App\Models\Booking;
use App\Support\BookingDeclineReasons;
use App\Support\BusinessHubNav;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    private const SPACER_ID_COLUMN = 'goormer_spacer_id';
    private const BOOKING_ID_COLUMN = 'id';
    private const BOOKING_STATUS_COLUMN = 'booking_status';

    public $bookings;
    public array $tableRows = [];
    public $rescheduleCalendarBookings;
    public $statusCounts = [];
    public int $activeBookingsCount = 0;
    public string $activeStatus = 'all';
    public string $pendingSort = 'latest_submitted';
    public ?int $declineBookingId = null;
    public ?int $rescheduleBookingId = null;
    public ?int $completedBookingId = null;
    public ?int $cancelledBookingId = null;
    public ?int $bookingDetailsId = null;
    public ?string $rescheduleCalendarMonth = null;
    public ?string $rescheduleSelectedDate = null;
    public ?string $rescheduleSelectedTime = null;
    public int $rescheduleDurationMinutes = 60;
    private array $allowedStatuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
    private array $declinableStatuses = ['pending', 'confirmed'];
    private array $reschedulableStatuses = ['pending', 'confirmed', 'completed'];

    private function spacerId(): int
    {
        return (int) (Auth::guard('groomer_spacer')->id() ?? (Auth::id() ?? 0));
    }

    private function scopedBookingQuery(int $bookingId)
    {
        return Booking::query()->where(self::SPACER_ID_COLUMN, $this->spacerId())->where(self::BOOKING_ID_COLUMN, $bookingId);
    }

    private function applyBookingSort($query): void
    {
        if ($this->pendingSort === 'oldest_submitted') {
            $query->orderBy('created_at')->orderBy(self::BOOKING_ID_COLUMN);

            return;
        }

        if ($this->pendingSort === 'amount_high') {
            $query->orderByDesc('amount')->orderByDesc('created_at')->orderByDesc(self::BOOKING_ID_COLUMN);

            return;
        }

        if ($this->pendingSort === 'amount_low') {
            $query->orderBy('amount')->orderByDesc('created_at')->orderByDesc(self::BOOKING_ID_COLUMN);

            return;
        }

        $query->orderByDesc('created_at')->orderByDesc(self::BOOKING_ID_COLUMN);
    }

    public function bookingSearchHaystack($booking): string
    {
        $owner = strtolower((string) ($booking->petOwner?->name ?? ''));
        $pets = strtolower($booking->pets->pluck('name')->filter()->implode(' '));
        $id = (string) $booking->id;
        $padded = 'fg-' . str_pad($id, 5, '0', STR_PAD_LEFT);

        return trim($padded . ' ' . $id . ' ' . $owner . ' ' . $pets);
    }

    private function toTableRow($booking): array
    {
        $firstPet = $booking->pets->first();
        $isSpace = $this->isSpaceUser();
        $spaceService = $this->formatSpaceServiceLabel($booking->service, $booking->time);

        return [
            'id' => (int) $booking->id,
            'idLabel' => 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT),
            'owner' => (string) ($booking->petOwner?->name ?? 'N/A'),
            'petName' => (string) ($firstPet?->name ?? 'N/A'),
            'petType' => $firstPet?->pet_type,
            'visitType' => $isSpace ? $this->formatSpaceVisitLabel($booking->visit_type) : ($booking->visit_type ? ucfirst((string) $booking->visit_type) : 'N/A'),
            'serviceHtml' => $isSpace ? e($spaceService) : $this->formatServiceTypeLabel($booking->service),
            'service' => $isSpace ? $spaceService : (string) ($booking->service ?: 'N/A'),
            'date' => optional($booking->date)->format('d/m/y') ?: '',
            'time' => (string) ($booking->time ?? ''),
            'status' => (string) $booking->booking_status,
            'statusLabel' => ucfirst((string) $booking->booking_status),
            'amount' => number_format((float) $booking->amount, 2),
            'haystack' => $this->bookingSearchHaystack($booking),
        ];
    }

    public function formatServiceTypeLabel(?string $service): string
    {
        $words = preg_split('/\s+/', trim((string) $service), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return 'N/A';
        }

        if (count($words) <= 2) {
            return e(implode(' ', $words));
        }

        return e(implode(' ', array_slice($words, 0, 2))) . '<br>' . e(implode(' ', array_slice($words, 2)));
    }

    private function spacerUserType(): string
    {
        $user = Auth::guard('groomer_spacer')->user() ?? Auth::user();

        return strtolower((string) ($user?->user_type ?? ''));
    }

    private function isSpaceUser(): bool
    {
        return $this->spacerUserType() === 'space';
    }

    public function formatSpaceVisitLabel(?string $visitType): string
    {
        $rawVisit = (string) ($visitType ?? '');
        $label = str_replace('_', ' ', strtolower(trim($rawVisit)));

        return match (true) {
            $label === '' => 'Garden/Shed',
            str_contains($rawVisit, '/') => str_replace([' / ', ' /', '/ '], '/', $rawVisit),
            $label === 'garden shed' || $label === 'garden/shed' => 'Garden/Shed',
            $label === 'home' || $label === 'home visit' => 'Home Visit',
            $label === 'salon' || $label === 'salon visit' => 'Salon',
            $label === 'private room' => 'Private room',
            $label === 'mobile station' => 'Mobile Station',
            $label === 'other' => 'Other',
            default => ucwords($label),
        };
    }

    public function formatSpaceServiceLabel(?string $service, mixed $time = null): string
    {
        $serviceLower = strtolower(trim((string) $service));
        $label = match (true) {
            (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-Day',
            (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-Day',
            str_contains($serviceLower, 'hour') => 'Hourly',
            default => null,
        };

        $timeRaw = is_object($time) && method_exists($time, 'format') ? $time->format('H:i') : trim((string) ($time ?? ''));

        if ($label === null && str_contains($timeRaw, '-')) {
            $rangeParts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
            if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                if ($diff < 0) {
                    $diff += 24 * 60;
                }
                $label = $diff >= 7 * 60 ? 'Full-Day' : ($diff >= 3 * 60 ? 'Half-Day' : 'Hourly');
            }
        }

        if ($label !== null) {
            return $label;
        }

        $plain = trim((string) $service);

        return $plain !== '' ? $plain : 'N/A';
    }

    public function bookingInvoicePdfUrl($booking): string
    {
        $bookingId = is_object($booking) ? $booking->getRouteKey() : $booking;

        return url('/business-hub/bookings/' . $bookingId . '/invoice.pdf');
    }

    public function refreshBookingsAndCounts(bool $force = false, bool $refreshCounts = true): void
    {
        // Avoid heavy polling refresh while a modal is open, so calendar
        // interactions (day/time/month selection) stay responsive.
        if (!$force && ($this->declineBookingId !== null || $this->rescheduleBookingId !== null || $this->completedBookingId !== null || $this->cancelledBookingId !== null || $this->bookingDetailsId !== null)) {
            return;
        }

        $profileId = $this->spacerId();

        if ($profileId === 0) {
            $this->bookings = collect();
            $this->tableRows = [];
            $this->activeBookingsCount = 0;

            return;
        }

        if ($refreshCounts) {
            $counts = Booking::query()
                ->where(self::SPACER_ID_COLUMN, $profileId)
                ->selectRaw(self::BOOKING_STATUS_COLUMN . ', COUNT(*) as total')
                ->groupBy(self::BOOKING_STATUS_COLUMN)
                ->pluck('total', self::BOOKING_STATUS_COLUMN);

            $this->statusCounts = collect(['pending', 'confirmed', 'completed', 'cancelled'])
                ->mapWithKeys(fn($status) => [$status => (int) ($counts[$status] ?? 0)])
                ->all();
        }

        $bookingsQuery = Booking::with(['petOwner:id,name', 'pets:id,name,pet_type,breed'])->where(self::SPACER_ID_COLUMN, $profileId);

        if ($this->activeStatus !== 'all') {
            $bookingsQuery->where(self::BOOKING_STATUS_COLUMN, $this->activeStatus);
        }

        $this->applyBookingSort($bookingsQuery);

        $this->bookings = $bookingsQuery->get();
        $this->tableRows = $this->bookings->map(fn($booking) => $this->toTableRow($booking))->values()->all();
        $this->activeBookingsCount = $this->bookings->count();

        if ($refreshCounts) {
            Cache::forget("dashboard_sidebar_booking_counts_{$profileId}");

            // Keep sidebar status counters in sync without a full page refresh.
            $this->dispatch('booking-counts-updated', counts: $this->statusCounts);
        }
    }

    public function mount(): void
    {
        $this->bookings = collect();
        $this->tableRows = [];
        $this->rescheduleCalendarBookings = collect();

        $requestedStatus = strtolower((string) request()->query('booking_status', ''));
        if ($requestedStatus === '') {
            $requestedStatus = BusinessHubNav::fromSession()['active_booking_status'];
        }

        $this->activeStatus = in_array($requestedStatus, $this->allowedStatuses, true) ? $requestedStatus : 'all';
        $this->refreshBookingsAndCounts(force: true);

        // Sync dashboard header with the initial filter (e.g. ?booking_status=pending).
        $this->dispatch('booking-status-changed', status: $this->activeStatus === 'all' ? '' : $this->activeStatus);
    }

    public function setActiveStatus(string $status): void
    {
        $status = strtolower($status);
        if (!in_array($status, $this->allowedStatuses, true)) {
            return;
        }

        if ($this->activeStatus === $status) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->activeStatus = $status;
        $this->refreshBookingsAndCounts(force: true, refreshCounts: false);
        $this->dispatch('booking-status-changed', status: $status === 'all' ? '' : $status);
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
        if ($this->activeStatus === 'all') {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->activeStatus = 'all';
        $this->refreshBookingsAndCounts(force: true, refreshCounts: false);
        $this->dispatch('booking-status-changed', status: '');
        $this->dispatch('bookings-tabs-loading-end');
    }

    public function refreshBookingsForPoll(): void
    {
        $this->refreshBookingsAndCounts(refreshCounts: false);
    }

    public function acceptBooking(int $bookingId): void
    {
        $booking = $this->scopedBookingQuery($bookingId)->firstOrFail();

        if ($booking->booking_status !== 'pending') {
            return;
        }

        $booking->update(['booking_status' => 'confirmed']);
        $this->refreshBookingsAndCounts(force: true);
    }

    public function cancelBooking(int $bookingId, ?string $reason = null): void
    {
        $booking = $this->scopedBookingQuery($bookingId)->firstOrFail();

        if (!in_array($booking->booking_status, $this->declinableStatuses, true)) {
            return;
        }

        $booking->update([
            'booking_status' => 'cancelled',
            'cancelled_by' => $this->declinedByLabel(),
            'cancellation_reason' => BookingDeclineReasons::normalize($reason),
        ]);
        $this->refreshBookingsAndCounts(force: true);
    }

    public function openDeclineModal(int $bookingId): void
    {
        $bookingExists = $this->scopedBookingQuery($bookingId)->whereIn('booking_status', $this->declinableStatuses)->exists();

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

    public function confirmDeclineBooking(?string $reason = null): void
    {
        if ($this->declineBookingId === null) {
            return;
        }

        $this->cancelBooking($this->declineBookingId, $reason);
        $this->declineBookingId = null;
        $this->dispatch('decline-modal-closed');
    }

    private function declinedByLabel(): string
    {
        $user = Auth::guard('groomer_spacer')->user() ?? Auth::user();

        return strtolower((string) ($user->user_type ?? 'groomer')) === 'space' ? 'Space Host' : 'Groomer';
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
        $this->rescheduleCalendarBookings = Booking::query()
            ->where(self::SPACER_ID_COLUMN, Auth::id())
            ->where(self::BOOKING_STATUS_COLUMN, '!=', 'cancelled')
            ->select([self::BOOKING_ID_COLUMN, 'date', self::BOOKING_STATUS_COLUMN])
            ->get();

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
        $this->rescheduleCalendarBookings = collect();
        $this->dispatch('reschedule-modal-closed');
    }

    public function openCompletedBookingModal(int $bookingId): void
    {
        $bookingExists = $this->scopedBookingQuery($bookingId)->where(self::BOOKING_STATUS_COLUMN, 'completed')->exists();
        if (!$bookingExists) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->completedBookingId = $bookingId;
        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('completed-booking-modal-opened');
    }

    public function closeCompletedBookingModal(): void
    {
        $this->completedBookingId = null;
        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('completed-booking-modal-closed');
    }

    public function openCancelledBookingModal(int $bookingId): void
    {
        $bookingExists = $this->scopedBookingQuery($bookingId)->where(self::BOOKING_STATUS_COLUMN, 'cancelled')->exists();
        if (!$bookingExists) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->cancelledBookingId = $bookingId;
        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('cancelled-booking-modal-opened');
    }

    #[On('open-booking-details')]
    public function openBookingViewModal($bookingId = null): void
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? ($bookingId['id'] ?? null);
        }

        $this->bookingDetailsId = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : null;
        $this->skipRender();
    }

    #[On('booking-details-closed')]
    public function closeBookingDetailsDrawer(): void
    {
        $this->bookingDetailsId = null;
        $this->skipRender();
    }

    public function closeCancelledBookingModal(): void
    {
        $this->cancelledBookingId = null;
        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('cancelled-booking-modal-closed');
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
        $start = DateTime::createFromFormat('H:i A', $this->rescheduleSelectedTime) ?: DateTime::createFromFormat('h:i A', $this->rescheduleSelectedTime);
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

        $this->refreshBookingsAndCounts(force: true);
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
        $this->refreshBookingsAndCounts(refreshCounts: false);
        $this->dispatch('bookings-tabs-loading-end');
    }
}; ?>

<section class="bookings-board" wire:poll.visible.60s="refreshBookingsForPoll" x-data="{
    query: window.__bhBookingsSearchQuery || '',
    visible: 10,
    matchCount: 0,
    observer: null,
    isSpace: {{ $this->isSpaceUser() ? 'true' : 'false' }},
    isGroomerService: {{ in_array($this->spacerUserType(), ['groomer', 'space'], true) ? 'true' : 'false' }},
    rows: [],
    init() {
        this.readRows();
        this.applyFilter();
        this.$watch('query', (value) => {
            window.__bhBookingsSearchQuery = value;
            this.visible = 10;
            this.applyFilter();
        });
        const wrap = this.$refs.tableWrap;
        if (wrap) {
            this.observer = new MutationObserver(() => this.applyFilter());
            this.observer.observe(wrap, { childList: true, subtree: true });
        }
    },
    destroy() {
        this.observer?.disconnect();
    },
    readRows() {
        const json = this.$refs.bookingRowsJson;
        if (!json) {
            this.rows = [];
            return;
        }
        const raw = json.content ? json.content.textContent : json.textContent;
        try {
            this.rows = JSON.parse(raw || '[]');
        } catch (error) {
            this.rows = [];
        }
    },
    get filteredRows() {
        const q = this.query.trim().toLowerCase();
        if (!q) {
            return this.rows;
        }
        return this.rows.filter((row) => (row.haystack || '').includes(q));
    },
    get visibleRows() {
        const rows = this.filteredRows;
        return this.query.trim() !== '' ? rows : rows.slice(0, this.visible);
    },
    applyFilter() {
        if (this.$root.querySelector('.bookings-table-all')) {
            this.matchCount = this.filteredRows.length;
            return;
        }
        const q = this.query.trim().toLowerCase();
        const rows = this.$root.querySelectorAll('tr.bookings-data-row');
        const searching = q.length > 0;
        const limit = searching ? Number.POSITIVE_INFINITY : this.visible;
        let matches = 0;
        let shown = 0;
        rows.forEach((tr) => {
            const haystack = tr.getAttribute('data-search') || '';
            const isMatch = !searching || haystack.includes(q);
            if (isMatch) {
                matches += 1;
            }
            const show = isMatch && shown < limit;
            if (show) {
                shown += 1;
            }
            tr.toggleAttribute('hidden', !show);
        });
        this.matchCount = matches;
        this.$root.querySelectorAll('tr.bookings-empty-row').forEach((tr) => {
            tr.toggleAttribute('hidden', matches > 0);
        });
    },
    loadMore() {
        this.visible += 10;
        this.applyFilter();
    },
    openView(id) {
        const row = this.rows.find((item) => Number(item.id) === Number(id)) || { id: Number(id) };
        window.dispatchEvent(new CustomEvent('booking-details-open', { detail: row }));
    },
    get showLoadMore() {
        if (this.query.trim() !== '') {
            return false;
        }
        if (this.$root.querySelector('tr.bookings-empty-row:not([hidden])')) {
            return false;
        }
        if (this.$root.querySelector('.bookings-table-all')) {
            return this.rows.length > this.visible;
        }
        return this.matchCount > this.visible;
    }
}">
    <template hidden x-ref="bookingRowsJson">@json($tableRows)</template>
    <div class="bookings-board-header">
        <label class="bookings-search">
            <input type="text" inputmode="search" autocomplete="off" spellcheck="false" x-model="query"
                placeholder="Search by pet, owner or booking ID ..." aria-label="Search bookings"
                x-on:keydown.escape.prevent="query = ''" />
            <span class="bookings-search-icon" aria-hidden="true">
                <img src="{{ asset('images/business-hub/icon-search.svg') }}" alt="" width="16" height="16">
            </span>
        </label>

        <div class="booking-list-header">
            <div class="booking-pill-row">
                @php
                    $bookingPills = [['status' => 'all', 'label' => 'All Bookings'], ['status' => 'pending', 'label' => 'Pending'], ['status' => 'confirmed', 'label' => 'Confirmed'], ['status' => 'completed', 'label' => 'Completed'], ['status' => 'cancelled', 'label' => 'Cancelled']];
                    $allBookingsCount = array_sum($statusCounts);
                @endphp
                @foreach ($bookingPills as $pill)
                    <button type="button" wire:click="setActiveStatus('{{ $pill['status'] }}')"
                        @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                        class="booking-pill {{ $activeStatus === $pill['status'] ? 'is-active' : '' }}{{ $pill['status'] !== 'all' ? ' is-' . $pill['status'] : '' }}">
                        {{ $pill['label'] }}
                        ({{ $pill['status'] === 'all' ? $allBookingsCount : $statusCounts[$pill['status']] ?? 0 }})
                    </button>
                @endforeach
            </div>

            <x-business-hub.common.sort-dropdown :pending-sort="$pendingSort" />
        </div>
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
                $durationLabel = '(' . (int) floor($durationMinutes / 60) . 'hr' . ($durationMinutes % 60 ? ' ' . $durationMinutes % 60 . 'm' : '') . ')';
                return $startDt->format('H:i') . ' - ' . $endDt->format('H:i') . ' ' . strtolower($endDt->format('a')) . ' ' . $durationLabel;
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

    <div class="bookings-table-wrap" x-ref="tableWrap">
        <div class="bookings-table-card">
            <div class="bookings-table-scroll">
                @if ($activeStatus === 'pending')
                    @php
                        $isSpaceUser = $this->isSpaceUser();
                    @endphp
                    <table class="bookings-table booking-list-table{{ $isSpaceUser ? ' is-space' : '' }}">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>{{ $isSpaceUser ? 'Submitted at' : 'Submitted' }}</th>
                                <th>{{ $isSpaceUser ? 'Client' : 'Owner' }}</th>
                                <th>{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                                <th>{{ $isSpaceUser ? 'Service type' : 'Service Type' }}</th>
                                <th class="booking-details-col">Booking Details</th>
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
                                $visiblePendingBookings = $pendingBookings;
                            @endphp
                            @foreach ($visiblePendingBookings as $booking)
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

                                                $diffMinutes = max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                                                $hours = (int) floor($diffMinutes / 60);
                                                $minutes = (int) ($diffMinutes % 60);

                                                if ($minutes === 0) {
                                                    $durationLabel = $hours . 'hr';
                                                } else {
                                                    $durationLabel = $hours . 'hr ' . $minutes . 'm';
                                                }

                                                if ($isSpaceUser) {
                                                    $bookingDetailsTime = $startHHMM . ' - ' . $endHHMM . ' (' . $durationLabel . ')';
                                                } elseif ($startMeridiem === $endMeridiem) {
                                                    $bookingDetailsTime = $startHHMM . ' - ' . $endHHMM . ' ' . $startMeridiem . ' (' . $durationLabel . ')';
                                                } else {
                                                    $bookingDetailsTime = $startDt->format('H:i a') . ' - ' . $endDt->format('H:i a') . ' (' . $durationLabel . ')';
                                                }
                                            } catch (Throwable $e) {
                                                // Keep raw value on parse failure.
                                                $bookingDetailsTime = $bookingDetailsTimeRaw;
                                            }
                                        }
                                    }
                                    $bookingDetailsTimeDisplay = $bookingDetailsTime;
                                @endphp

                                <tr wire:key="booking-pending-row-{{ $booking->id }}" class="bookings-data-row"
                                    data-search="{{ $this->bookingSearchHaystack($booking) }}" @if ($loop->index >= 10) hidden @endif>
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="submitted-at">
                                            @if ($isSpaceUser)
                                                <div class="submitted-time">{{ $submittedTime }}</div>
                                                <div class="submitted-date">{{ $submittedDate }}</div>
                                            @else
                                                <div class="submitted-date">{{ $submittedDate }}</div>
                                                <div class="submitted-time">{{ $submittedTime }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                    <td @if ($isSpaceUser) class="space-visit-cell" @endif>
                                        @if ($isSpaceUser)
                                            {{ $this->formatSpaceVisitLabel($booking->visit_type ?? null) }}
                                        @else
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
                                        @endif
                                    </td>
                                    <td class="service-type">
                                        @if ($isSpaceUser)
                                            {{ $this->formatSpaceServiceLabel($booking->service, $booking->time) }}
                                        @else
                                            {!! $this->formatServiceTypeLabel($booking->service) !!}
                                        @endif
                                    </td>
                                    <td class="booking-details-col">
                                        <div class="booking-details">
                                            <div class="details-date">{{ $bookingDetailsDate }}</div>
                                            <div class="details-time">
                                                {{ $bookingDetailsTimeDisplay }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                                    <td class="action-col">
                                        <div class="booking-action-cell">
                                            <button type="button" class="booking-accept-btn"
                                                wire:click="acceptBooking({{ $booking->id }})" wire:loading.attr="disabled"
                                                wire:target="acceptBooking({{ $booking->id }})" aria-label="Accept booking">
                                                <span wire:loading.remove
                                                    wire:target="acceptBooking({{ $booking->id }})">Accept</span>
                                                <span class="booking-accept-loading" wire:loading.inline-flex
                                                    wire:target="acceptBooking({{ $booking->id }})">
                                                    <span class="booking-accept-spinner" aria-hidden="true"></span>
                                                </span>
                                            </button>
                                            <button type="button" class="booking-decline-btn"
                                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                                wire:click="openDeclineModal({{ $booking->id }})" aria-label="Decline booking">
                                                Decline
                                            </button>
                                            <x-business-hub.common.more-action-btn variant="pending" :row-id="$booking->id" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bookings-empty-row" wire:key="booking-row-{{ $activeStatus }}-empty" @if ($visiblePendingBookings->isNotEmpty()) hidden @endif>
                                <td colspan="8" class="empty-bookings">No pending bookings found.</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if ($activeStatus === 'confirmed')
                    @php
                        $isSpaceUser = $this->isSpaceUser();
                    @endphp
                    <table class="bookings-table confirmed-bookings-table{{ $isSpaceUser ? ' is-space' : '' }}">
                        <thead>
                            @if ($isSpaceUser)
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Client</th>
                                    <th>Service Type</th>
                                    <th>Location</th>
                                    <th>Booking Details</th>
                                    <th>Staff</th>
                                    <th class="confirmed-action-col">Action</th>
                                </tr>
                            @else
                                <tr>
                                    <th>Booking ID</th>
                                    <th class="booking-details-col">Appointment Details</th>
                                    <th>Pet</th>
                                    <th>Service Type</th>
                                    <th>Owner</th>
                                    <th>Location</th>
                                    <th>Staff</th>
                                    <th class="confirmed-action-col">Action</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody wire:key="bookings-table-confirmed" class="bookings-table-body">
                            @php
                                $confirmedBookings = $bookings->where('booking_status', 'confirmed')->values();
                                if ($pendingSort === 'oldest_submitted') {
                                    $confirmedBookings = $confirmedBookings->sortBy('created_at')->values();
                                } elseif ($pendingSort === 'amount_high') {
                                    $confirmedBookings = $confirmedBookings->sortByDesc(fn($b) => (float) $b->amount)->values();
                                } elseif ($pendingSort === 'amount_low') {
                                    $confirmedBookings = $confirmedBookings->sortBy(fn($b) => (float) $b->amount)->values();
                                } else {
                                    $confirmedBookings = $confirmedBookings->sortByDesc('created_at')->values();
                                }
                                $visibleConfirmedBookings = $confirmedBookings;
                            @endphp
                            @foreach ($visibleConfirmedBookings as $booking)
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
                                                $durationMinutes = (int) max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                                                $durationLabel = '(' . (int) floor($durationMinutes / 60) . 'hr' . ($durationMinutes % 60 ? ' ' . $durationMinutes % 60 . 'm' : '') . ')';
                                                if ($isSpaceUser) {
                                                    $appointmentTime = $startDt->format('H:i') . ' - ' . $endDt->format('H:i') . ' ' . $durationLabel;
                                                } else {
                                                    $appointmentTime = $startDt->format('H:i') . ' - ' . $endDt->format('H:i') . ' ' . strtolower($endDt->format('a')) . ' ' . $durationLabel;
                                                }
                                            } catch (Throwable $e) {
                                                $appointmentTime = $appointmentTimeRaw;
                                            }
                                        }
                                    }
                                    $appointmentTimeDisplay = $appointmentTime;

                                    if ($isSpaceUser) {
                                        $locationLabel = $this->formatSpaceVisitLabel($booking->visit_type ?? null);
                                    } else {
                                        $locationLabel = str_replace('_', ' ', strtolower((string) ($booking->visit_type ?? '')));
                                        $locationLabel = $locationLabel === 'home' || $locationLabel === 'home visit' ? 'Home Visit' : ($locationLabel === 'salon' || $locationLabel === 'salon visit' ? 'Salon Visit' : ucfirst($locationLabel ?: 'N/A'));
                                    }
                                @endphp
                                <tr wire:key="booking-row-confirmed-{{ $booking->id }}" class="bookings-data-row"
                                    data-search="{{ $this->bookingSearchHaystack($booking) }}" @if ($loop->index >= 10) hidden @endif>
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    @if ($isSpaceUser)
                                        <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                        <td class="service-type">{{ $this->formatSpaceServiceLabel($booking->service, $booking->time) }}</td>
                                        <td class="space-visit-cell">{{ $locationLabel }}</td>
                                        <td>
                                            <div class="confirmed-appointment-cell">
                                                <div>{{ $appointmentDate }}</div>
                                                <div>{{ $appointmentTimeDisplay }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @else
                                        <td class="booking-details-col">
                                            <div class="confirmed-appointment-cell">
                                                <div>{{ $appointmentDate }}</div>
                                                <div>{{ $appointmentTimeDisplay }}</div>
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
                                        <td class="service-type">{!! $this->formatServiceTypeLabel($booking->service) !!}</td>
                                        <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                        <td>{{ $locationLabel }}</td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @endif
                                    <td class="confirmed-action-col">
                                        <div class="confirmed-action-cell">
                                            <button type="button" class="confirmed-action-btn is-message" aria-label="Message">
                                                <img src="{{ asset('images/business-hub/icon-booking-message.svg') }}" alt=""
                                                    width="36" height="36">
                                            </button>
                                            <button type="button" class="confirmed-action-btn is-reschedule"
                                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                                wire:click="openRescheduleModal({{ $booking->id }})" aria-label="Reschedule">
                                                <img src="{{ asset('images/business-hub/icon-booking-reschedule.svg') }}" alt=""
                                                    width="36" height="36">
                                            </button>
                                            <x-business-hub.common.more-action-btn variant="confirmed" :row-id="$booking->id"
                                                :owner-id="$booking->pet_owner_id ?? $booking->petOwner?->id" />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bookings-empty-row" @if ($visibleConfirmedBookings->isNotEmpty()) hidden @endif>
                                <td colspan="{{ $isSpaceUser ? 7 : 8 }}" class="empty-bookings">No confirmed bookings
                                    found.</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if ($activeStatus === 'completed')
                    @php
                        $isSpaceUser = $this->isSpaceUser();
                    @endphp
                    <table class="bookings-table completed-bookings-table{{ $isSpaceUser ? ' is-space' : '' }}">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Date</th>
                                <th>{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                                <th>Service Type</th>
                                <th>Rating</th>
                                <th>Earnings</th>
                                <th class="completed-action-col">Actions</th>
                            </tr>
                        </thead>
                        <tbody wire:key="bookings-table-completed" class="bookings-table-body">
                            @php
                                $completedBookings = $bookings->where('booking_status', 'completed')->values();
                                if ($pendingSort === 'oldest_submitted') {
                                    $completedBookings = $completedBookings->sortBy('created_at')->values();
                                } elseif ($pendingSort === 'amount_high') {
                                    $completedBookings = $completedBookings->sortByDesc(fn($b) => (float) $b->amount)->values();
                                } elseif ($pendingSort === 'amount_low') {
                                    $completedBookings = $completedBookings->sortBy(fn($b) => (float) $b->amount)->values();
                                } else {
                                    $completedBookings = $completedBookings->sortByDesc('created_at')->values();
                                }
                                $visibleCompletedBookings = $completedBookings;
                            @endphp
                            @foreach ($visibleCompletedBookings as $booking)
                                @php
                                    $firstPet = $booking->pets->first();
                                    $petName = $firstPet?->name ?? 'N/A';
                                    $petType = $firstPet?->pet_type ?? null;
                                    $rating = data_get($booking, 'rating');
                                    $completedLocationLabel = strtolower((string) ($booking->visit_type ?? ''));
                                    $completedLocationLabel = str_replace('_', ' ', $completedLocationLabel);
                                    $completedLocationLabel = $completedLocationLabel === 'home' || $completedLocationLabel === 'home visit' ? 'Home Visit' : ($completedLocationLabel === 'salon' || $completedLocationLabel === 'salon visit' ? 'Salon Visit' : ucfirst($completedLocationLabel ?: 'N/A'));
                                @endphp
                                <tr wire:key="booking-row-completed-{{ $booking->id }}" class="bookings-data-row"
                                    data-search="{{ $this->bookingSearchHaystack($booking) }}" @if ($loop->index >= 10) hidden @endif>
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ optional($booking->date)->format('d/m/y') }}</td>
                                    <td>
                                        @if ($isSpaceUser)
                                            <span class="completed-space-label">{{ $completedLocationLabel }}</span>
                                        @else
                                            <div class="pet-name-wrap">
                                                <span class="pet-name">{{ $petName }}</span>
                                                @if ($petType)
                                                    <span class="pet-type">{{ $petType }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="service-type">{!! $this->formatServiceTypeLabel($booking->service) !!}</td>
                                    <td>
                                        <span class="completed-rating">
                                            <img src="{{ asset('images/business-hub/icon-booking-star.svg') }}" alt=""
                                                width="16" height="16">
                                            <span>{{ is_numeric($rating) ? number_format((float) $rating, 1) : '-' }}</span>
                                        </span>
                                    </td>
                                    <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                                    <td class="completed-action-col">
                                        <div class="completed-action-cell">
                                            <button type="button" class="completed-action-btn is-view"
                                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                                wire:click="openCompletedBookingModal({{ $booking->id }})"
                                                aria-label="View completed booking">
                                                <img src="{{ asset('images/business-hub/icon-booking-view.svg') }}" alt=""
                                                    width="36" height="36">
                                            </button>
                                            <button type="button" class="completed-action-btn is-download"
                                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                                wire:click="openCompletedBookingModal({{ $booking->id }})"
                                                aria-label="Download invoice">
                                                <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}"
                                                    alt="" width="36" height="36" class="completed-download-ring">
                                                <img src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}"
                                                    alt="" width="16" height="19" class="completed-download-arrow">
                                            </button>
                                            <x-business-hub.common.more-action-btn :row-id="$booking->id" message-only />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bookings-empty-row" @if ($visibleCompletedBookings->isNotEmpty()) hidden @endif>
                                <td colspan="7" class="empty-bookings">No completed bookings found.</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if ($activeStatus === 'cancelled')
                    @php
                        $isSpaceUser = $this->isSpaceUser();
                    @endphp
                    <table class="bookings-table cancelled-bookings-table{{ $isSpaceUser ? ' is-space' : '' }}">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Date</th>
                                <th>{{ $isSpaceUser ? 'Client' : 'Pet Owner' }}</th>
                                <th>{{ $isSpaceUser ? 'Service' : 'Pet' }}</th>
                                <th>Cancelled By</th>
                                <th>Refund Amount</th>
                                <th>Refund Status</th>
                                <th class="cancelled-action-col">Action</th>
                            </tr>
                        </thead>
                        <tbody wire:key="bookings-table-cancelled" class="bookings-table-body">
                            @php
                                $cancelledBookings = $bookings->where('booking_status', 'cancelled')->values();
                                if ($pendingSort === 'oldest_submitted') {
                                    $cancelledBookings = $cancelledBookings->sortBy('created_at')->values();
                                } elseif ($pendingSort === 'amount_high') {
                                    $cancelledBookings = $cancelledBookings->sortByDesc(fn($b) => (float) ($b->refund_amount ?? $b->amount))->values();
                                } elseif ($pendingSort === 'amount_low') {
                                    $cancelledBookings = $cancelledBookings->sortBy(fn($b) => (float) ($b->refund_amount ?? $b->amount))->values();
                                } else {
                                    $cancelledBookings = $cancelledBookings->sortByDesc('created_at')->values();
                                }
                                $visibleCancelledBookings = $cancelledBookings;
                            @endphp
                            @foreach ($visibleCancelledBookings as $booking)
                                @php
                                    $firstPet = $booking->pets->first();
                                    $petName = $firstPet?->name ?? 'N/A';
                                    $petType = $firstPet?->pet_type ?? null;
                                    $cancelledPetLabel = trim($petName . ($petType ? ' ' . $petType : ''));
                                    $cancelledByRaw = strtolower(trim((string) ($booking->cancelled_by ?? '')));
                                    $cancelledByYou = in_array($cancelledByRaw, ['groomer', 'space host', 'you', 'host'], true);
                                    $cancelledByLabel = $cancelledByYou ? 'You' : 'Client';
                                    $refundAmount = $booking->refund_amount !== null ? (float) $booking->refund_amount : (float) $booking->amount;
                                    $refundStatus = trim((string) ($booking->refund_status ?? 'In Progress')) ?: 'In Progress';
                                    $refundStatusKey = strtolower($refundStatus);
                                    $refundStatusClass = $refundStatusKey === 'rejected' ? 'rejected' : ($refundStatusKey === 'in progress' || $refundStatusKey === 'in-progress' ? 'in-progress' : 'processed');
                                    $refundStatusLabel = $refundStatusClass === 'rejected' ? 'Rejected' : ($refundStatusClass === 'in-progress' ? 'In Progress' : 'Processed');
                                    $cancelledLocationLabel = strtolower((string) ($booking->visit_type ?? ''));
                                    $cancelledLocationLabel = str_replace('_', ' ', $cancelledLocationLabel);
                                    $cancelledLocationLabel = $cancelledLocationLabel === 'home' || $cancelledLocationLabel === 'home visit' ? 'Home Visit' : ($cancelledLocationLabel === 'salon' || $cancelledLocationLabel === 'salon visit' ? 'Salon Visit' : ucfirst($cancelledLocationLabel ?: 'N/A'));
                                @endphp
                                <tr wire:key="booking-row-cancelled-{{ $booking->id }}" class="bookings-data-row"
                                    data-search="{{ $this->bookingSearchHaystack($booking) }}" @if ($loop->index >= 10) hidden @endif>
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ optional($booking->date)->format('d/m/y') }}</td>
                                    <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($isSpaceUser)
                                            {{ $this->formatSpaceServiceLabel($booking->service, $booking->time) }}
                                        @else
                                            {{ $cancelledPetLabel }}
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="cancelled-by-chip {{ $cancelledByYou ? 'you' : 'client' }}">{{ $cancelledByLabel }}</span>
                                    </td>
                                    <td>£{{ number_format($refundAmount, 2) }}</td>
                                    <td>
                                        <span
                                            class="refund-status-chip {{ $refundStatusClass }}">{{ $refundStatusLabel }}</span>
                                    </td>
                                    <td class="cancelled-action-col">
                                        <div class="completed-action-cell">
                                            <button type="button" class="completed-action-btn is-view"
                                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                                wire:click="openCancelledBookingModal({{ $booking->id }})"
                                                aria-label="View cancelled booking">
                                                <img src="{{ asset('images/business-hub/icon-booking-view.svg') }}" alt=""
                                                    width="36" height="36">
                                            </button>
                                            <button type="button" class="completed-action-btn is-download"
                                                data-invoice-url="{{ $this->bookingInvoicePdfUrl($booking) }}"
                                                onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
                                                aria-label="Download invoice">
                                                <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}"
                                                    alt="" width="36" height="36" class="completed-download-ring">
                                                <img src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}"
                                                    alt="" width="16" height="19" class="completed-download-arrow">
                                            </button>
                                            <x-business-hub.common.more-action-btn :row-id="$booking->id" message-only />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bookings-empty-row" @if ($visibleCancelledBookings->isNotEmpty()) hidden @endif>
                                <td colspan="8" class="empty-bookings">No cancelled bookings found.</td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                @if ($activeStatus === 'all')
                    @php
                        $isSpaceUser = $this->isSpaceUser();
                    @endphp
                    <table class="bookings-table bookings-table-all{{ $isSpaceUser ? ' is-space' : '' }}">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>{{ $isSpaceUser ? 'Client' : 'Owner' }}</th>
                                <th>{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                                <th>{{ $isSpaceUser ? 'Service' : 'Service Type' }}</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th class="view-col">View</th>
                            </tr>
                        </thead>
                        <tbody class="bookings-table-body" wire:ignore>
                            <template x-for="row in visibleRows" :key="row.id">
                                <tr>
                                    <td x-text="row.idLabel"></td>
                                    <td x-text="row.owner"></td>
                                    <td>
                                        <span class="space-visit-cell" x-show="isSpace" x-text="row.visitType"></span>
                                        <div class="pet-name-wrap" x-show="!isSpace">
                                            <span class="pet-name" x-text="row.petName"></span>
                                            <span class="pet-type" x-show="row.petType" x-text="row.petType"></span>
                                        </div>
                                    </td>
                                    <td class="service-type" :class="!isSpace && isGroomerService ? 'service-type-groomer' : ''"
                                        x-html="row.serviceHtml"></td>
                                    <td x-text="row.date"></td>
                                    <td>
                                        <span class="status-chip" :class="row.status" x-text="row.statusLabel"></span>
                                    </td>
                                    <td x-text="'£' + row.amount"></td>
                                    <td class="view-col">
                                        <div class="view-col-inner all-bookings-actions">
                                            <button type="button" class="view-btn" @click="openView(row.id)"
                                                aria-label="View booking">
                                                <img src="{{ asset('images/business-hub/icon-booking-view.svg') }}" alt=""
                                                    width="36" height="36">
                                            </button>
                                            @unless ($isSpaceUser)
                                                <x-business-hub.common.more-action-btn message-only />
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr class="bookings-empty-row" x-show="visibleRows.length === 0" x-cloak>
                                <td colspan="8" class="empty-bookings">No bookings found.</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        <div class="bookings-load-more-wrap" x-show="showLoadMore" x-cloak>
            <button type="button" class="bookings-load-more-btn" @click="loadMore">
                Load More
            </button>
        </div>
    </div>

    @php
        $loadModalBooking = function (?int $bookingId) {
            if (!$bookingId) {
                return null;
            }

            return Booking::with(['petOwner:id,name,profile_image', 'pets:id,name,pet_type,breed,sex,weight,notes,photo,address'])
                ->where('goormer_spacer_id', Auth::guard('groomer_spacer')->id() ?? Auth::id())
                ->where('id', $bookingId)
                ->first();
        };

        $completedBooking = $loadModalBooking($completedBookingId);
        $cancelledBooking = $loadModalBooking($cancelledBookingId);
        $declineBooking = $loadModalBooking($declineBookingId);
        $rescheduleBooking = $loadModalBooking($rescheduleBookingId);
    @endphp

    <livewire:business-hub.booking-details-drawer wire:key="booking-details-drawer" />

    <x-business-hub.common.completed-booking-modal :booking="$completedBooking"
        loading-event="bookings-tabs-loading-start" />

    @if ($cancelledBooking)
        @php
            $isSpaceUser = $this->isSpaceUser();
            $cancelledBookingIdLabel = 'FG-' . str_pad((string) $cancelledBooking->id, 5, '0', STR_PAD_LEFT);
            $cancelledDateLabel = optional($cancelledBooking->date)->format('d/m/Y') ?? 'N/A';
            $cancelledOwnerName = $cancelledBooking->petOwner?->name ?? 'N/A';
            $cancelledFirstPet = $cancelledBooking->pets->first();
            $cancelledPetName = $cancelledFirstPet?->name ?? 'N/A';
            $cancelledPetType = $cancelledFirstPet?->pet_type ?? '';
            $cancelledService = $cancelledBooking->service ?: 'N/A';
            $cancelledTimeRaw = (string) ($cancelledBooking->time ?? '');
            $cancelledTimeLabelForSpace = trim($cancelledTimeRaw) !== '' ? trim($cancelledTimeRaw) : 'N/A';
            if (str_contains($cancelledTimeRaw, '-')) {
                $parts = preg_split('/\s*-\s*/', $cancelledTimeRaw, 2);
                $start = $parts[0] ?? '';
                $end = $parts[1] ?? '';
                preg_match('/(\d{1,2}:\d{2})/', $start, $mStartOnly);
                preg_match('/(\d{1,2}:\d{2})/', $end, $mEndOnly);
                if (!empty($mStartOnly[1]) && !empty($mEndOnly[1])) {
                    try {
                        $startDt = new DateTimeImmutable($mStartOnly[1]);
                        $endDt = new DateTimeImmutable($mEndOnly[1]);
                        $cancelledTimeLabelForSpace = $startDt->format('H:i') . ' - ' . $endDt->format('H:i');
                    } catch (Throwable $e) {
                        $cancelledTimeLabelForSpace = trim((string) $mStartOnly[1]) . ' - ' . trim((string) $mEndOnly[1]);
                    }
                }
            }
            $cancelledServiceLower = strtolower(trim((string) $cancelledService));
            $cancelledDurationLabel = match (true) {
                (bool) preg_match('/full[\s_-]*day|fullday/', $cancelledServiceLower) => 'Full-day',
                (bool) preg_match('/half[\s_-]*day/', $cancelledServiceLower) => 'Half-day',
                str_contains($cancelledServiceLower, 'hour') => 'Hourly',
                default => null,
            };
            if ($cancelledDurationLabel === null && str_contains($cancelledTimeRaw, '-')) {
                $rangeParts = preg_split('/\s*-\s*/', $cancelledTimeRaw, 2);
                preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
                preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
                if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                    $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                    if ($diff < 0) {
                        $diff += 24 * 60;
                    }
                    $cancelledDurationLabel = $diff >= 7 * 60 ? 'Full-day' : ($diff >= 3 * 60 ? 'Half-day' : 'Hourly');
                }
            }
            $cancelledVisitRaw = str_replace('_', ' ', strtolower(trim((string) ($cancelledBooking->visit_type ?? ''))));
            $cancelledSpaceLabel = match (true) {
                $cancelledVisitRaw === '' => 'Garden/Shed',
                str_contains((string) ($cancelledBooking->visit_type ?? ''), '/') => str_replace([' / ', ' /', '/ '], '/', (string) $cancelledBooking->visit_type),
                $cancelledVisitRaw === 'garden shed' || $cancelledVisitRaw === 'garden/shed' => 'Garden/Shed',
                $cancelledVisitRaw === 'home' || $cancelledVisitRaw === 'home visit' => 'Home Visit',
                $cancelledVisitRaw === 'salon' || $cancelledVisitRaw === 'salon visit' => 'Salon Visit',
                default => ucwords($cancelledVisitRaw),
            };
            $cancelledTimeCompact = str_replace(' - ', '-', $cancelledTimeLabelForSpace);
            $cancelledServiceLine = $isSpaceUser ? $cancelledSpaceLabel . ' (' . trim(($cancelledDurationLabel ? $cancelledDurationLabel . ' - ' : '') . $cancelledTimeCompact) . ')' : $cancelledService . ($cancelledPetName !== 'N/A' ? ' (' . $cancelledPetName . ')' : '');
            $cancelledPetLabel = trim($cancelledPetName . ($cancelledPetType !== '' ? ' ' . $cancelledPetType : ''));
            $cancelledOwnerPhotoRaw = trim((string) ($cancelledBooking->petOwner?->profile_image ?? ''));
            $cancelledOwnerPhotoUrl = $cancelledOwnerPhotoRaw === '' ? null : (str_starts_with($cancelledOwnerPhotoRaw, 'http://') || str_starts_with($cancelledOwnerPhotoRaw, 'https://') || str_starts_with($cancelledOwnerPhotoRaw, 'data:') || str_starts_with($cancelledOwnerPhotoRaw, '/') ? $cancelledOwnerPhotoRaw : asset('storage/' . ltrim($cancelledOwnerPhotoRaw, '/')));
            $cancelledOwnerInitial = strtoupper(substr((string) $cancelledOwnerName, 0, 1)) ?: 'U';
            $cancelledServiceAmount = (float) $cancelledBooking->amount;
            $cancelledExtraAddOnsRaw = $cancelledBooking->extra_add_ons;
            $cancelledExtraAddOns = collect(is_array($cancelledExtraAddOnsRaw) ? $cancelledExtraAddOnsRaw : [])
                ->map(function ($item) {
                    return [
                        'label' => trim((string) data_get($item, 'label', '')),
                        'amount' => (float) data_get($item, 'amount', 0),
                    ];
                })
                ->filter(fn($item) => $item['label'] !== '')
                ->values();
            $cancelledExtrasAmount = (float) $cancelledExtraAddOns->sum(fn($item) => $item['amount']);
            $cancelledPromoDiscount = (float) ($cancelledBooking->discount ?? 0);
            $cancelledTotalAmount = $cancelledServiceAmount + $cancelledExtrasAmount - $cancelledPromoDiscount;
            $cancelledRefundStatusRaw = trim((string) ($cancelledBooking->refund_status ?? 'In Progress')) ?: 'In Progress';
            $cancelledRefundStatusKey = strtolower($cancelledRefundStatusRaw);
            $cancelledRefundStatusClass = $cancelledRefundStatusKey === 'rejected' ? 'rejected' : ($cancelledRefundStatusKey === 'in progress' || $cancelledRefundStatusKey === 'in-progress' ? 'in-progress' : 'processed');
            $cancelledRefundStatus = $cancelledRefundStatusClass === 'rejected' ? 'Rejected' : ($cancelledRefundStatusClass === 'in-progress' ? 'In Progress' : 'Processed');
        @endphp
        @teleport('body')
            <div class="completed-booking-modal-overlay cancelled-booking-modal-overlay"
                wire:keydown.escape="closeCancelledBookingModal">
                <div class="cancelled-booking-modal-card" role="dialog" aria-modal="true"
                    aria-labelledby="cancelled-booking-modal-title">
                    <div class="cancelled-booking-modal-head">
                        <h3 class="cancelled-booking-modal-title" id="cancelled-booking-modal-title">
                            <span>Cancelled</span> <span class="is-light">Booking</span>
                        </h3>
                        <button type="button" class="cancelled-booking-modal-close"
                            @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                            wire:click="closeCancelledBookingModal" aria-label="Close modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none"
                                aria-hidden="true">
                                <path d="M1 12L12 1M1 1L12 12" stroke="#3B3731" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <div class="cancelled-booking-modal-id-row">
                        <div class="cancelled-booking-modal-id-left">
                            <span class="cancelled-booking-modal-id">Booking ID: {{ $cancelledBookingIdLabel }}</span>
                            <span
                                class="refund-status-chip {{ $cancelledRefundStatusClass }}">{{ $cancelledRefundStatus }}</span>
                        </div>
                        <div class="cancelled-booking-modal-id-right">
                            <span class="cancelled-booking-modal-date">{{ $cancelledDateLabel }}</span>
                            <button type="button" data-invoice-url="{{ $this->bookingInvoicePdfUrl($cancelledBooking) }}"
                                onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
                                class="completed-action-btn is-download" aria-label="Download invoice">
                                <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}" alt=""
                                    width="36" height="36" class="completed-download-ring">
                                <img src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}" alt=""
                                    width="16" height="19" class="completed-download-arrow">
                            </button>
                        </div>
                    </div>

                    <div class="cancelled-booking-modal-customer">
                        <span class="cancelled-booking-modal-avatar">
                            @if ($cancelledOwnerPhotoUrl)
                                <img src="{{ $cancelledOwnerPhotoUrl }}" alt="" width="44" height="44">
                            @else
                                <span>{{ $cancelledOwnerInitial }}</span>
                            @endif
                        </span>
                        <div>
                            <p class="cancelled-booking-modal-owner">{{ $cancelledOwnerName }}</p>
                            @unless ($isSpaceUser)
                                <p class="cancelled-booking-modal-pet">
                                    {{ $cancelledPetName }}@if ($cancelledPetType !== '')
                                        <span>{{ $cancelledPetType }}</span>
                                    @endif
                                </p>
                            @endunless
                        </div>
                    </div>

                    <div class="cancelled-booking-modal-section">
                        <p class="cancelled-booking-modal-section-title">{{ $isSpaceUser ? 'Space' : 'Service' }}</p>
                        <div class="cancelled-booking-modal-line">
                            <span>{{ $cancelledServiceLine }}</span>
                            <span>£{{ number_format($cancelledServiceAmount, 2) }}</span>
                        </div>
                    </div>

                    <div class="cancelled-booking-modal-section">
                        <p class="cancelled-booking-modal-section-title">Extras &amp; Add-ons</p>
                        @forelse ($cancelledExtraAddOns as $addon)
                            <div class="cancelled-booking-modal-line">
                                <span>{{ $addon['label'] }}</span>
                                <span>£{{ number_format((float) $addon['amount'], 2) }}</span>
                            </div>
                        @empty
                            <div class="cancelled-booking-modal-line">
                                <span>No add-ons recorded</span>
                                <span>£{{ number_format($cancelledExtrasAmount, 2) }}</span>
                            </div>
                        @endforelse
                    </div>

                    <div class="cancelled-booking-modal-totals">
                        <div class="cancelled-booking-modal-total-row">
                            <span>Service:</span>
                            <span class="cancelled-modal-strike">£{{ number_format($cancelledServiceAmount, 2) }}</span>
                        </div>
                        <div class="cancelled-booking-modal-total-row">
                            <span>Extras &amp; Add-ons:</span>
                            <span class="cancelled-modal-strike">£{{ number_format($cancelledExtrasAmount, 2) }}</span>
                        </div>
                        <div class="cancelled-booking-modal-total-row">
                            <span>Promo discount:</span>
                            <span class="cancelled-modal-strike">- £{{ number_format($cancelledPromoDiscount, 2) }}</span>
                        </div>
                    </div>
                    <div class="cancelled-booking-modal-grand">
                        <span>Total</span>
                        <span class="cancelled-modal-strike">£{{ number_format($cancelledTotalAmount, 2) }}</span>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    <x-business-hub.common.decline-modal :decline-booking="$declineBooking" />
    <x-business-hub.common.reschedule-modal :reschedule-booking="$rescheduleBooking"
        :bookings="$rescheduleCalendarBookings" :reschedule-selected-date="$rescheduleSelectedDate"
        :reschedule-selected-time="$rescheduleSelectedTime" :reschedule-calendar-month="$rescheduleCalendarMonth"
        :reschedule-duration-minutes="$rescheduleDurationMinutes" />
</section>

<script>
    if (!window.downloadBookingInvoicePdf) {
        window.downloadBookingInvoicePdf = async function(invoiceUrl) {
            if (!invoiceUrl) {
                return;
            }
            window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'));
            try {
                const res = await fetch(invoiceUrl, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/pdf',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const ct = (res.headers.get('Content-Type') || '').toLowerCase();
                if (!res.ok || (!ct.includes('application/pdf') && !ct.includes('octet-stream'))) {
                    throw new Error('Invoice download failed');
                }
                let filename = 'Fursgo-Invoice.pdf';
                const cd = res.headers.get('Content-Disposition');
                if (cd) {
                    const utf = cd.match(/filename\*=(?:UTF-8'')?([^;\n]+)/i);
                    const quoted = cd.match(/filename="([^"]+)"/i);
                    const plain = cd.match(/filename=([^;\s]+)/i);
                    if (utf && utf[1]) {
                        try {
                            filename = decodeURIComponent(utf[1].trim().replace(/^"+|"+$/g, ''));
                        } catch (e) {
                            filename = utf[1].trim();
                        }
                    } else if (quoted && quoted[1]) {
                        filename = quoted[1];
                    } else if (plain && plain[1]) {
                        filename = plain[1].replace(/^"+|"+$/g, '');
                    }
                }
                const blob = await res.blob();
                const objectUrl = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = objectUrl;
                a.download = filename;
                a.rel = 'noopener';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(objectUrl);
            } catch (e) {
                console.error(e);
                window.alert('Could not download the invoice. Please try again.');
            } finally {
                window.dispatchEvent(new CustomEvent('bookings-tabs-loading-end'));
            }
        };
    }

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
                get newAppointmentLabel() {
                    const parsed = parseYmd(this.selectedDate);
                    if (!parsed || !this.selectedTime) {
                        return '';
                    }
                    return `${parsed.d} ${monthNames[parsed.m - 1]} ${parsed.y} · ${this.selectedTime}`;
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

        window.addEventListener('completed-booking-modal-opened', () => {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });

        window.addEventListener('completed-booking-modal-closed', () => {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });

        window.addEventListener('cancelled-booking-modal-opened', () => {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });

        window.addEventListener('cancelled-booking-modal-closed', () => {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });

        const lockBookingDetailsPage = () => {
            if (document.body.classList.contains('booking-details-drawer-open')) {
                return;
            }

            const scrollY = window.scrollY || window.pageYOffset || 0;
            document.body.dataset.bookingDetailsScrollY = String(scrollY);
            document.body.classList.add('booking-details-drawer-open');
            document.documentElement.classList.add('booking-details-drawer-open');
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        };

        const unlockBookingDetailsPage = () => {
            if (!document.body.classList.contains('booking-details-drawer-open')) {
                return;
            }

            const scrollY = parseInt(document.body.dataset.bookingDetailsScrollY || '0', 10);
            document.body.classList.remove('booking-details-drawer-open');
            document.documentElement.classList.remove('booking-details-drawer-open');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            delete document.body.dataset.bookingDetailsScrollY;
            window.scrollTo(0, scrollY);
        };

        window.__lockBookingDetailsDrawer = lockBookingDetailsPage;
        window.__unlockBookingDetailsDrawer = unlockBookingDetailsPage;

        window.addEventListener('booking-details-drawer-opened', lockBookingDetailsPage);
        window.addEventListener('booking-details-drawer-closed', unlockBookingDetailsPage);

        document.addEventListener('livewire:init', () => {
            Livewire.on('booking-details-drawer-opened', lockBookingDetailsPage);
            Livewire.on('booking-details-drawer-closed', unlockBookingDetailsPage);
        });
    }
</script>

<style>
    html.booking-details-drawer-open,
    body.booking-details-drawer-open {
        overflow: hidden !important;
        overscroll-behavior: none;
        touch-action: none;
    }

    [x-cloak] {
        display: none !important;
    }

    :root {
        --booking-modal-divider-color: #DCDCDC;
        --booking-modal-divider-height: 1px;
    }

    .completed-booking-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        z-index: 100100;
    }

    .completed-booking-modal-card {
        width: min(610px, 100%);
        border-radius: 10px;
        border: 1px solid #CBDCE8;
        background: #F8F8F8;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .completed-booking-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: 10px 10px 0 0;
        border-bottom: 1px solid #CBDCE8;
        background: rgba(203, 220, 232, 0.20);
        padding: 1.2rem 1.65rem;
    }

    .completed-booking-modal-title {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .completed-booking-modal-close {
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }

    .completed-booking-modal-booking-row {
        padding: 1.2rem 1.65rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        position: relative;
    }

    .completed-booking-modal-booking-row strong {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .completed-booking-modal-booking-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .completed-booking-download-btn {
        border: 0;
        background: transparent;
        color: inherit;
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
    }

    .completed-booking-modal-customer {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 1.2rem 1.65rem;
        position: relative;
    }

    .completed-booking-modal-user-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .completed-booking-modal-owner {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .completed-booking-modal-pet {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .completed-booking-modal-space-service {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .completed-booking-modal-pet-type {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-left: 0.35rem;
    }

    .completed-booking-modal-section {
        padding: 1.2rem 1.65rem;
        position: relative;
    }

    .completed-booking-modal-booking-row::after,
    .completed-booking-modal-section::after {
        content: '';
        position: absolute;
        left: 1.65rem;
        right: 1.65rem;
        bottom: 0;
        height: var(--booking-modal-divider-height);
        background: var(--booking-modal-divider-color);
    }

    .completed-booking-modal-section-label {
        margin: 0 0 0.5rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .completed-booking-modal-section-title {
        margin: 0 0 0.65rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .completed-booking-modal-line {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .completed-booking-modal-line p {
        margin: 0;
    }

    .completed-booking-modal-line-sub {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
    }

    .completed-booking-modal-total-block {
        padding: 1.35rem 1.65rem 1.55rem;
    }

    .completed-booking-modal-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
        margin-bottom: 1.7rem;
    }

    .completed-booking-modal-total-row>span:last-child {
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
        /* 127.778% */
    }

    .completed-booking-modal-total-row.is-grand {
        border-top: var(--booking-modal-divider-height) solid var(--booking-modal-divider-color);
        padding-top: 1rem;
        margin-top: 0.8rem;
        margin-bottom: 0;
    }

    .completed-booking-modal-total-row.is-grand>span {
        color: #3B3731 !important;
        font-family: Lato !important;
        font-size: 18px !important;
        font-style: normal !important;
        font-weight: 700 !important;
        line-height: normal !important;
    }

    .completed-booking-addon-line {
        margin-bottom: 0.35rem;
    }

    .completed-booking-addon-line:last-child {
        margin-bottom: 0;
    }

    .cancelled-booking-modal-card {
        width: 610px;
        max-width: 100%;
        max-height: calc(100vh - 2rem);
        overflow: auto;
        flex: 0 0 auto;
        border-radius: 10px;
        background: #FFF;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
    }

    .cancelled-booking-modal-head {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 80px;
        padding: 0 50px;
        background: #FFE2E2;
        border-radius: 10px 10px 0 0;
    }

    .cancelled-booking-modal-title {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
        text-align: center;
    }

    .cancelled-booking-modal-title .is-light {
        font-weight: 600;
    }

    .cancelled-booking-modal-close {
        position: absolute;
        top: 50%;
        right: 30px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 13px;
        height: 13px;
        padding: 0;
        cursor: pointer;
        line-height: 1;
    }

    .cancelled-booking-modal-close svg {
        display: block;
        width: 13px;
        height: 13px;
    }

    .cancelled-booking-modal-id-row,
    .cancelled-booking-modal-customer,
    .cancelled-booking-modal-section,
    .cancelled-booking-modal-totals,
    .cancelled-booking-modal-grand {
        margin: 0 25px;
        width: calc(100% - 50px);
        box-sizing: border-box;
    }

    .cancelled-booking-modal-id-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 36px;
        margin-top: 20px;
        padding-bottom: 20px;
        border-bottom: var(--booking-modal-divider-height) solid var(--booking-modal-divider-color);
    }

    .cancelled-booking-modal-id-left,
    .cancelled-booking-modal-id-right {
        display: inline-flex;
        align-items: center;
        gap: 20px;
        min-width: 0;
    }

    .cancelled-booking-modal-id {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
    }

    .cancelled-booking-modal-date {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
    }

    .cancelled-booking-modal-customer {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px 0;
    }

    .cancelled-booking-modal-avatar {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        overflow: hidden;
        border-radius: 144px;
        background: #F6F5F5;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .cancelled-booking-modal-avatar img {
        display: block;
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 144px;
    }

    .cancelled-booking-modal-owner {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .cancelled-booking-modal-pet {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .cancelled-booking-modal-pet span {
        color: #9D9B98;
    }

    .cancelled-booking-modal-section {
        padding: 20px 0;
        border-bottom: var(--booking-modal-divider-height) solid var(--booking-modal-divider-color);
    }

    .cancelled-booking-modal-section-title {
        margin: 0 0 20px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .cancelled-booking-modal-line {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
    }

    .cancelled-booking-modal-line+.cancelled-booking-modal-line {
        margin-top: 10px;
    }

    .cancelled-booking-modal-line>span:first-child {
        color: #9D9B98;
        min-width: 0;
    }

    .cancelled-booking-modal-line>span:last-child {
        color: #3B3731;
        text-align: right;
        flex-shrink: 0;
    }

    .cancelled-booking-modal-totals {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 20px 0;
        border-bottom: var(--booking-modal-divider-height) solid var(--booking-modal-divider-color);
    }

    .cancelled-booking-modal-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
    }

    .cancelled-booking-modal-total-row>span:first-child {
        color: #9D9B98;
    }

    .cancelled-booking-modal-total-row>span:last-child {
        color: #3B3731;
        text-align: right;
    }

    .cancelled-booking-modal-grand {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 40px 0 38px;
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .cancelled-modal-strike {
        text-decoration: line-through;
        text-decoration-thickness: 1px;
        text-underline-position: from-font;
        text-decoration-skip-ink: none;
    }

    .bookings-board {
        width: 100%;
        padding: 0.35rem 0 20px;
        box-sizing: border-box;
    }

    .bookings-board-header {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 2.5rem;
        margin-bottom: 2.5rem;
        position: relative;
        z-index: 40;
    }

    .bookings-search {
        position: relative;
        display: flex;
        align-items: center;
        width: 400px;
        max-width: 100%;
        height: 42px;
        margin-top: 2rem;
    }

    .bookings-search input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #FFC97A;
        background: #FFF;
        padding: 0 42px 0 10px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        outline: none;
    }

    .bookings-search input::placeholder {
        color: #D4D4D4;
    }

    .bookings-search input::-webkit-search-cancel-button {
        display: none;
    }

    .bookings-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .bookings-search-icon img {
        display: block;
        width: 16px;
        height: 16px;
    }

    .booking-pill-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-pill {
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        border-radius: 100px;
        min-height: 42px;
        padding: 0.6rem 1.15rem;
        border: none;
        cursor: pointer;
        text-align: center;
        background: #F6F5F5;
        color: #9D9B98;
    }

    .booking-pill.is-active {
        background: #3B3731;
        color: #F7F7F7;
    }

    .booking-pill.is-pending.is-active {
        background: #FFFAF2;
        color: #FEB95C;
    }

    .booking-pill.is-confirmed.is-active {
        background: #F7FAF1;
        color: #AFCD73;
    }

    .booking-pill.is-completed.is-active {
        background: #F6FAFD;
        color: #A4C9E4;
    }

    .booking-pill.is-cancelled.is-active {
        background: #FEE2E2;
        color: #FD6D70;
    }

    .section-container:has(.section-panel.section-active .bookings-board) {
        overflow: visible;
    }

    .bookings-table-wrap {
        width: 100%;
        overflow: visible;
    }

    .bookings-table-card {
        width: 100%;
        overflow: visible;
        background: #FDFDFD;
        border: 1px solid #F6F5F5;
        border-radius: 10px;
        box-shadow: 0px 0px 15px 2px rgba(59, 55, 49, 0.1);
    }

    .bookings-table-scroll {
        width: 100%;
        overflow: visible;
        border-radius: 10px;
    }

    .bookings-board [x-cloak] {
        display: none !important;
    }

    .bookings-table-body tr[hidden] {
        display: none !important;
    }

    .bookings-table {
        width: 100%;
        max-width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        min-width: 0;
    }

    .bookings-load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 2.5rem;
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
        border-bottom: 1px solid #F6F5F5;
        padding: 16px 8px;
        text-align: left;
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
        vertical-align: middle !important;
    }

    .bookings-table thead th:first-child,
    .bookings-table tbody td:first-child {
        padding-left: 16px;
    }

    .bookings-table thead th:last-child,
    .bookings-table tbody td:last-child {
        padding-right: 16px;
    }

    .bookings-table thead {
        border-radius: 10px 10px 0 0;
        background: #F6F5F5;
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .bookings-table th {
        background: #F6F5F5;
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        height: 50px;
        padding-top: 0;
        padding-bottom: 0;
    }

    .bookings-table thead th:first-child {
        border-top-left-radius: 10px;
    }

    .bookings-table thead th:last-child {
        border-top-right-radius: 10px;
    }

    .bookings-table td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .bookings-table tbody tr:last-child td {
        border-bottom: none;
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
        gap: 0.4rem;
    }

    .pet-name {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
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
        border-radius: 100px;
        min-height: 25px;
        padding: 0.18rem 0.7rem;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .status-chip.pending {
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.20);
    }

    .status-chip.confirmed {
        color: #AFCD6F;
        background: rgba(201, 221, 160, 0.20);
    }

    .status-chip.completed {
        color: #9FC7E4;
        background: rgba(203, 220, 232, 0.20);
    }

    .status-chip.cancelled {
        color: #FF6E6E;
        background: rgba(255, 110, 110, 0.20);
    }

    .bookings-table .view-col {
        vertical-align: middle;
        width: 100px;
        white-space: nowrap;
        padding-left: 8px;
        padding-right: 12px;
    }

    .bookings-table-all th.view-col {
        text-align: center;
    }

    .bookings-table-all .view-col-inner {
        justify-content: center;
    }

    .bookings-table .action-col,
    .bookings-table .confirmed-action-col,
    .bookings-table .completed-action-col,
    .bookings-table .cancelled-action-col {
        width: 176px;
        white-space: nowrap;
        padding-left: 8px;
        padding-right: 12px;
        vertical-align: middle;
    }

    .bookings-table td.action-col,
    .bookings-table td.confirmed-action-col,
    .bookings-table td.completed-action-col,
    .bookings-table td.cancelled-action-col {
        vertical-align: middle;
    }

    .booking-list-table .action-col {
        width: 230px;
        padding-right: 16px;
    }

    .booking-list-table th.action-col,
    .confirmed-bookings-table th.confirmed-action-col,
    .completed-bookings-table th.completed-action-col,
    .cancelled-bookings-table th.cancelled-action-col {
        text-align: center;
    }

    .booking-list-table {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .booking-list-table th {
        color: #948F88;
        font-weight: 600;
    }

    .booking-list-table td {
        color: #3B3731;
        font-weight: 400;
    }

    .booking-list-table .service-type,
    .booking-list-table .service-type-groomer {
        color: #3B3731 !important;
        font-weight: 400 !important;
    }

    .booking-list-table .booking-pet-name {
        color: #3B3731;
        font-weight: 400;
    }

    .booking-list-table .booking-pet-type,
    .booking-list-table .booking-pet-more,
    .booking-list-table .details-time {
        color: #9D9B98;
        font-weight: 400;
    }

    .booking-list-table .submitted-date,
    .booking-list-table .details-date {
        color: #3B3731;
    }

    .booking-list-table .submitted-time {
        color: #948F88;
    }

    .service-type {
        color: #3B3731 !important;
        font-family: Lato !important;
        font-size: 16px !important;
        font-style: normal !important;
        font-weight: 600 !important;
        line-height: normal !important;
        white-space: normal !important;
        vertical-align: middle !important;
    }

    .service-type-groomer {
        font-weight: 600 !important;
    }

    .invoice-col,
    .more-col {
        vertical-align: middle;
        padding: 0 !important;
    }

    .view-col-inner {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-align: center;
        min-height: 36px;
    }

    .view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0;
        cursor: pointer;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        line-height: 0;
    }

    .view-btn img,
    .view-btn svg {
        display: block;
        width: 36px;
        height: 36px;
        flex-shrink: 0;
    }

    .all-bookings-actions {
        width: 82px;
        min-height: 36px;
        justify-content: center;
        gap: 10px;
    }

    .bookings-table.is-space thead th {
        white-space: nowrap;
        overflow-wrap: normal;
        word-break: normal;
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .booking-list-table.is-space .submitted-time {
        color: #3B3731;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .booking-list-table.is-space .submitted-date {
        color: #9D9B98;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .booking-list-table.is-space .service-type {
        font-weight: 400 !important;
        white-space: nowrap !important;
    }

    .booking-list-table.is-space .space-visit-cell {
        white-space: nowrap;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .booking-list-table.is-space .details-time {
        color: #9D9B98;
        font-size: 16px;
        font-weight: 400;
        white-space: nowrap;
    }

    .bookings-table-all.is-space .space-visit-cell {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .bookings-table-all.is-space .service-type {
        font-weight: 400 !important;
        white-space: nowrap !important;
    }

    .bookings-table-all.is-space .view-col {
        width: 64px;
        padding-left: 8px;
        padding-right: 8px;
        text-align: center;
    }

    .bookings-table-all.is-space .all-bookings-actions {
        width: 100%;
        justify-content: center;
        gap: 0;
    }

    a.view-btn {
        color: inherit;
        text-decoration: none;
    }

    button.view-btn {
        color: inherit;
    }

    a.completed-booking-download-btn {
        color: inherit;
        text-decoration: none;
    }

    .completed-rating {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: normal;
    }

    .completed-rating img {
        display: block;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .cancelled-bookings-table .bookings-empty-row td.empty-bookings {
        height: 150px;
        white-space: normal;
        text-align: center;
        color: #948F88 !important;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .cancelled-by-chip,
    .refund-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        border-radius: 100px;
        padding: 0 10px;
        box-sizing: border-box;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        white-space: nowrap;
    }

    .cancelled-by-chip {
        min-width: 55px;
        padding: 0;
    }

    .cancelled-by-chip.client {
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.20);
    }

    .cancelled-by-chip.you {
        color: #3B3731;
        background: rgba(157, 155, 152, 0.20);
    }

    .refund-status-chip.rejected {
        min-width: 83px;
        color: #FF6E6E;
        background: #FFE2E2;
    }

    .refund-status-chip.in-progress {
        min-width: 89px;
        color: #9FC7E4;
        background: rgba(203, 220, 232, 0.20);
    }

    .refund-status-chip.processed {
        min-width: 83px;
        color: #AFCD6F;
        background: rgba(186, 207, 142, 0.20);
    }

    .cancelled-bookings-table {
        table-layout: auto;
    }

    .cancelled-bookings-table th,
    .cancelled-bookings-table td {
        white-space: nowrap;
        overflow-wrap: normal;
        word-break: normal;
    }

    .cancelled-bookings-table tbody tr:not(.bookings-empty-row) td {
        height: 78px;
        padding-top: 0;
        padding-bottom: 0;
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
        margin: 0;
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
        gap: 0;
    }

    .submitted-date {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .submitted-time {
        color: #948F88;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .booking-list-table .filtered-pet-cell {
        gap: 0;
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
        white-space: nowrap;
        overflow-wrap: normal;
        word-break: normal;
    }

    .confirmed-appointment-time-space {
        color: #9D9B98 !important;
    }

    .confirmed-space-label {
        font-weight: 600;
    }

    .confirmed-bookings-table.is-space td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .confirmed-bookings-table.is-space .service-type {
        font-weight: 600 !important;
        white-space: nowrap !important;
    }

    .confirmed-bookings-table.is-space .space-visit-cell {
        white-space: nowrap;
        font-weight: 400;
    }

    .confirmed-bookings-table.is-space .confirmed-appointment-cell div:last-child {
        color: #3B3731;
        white-space: nowrap;
    }

    .confirmed-action-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        width: 100%;
        min-height: 36px;
    }

    .confirmed-action-btn {
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        background: transparent;
        width: 36px;
        height: 36px;
        flex-shrink: 0;
    }

    .confirmed-action-btn img,
    .confirmed-action-btn svg {
        display: block;
        width: 36px;
        height: 36px;
    }

    .completed-action-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 36px;
    }

    .completed-action-btn {
        position: relative;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        background: transparent;
        width: 36px;
        height: 36px;
        flex-shrink: 0;
    }

    .completed-action-btn img.completed-download-ring,
    .completed-action-btn.is-view img {
        display: block;
        width: 36px;
        height: 36px;
    }

    .completed-action-btn .completed-download-arrow {
        position: absolute;
        display: block;
        width: 16px;
        height: 19px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .completed-bookings-table td:nth-child(3),
    .completed-bookings-table .pet-name-wrap,
    .completed-bookings-table .pet-name,
    .completed-bookings-table .pet-type {
        flex-wrap: nowrap;
        white-space: nowrap;
        overflow-wrap: normal;
        word-break: normal;
    }

    .booking-action-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        overflow: visible;
        width: 100%;
        min-height: 36px;
    }

    .booking-action-cell.is-open {
        z-index: 10001;
    }

    .booking-accept-btn {
        -webkit-appearance: none;
        appearance: none;
        border-radius: 100px;
        background-color: #C9DDA0;
        background-image: none;
        border: 1px solid #AFCD6F;
        box-sizing: border-box;
        width: 75.939px;
        height: 36px;
        color: #FFFFFF;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        -webkit-font-smoothing: antialiased;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        flex-shrink: 0;
        padding: 0;
        box-shadow: none;
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
        -webkit-appearance: none;
        appearance: none;
        width: 75.939px;
        height: 36px;
        border-radius: 100px;
        border: 1px solid #FF6E6E;
        background-color: #FFFFFF;
        background-image: none;
        color: #FF6E6E;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        -webkit-font-smoothing: antialiased;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        flex-shrink: 0;
        padding: 0;
        box-shadow: none;
    }

    .bookings-table .booking-details-col {
        width: 18%;
        white-space: normal;
    }

    .booking-details {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        min-width: 0;
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
        white-space: nowrap;
        overflow-wrap: normal;
        word-break: normal;
    }

    .details-time-space {
        color: #9D9B98;
    }

    .completed-space-label {
        font-weight: 600;
    }
</style>
