<?php

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Support\BookingDeclineReasons;
use App\Support\BusinessHubAvatar;
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
    public string $pendingSort = 'soonest_first';
    public array $activeFilters = [];
    public bool $autoAcceptBooking = true;
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
    private array $allowedSorts = ['soonest_first', 'amount_high', 'amount_low', 'client_az'];

    private function spacerId(): int
    {
        return (int) (Auth::guard('groomer_spacer')->id() ?? (Auth::id() ?? 0));
    }

    private function profile(): ?GroomerSpacerProfile
    {
        $profile = Auth::guard('groomer_spacer')->user();

        if ($profile instanceof GroomerSpacerProfile) {
            return $profile;
        }

        $user = Auth::user();
        $email = (string) ($user->email ?? '');

        return $email !== '' ? GroomerSpacerProfile::whereEmail($email)->first() : null;
    }

    private function scopedBookingQuery(int $bookingId)
    {
        return Booking::query()->where(self::SPACER_ID_COLUMN, $this->spacerId())->where(self::BOOKING_ID_COLUMN, $bookingId);
    }

    private function applyBookingSort($query): void
    {
        // On All Bookings, keep pending requests at the top before the chosen sort.
        if ($this->activeStatus === 'all') {
            $query->orderByRaw('CASE WHEN ' . self::BOOKING_STATUS_COLUMN . " = 'pending' THEN 0 ELSE 1 END");
        }

        if ($this->pendingSort === 'amount_high') {
            $query->orderByDesc('amount')->orderBy('date')->orderBy('time')->orderByDesc(self::BOOKING_ID_COLUMN);

            return;
        }

        if ($this->pendingSort === 'amount_low') {
            $query->orderBy('amount')->orderBy('date')->orderBy('time')->orderByDesc(self::BOOKING_ID_COLUMN);

            return;
        }

        if ($this->pendingSort === 'client_az') {
            $query->orderBy('date')->orderBy('time')->orderBy(self::BOOKING_ID_COLUMN);

            return;
        }

        $query->orderBy('date')->orderBy('time')->orderBy(self::BOOKING_ID_COLUMN);
    }

    public function bookingSearchHaystack($booking): string
    {
        $owner = strtolower((string) ($booking->petOwner?->name ?? ''));
        $pets = strtolower($booking->pets->pluck('name')->filter()->implode(' '));
        $service = strtolower((string) ($booking->service ?? ''));
        $id = (string) $booking->id;
        $padded = 'fg-' . str_pad($id, 5, '0', STR_PAD_LEFT);

        return trim($padded . ' ' . $id . ' ' . $owner . ' ' . $pets . ' ' . $service);
    }

    private function ownerShortName($owner): string
    {
        $name = trim((string) ($owner?->name ?? 'N/A'));
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parts) >= 2) {
            return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.';
        }

        return $name !== '' ? $name : 'N/A';
    }

    private function clientTierLabel(int $previousCompleted): array
    {
        if ($previousCompleted <= 0) {
            return ['label' => 'New Client', 'tone' => 'new'];
        }
        if ($previousCompleted === 1) {
            return ['label' => 'Repeat Client', 'tone' => 'repeat'];
        }

        return ['label' => 'Regular Client', 'tone' => 'regular'];
    }

    private function responseTimerLabel($booking): string
    {
        $created = $booking->created_at ?? now();
        $deadline = $created->copy()->addHours(48);
        $seconds = now()->diffInSeconds($deadline, false);
        if ($seconds <= 0) {
            return '0m';
        }
        if ($seconds < 3600) {
            return max(1, (int) ceil($seconds / 60)) . 'm';
        }
        if ($seconds < 86400) {
            return max(1, (int) ceil($seconds / 3600)) . 'h';
        }

        $days = (int) floor($seconds / 86400);
        $hours = (int) floor(($seconds % 86400) / 3600);

        return $hours > 0 ? $days . 'd ' . $hours . 'h' : $days . 'd';
    }

    private function bookingStartTimeLabel($booking): string
    {
        $time = trim((string) ($booking->time ?? ''));
        if ($time === '') {
            return '--:--';
        }

        if (preg_match('/(\d{1,2}:\d{2})/', $time, $m)) {
            $dt = DateTime::createFromFormat('H:i', $m[1]);

            return $dt instanceof DateTime ? $dt->format('H:i') : $m[1];
        }

        return $time;
    }

    private function filterOptions(): array
    {
        if ($this->isSpaceUser()) {
            return ['Full-Day', 'Half-Day', 'Hourly', 'Garden/Shed', 'Private room', 'Salon', 'Mobile Station'];
        }

        return ['Full Groom', 'Bath & Tidy', 'Nail Trim', 'Pet Spa', 'Face Trim Only', 'Home Visit', 'At Studio'];
    }

    private function bookingMatchesFilters($booking): bool
    {
        if ($this->activeFilters === []) {
            return true;
        }

        $isSpace = $this->isSpaceUser();
        $service = $isSpace ? $this->formatSpaceServiceLabel($booking->service, $booking->time) : (string) ($booking->service ?: '');
        $visit = $isSpace ? $this->formatSpaceVisitLabel($booking->visit_type ?? null) : $this->formatLocationLabel($booking->visit_type ?? null);
        $rawVisit = strtolower(str_replace('_', ' ', (string) ($booking->visit_type ?? '')));
        $haystack = strtolower(trim($service . ' ' . $visit . ' ' . (string) ($booking->service ?? '') . ' ' . $rawVisit));

        foreach ($this->activeFilters as $filter) {
            if ($this->filterMatchesHaystack((string) $filter, $haystack, $rawVisit)) {
                return true;
            }
        }

        return false;
    }

    private function filterMatchesHaystack(string $filter, string $haystack, string $rawVisit = ''): bool
    {
        $needle = strtolower(trim($filter));
        if ($needle === '') {
            return false;
        }

        $aliases = match ($needle) {
            'bath & tidy', 'bath and tidy' => ['bath & tidy', 'bath and tidy', 'bath & brush', 'bath and brush'],
            'face trim only' => ['face trim only', 'face trim'],
            'at studio' => ['at studio', 'salon', 'studio', 'salon visit'],
            'home visit' => ['home visit', 'home', 'homevisit'],
            'garden/shed' => ['garden/shed', 'garden / shed', 'garden shed'],
            'private room' => ['private room', 'private'],
            'mobile station' => ['mobile station', 'mobile'],
            default => [$needle],
        };

        foreach ($aliases as $alias) {
            if (str_contains($haystack, $alias) || ($rawVisit !== '' && str_contains($rawVisit, $alias))) {
                return true;
            }
        }

        return false;
    }

    private function formatLocationLabel(?string $visitType): string
    {
        $label = str_replace('_', ' ', strtolower((string) $visitType));
        if ($label === 'home' || $label === 'home visit') {
            return 'Home Visit';
        }
        if ($label === 'salon' || $label === 'salon visit') {
            return 'Salon Visit';
        }

        return ucfirst($label ?: 'N/A');
    }

    private function toTableRow($booking, array $completedByOwner = []): array
    {
        $firstPet = $booking->pets->first();
        $isSpace = $this->isSpaceUser();
        $spaceService = $this->formatSpaceServiceLabel($booking->service, $booking->time);
        $status = (string) $booking->booking_status;
        $petNames = $booking->pets->pluck('name')->filter()->values()->all();
        $petName = $petNames[0] ?? 'N/A';
        $petMore = count($petNames) > 1 ? ' +' . (count($petNames) - 1) : '';
        $petType = $firstPet?->pet_type;
        $petLabel = $isSpace ? $this->formatSpaceVisitLabel($booking->visit_type ?? null) : trim($petName . ($petType ? ' ' . $petType : '') . $petMore);
        $serviceLabel = $isSpace ? $spaceService : (string) ($booking->service ?: 'N/A');
        $ownerName = $this->ownerShortName($booking->petOwner);
        $previousCompleted = max(0, (int) ($completedByOwner[$booking->pet_owner_id] ?? 0) - ($status === 'completed' ? 1 : 0));
        $tier = $this->clientTierLabel($previousCompleted);
        $date = $booking->date;
        $petPhoto = BusinessHubAvatar::mediaUrl($firstPet?->photo ?? null);
        $avatarName = $isSpace ? (string) ($booking->petOwner?->name ?? $petLabel) : $petName;

        return [
            'id' => (int) $booking->id,
            'idLabel' => $status === 'pending' ? '-----' : 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT),
            'owner' => $ownerName,
            'ownerFull' => (string) ($booking->petOwner?->name ?? 'N/A'),
            'petName' => $petName,
            'petType' => $petType,
            'petMore' => $petMore,
            'petLabel' => $petLabel,
            'petPhoto' => $petPhoto,
            'avatarName' => $avatarName,
            'visitType' => $isSpace ? $this->formatSpaceVisitLabel($booking->visit_type) : ($booking->visit_type ? ucfirst((string) $booking->visit_type) : 'N/A'),
            'serviceHtml' => $isSpace ? e($spaceService) : e($serviceLabel),
            'service' => $serviceLabel,
            'date' => optional($date)->format('d/m/y') ?: '',
            'dateMonth' => $date ? $date->format('M') : '—',
            'dateDay' => $date ? $date->format('j') : '—',
            'startTime' => $this->bookingStartTimeLabel($booking),
            'time' => (string) ($booking->time ?? ''),
            'status' => $status,
            'statusLabel' => ucfirst($status),
            'autoAccepted' => $status === 'confirmed' && (bool) ($booking->auto_accepted ?? false),
            'amount' => number_format((float) $booking->amount, 2),
            'clientTier' => $tier['label'],
            'clientTierTone' => $tier['tone'],
            'timerLabel' => $status === 'pending' ? $this->responseTimerLabel($booking) : '',
            'haystack' => $this->bookingSearchHaystack($booking),
            'filterHaystack' => strtolower(trim($serviceLabel . ' ' . (string) ($booking->visit_type ?? '') . ' ' . str_replace('_', ' ', (string) ($booking->visit_type ?? '')) . ' ' . (string) ($booking->service ?? ''))),
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

        $bookingsQuery = Booking::with(['petOwner:id,name,profile_image', 'pets:id,name,pet_type,breed,photo'])->where(self::SPACER_ID_COLUMN, $profileId);

        if ($this->activeStatus !== 'all') {
            $bookingsQuery->where(self::BOOKING_STATUS_COLUMN, $this->activeStatus);
        }

        $this->applyBookingSort($bookingsQuery);

        $bookings = $bookingsQuery->get();

        if ($this->pendingSort === 'client_az') {
            $bookings = $bookings
                ->sortBy(function ($booking) {
                    $pendingRank = $this->activeStatus === 'all' && (string) $booking->booking_status !== 'pending' ? 1 : 0;

                    return sprintf('%d-%s', $pendingRank, strtolower((string) ($booking->petOwner?->name ?? '')));
                })
                ->values();
        }

        $ownerIds = $bookings->pluck('pet_owner_id')->filter()->unique()->values();
        $completedByOwner = $ownerIds->isEmpty() ? collect() : Booking::query()->where(self::SPACER_ID_COLUMN, $profileId)->where(self::BOOKING_STATUS_COLUMN, 'completed')->whereIn('pet_owner_id', $ownerIds)->selectRaw('pet_owner_id, COUNT(*) as total')->groupBy('pet_owner_id')->pluck('total', 'pet_owner_id');

        $this->bookings = $bookings;
        $this->tableRows = $bookings->map(fn($booking) => $this->toTableRow($booking, $completedByOwner->all()))->values()->all();
        $this->activeBookingsCount = $bookings->count();

        if ($refreshCounts) {
            Cache::forget("dashboard_sidebar_booking_counts_{$profileId}");
            $this->dispatch('booking-counts-updated', counts: $this->statusCounts);
        }
    }

    public function mount(): void
    {
        $this->bookings = collect();
        $this->tableRows = [];
        $this->rescheduleCalendarBookings = collect();
        $this->autoAcceptBooking = (bool) ($this->profile()?->auto_accept_booking ?? true);

        $requestedStatus = strtolower((string) request()->query('booking_status', ''));
        if ($requestedStatus === '') {
            $requestedStatus = BusinessHubNav::fromSession()['active_booking_status'];
        }

        $this->activeStatus = in_array($requestedStatus, $this->allowedStatuses, true) ? $requestedStatus : 'all';
        $this->refreshBookingsAndCounts(force: true);
        $this->dispatch('booking-status-changed', status: $this->activeStatus === 'all' ? '' : $this->activeStatus);
    }

    public function toggleAutoAcceptBooking(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $this->autoAcceptBooking = !$this->autoAcceptBooking;
        $profile->update(['auto_accept_booking' => $this->autoAcceptBooking]);
    }

    public function toggleFilter(string $filter): void
    {
        $filter = trim($filter);
        if ($filter === '' || !in_array($filter, $this->filterOptions(), true)) {
            return;
        }

        if (in_array($filter, $this->activeFilters, true)) {
            $this->activeFilters = array_values(array_filter($this->activeFilters, fn($item) => $item !== $filter));
        } else {
            $this->activeFilters = [...$this->activeFilters, $filter];
        }
    }

    public function clearFilter(string $filter): void
    {
        $this->activeFilters = array_values(array_filter($this->activeFilters, fn($item) => $item !== $filter));
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

        $booking->update([
            'booking_status' => 'confirmed',
            'auto_accepted' => false,
        ]);
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

    public function openRescheduleFromDecline(int $bookingId): void
    {
        $this->closeDeclineModal();
        $this->openRescheduleModal($bookingId);
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
            $this->rescheduleSelectedTime = date('H:i', strtotime($mStart[1]));
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

    #[On('booking-drawer-accept')]
    public function acceptBookingFromDrawer($bookingId = null): void
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? ($bookingId['id'] ?? null);
        }
        $id = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : 0;
        if ($id > 0) {
            $this->acceptBooking($id);
        }
    }

    #[On('booking-drawer-decline')]
    public function declineBookingFromDrawer($bookingId = null): void
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? ($bookingId['id'] ?? null);
        }
        $id = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : 0;
        if ($id > 0) {
            $this->openDeclineModal($id);
        }
    }

    #[On('booking-drawer-reschedule')]
    public function rescheduleBookingFromDrawer($bookingId = null): void
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? ($bookingId['id'] ?? null);
        }
        $id = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : 0;
        if ($id > 0) {
            $this->openRescheduleModal($id);
        }
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
        $start = DateTime::createFromFormat('H:i', $this->rescheduleSelectedTime) ?: DateTime::createFromFormat('H:i A', $this->rescheduleSelectedTime) ?: DateTime::createFromFormat('h:i A', $this->rescheduleSelectedTime);
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

        if (is_string($selectedTime) && (preg_match('/^\d{1,2}:\d{2}$/', $selectedTime) || preg_match('/^\d{1,2}:\d{2}\s(?:AM|PM)$/i', $selectedTime))) {
            $this->rescheduleSelectedTime = $selectedTime;
        }

        $this->confirmRescheduleBooking();
    }

    public function setPendingSort(string $sort): void
    {
        if (!in_array($sort, $this->allowedSorts, true)) {
            $this->dispatch('bookings-tabs-loading-end');

            return;
        }

        $this->pendingSort = $sort;
        $this->refreshBookingsAndCounts(force: true, refreshCounts: false);
        $this->dispatch('bookings-tabs-loading-end');
    }
}; ?>

@php
    $isSpaceUser = $this->isSpaceUser();
    $activeAccent = $isSpaceUser ? '#FFA899' : '#FFC97A';
    $filterOptions = $this->isSpaceUser() ? ['Full-Day', 'Half-Day', 'Hourly', 'Garden/Shed', 'Private room', 'Salon', 'Mobile Station'] : ['Full Groom', 'Bath & Tidy', 'Nail Trim', 'Pet Spa', 'Face Trim Only', 'Home Visit', 'At Studio'];
    $sortLabels = [
        'soonest_first' => 'Soonest first',
        'amount_high' => 'Highest Value',
        'amount_low' => 'Lowest Value',
        'client_az' => 'Client (A-Z)',
    ];
    $pendingSortKey = (string) ($this->pendingSort ?? 'soonest_first');
    $currentSortLabel = $sortLabels[$pendingSortKey] ?? 'Soonest first';
    $allBookingsCount = array_sum(is_array($this->statusCounts) ? $this->statusCounts : []);
    $bookingPills = [['status' => 'all', 'label' => 'All Bookings'], ['status' => 'pending', 'label' => 'Pending'], ['status' => 'confirmed', 'label' => 'Confirmed'], ['status' => 'completed', 'label' => 'Completed'], ['status' => 'cancelled', 'label' => 'Cancelled']];
@endphp

<section class="bookings-board" style="--bh-booking-accent: {{ $activeAccent }};" wire:poll.visible.60s="refreshBookingsForPoll"
    x-data="bhBookingsBoard()">
    <template hidden x-ref="bookingRowsJson" wire:key="booking-rows-{{ md5(json_encode([$activeStatus, $pendingSort, count($tableRows)])) }}">@json($tableRows)</template>
    <div class="bookings-board-header">
        <div class="booking-list-header">
            <div class="booking-pill-row">
                @foreach ($bookingPills as $pill)
                    <button type="button" wire:click="setActiveStatus('{{ $pill['status'] }}')"
                        @click="window.dispatchEvent(new CustomEvent('booking-status-intent', { detail: { status: @js($pill['status'] === 'all' ? '' : $pill['status']) } })); window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                        class="booking-pill {{ $activeStatus === $pill['status'] ? 'is-active' : '' }}{{ $pill['status'] !== 'all' ? ' is-' . $pill['status'] : '' }}">
                        {{ $pill['label'] }}
                        ({{ $pill['status'] === 'all' ? $allBookingsCount : $statusCounts[$pill['status']] ?? 0 }})
                    </button>
                @endforeach
            </div>

            <button type="button" class="bookings-auto-accept {{ $autoAcceptBooking ? 'is-on' : '' }}"
                wire:click="toggleAutoAcceptBooking" wire:loading.attr="disabled"
                wire:target="toggleAutoAcceptBooking"
                aria-pressed="{{ $autoAcceptBooking ? 'true' : 'false' }}"
                aria-label="Auto-accept {{ $autoAcceptBooking ? 'on' : 'off' }}">
                <svg width="9" height="10" viewBox="0 0 9 10" fill="none" aria-hidden="true">
                    <path d="M5.25 0.5L0.75 5.5H4.25L3.75 9.5L8.25 4.5H4.75L5.25 0.5Z" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Auto-accept {{ $autoAcceptBooking ? 'ON' : 'OFF' }}</span>
            </button>
        </div>

        <div class="bookings-toolbar">
            <label class="bookings-search" :class="{ 'has-value': query.trim() !== '' }">
                <input type="text" inputmode="search" autocomplete="off" spellcheck="false" x-model="query"
                    placeholder="Search by pet, owner or booking ID..." aria-label="Search bookings"
                    x-on:keydown.escape.prevent="query = ''" />
                <span class="bookings-search-icon" aria-hidden="true">
                    <img src="{{ asset('images/business-hub/icon-search.svg') }}" alt="" width="16" height="16">
                </span>
            </label>

            <div class="bookings-toolbar-actions">
                <div class="bookings-filter-dropdown" @keydown.escape.window="filterOpen = false"
                    @click.outside="filterOpen = false">
                    <button type="button" class="bookings-tool-btn" @click.stop="filterOpen = !filterOpen; sortOpen = false"
                        :aria-expanded="filterOpen.toString()" aria-label="Filter bookings">
                        <img src="{{ asset('images/business-hub/icon-booking-filter.svg') }}" alt="" width="8" height="10">
                        <span>Filter</span>
                    </button>
                    <div class="bookings-filter-menu" x-cloak x-show="filterOpen" x-transition.opacity.duration.100ms>
                        @foreach ($filterOptions as $option)
                            <button type="button" class="bookings-filter-option"
                                :class="{ 'is-active': activeFilters.includes(@js($option)) }"
                                @click.stop="toggleLocalFilter(@js($option))">
                                <span>{{ $option }}</span>
                                <img class="bookings-menu-check" src="{{ asset('images/business-hub/icon-booking-menu-check.svg') }}" alt="" width="10" height="8" aria-hidden="true">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="bookings-sort-dropdown" @keydown.escape.window="sortOpen = false"
                    @click.outside="sortOpen = false">
                    <button type="button" class="bookings-tool-btn is-sort" @click.stop="sortOpen = !sortOpen; filterOpen = false"
                        :aria-expanded="sortOpen.toString()" aria-label="Sort bookings">
                        <img src="{{ asset('images/business-hub/icon-booking-sort.svg') }}" alt="" width="15" height="11">
                        <span><strong>Sort:</strong> {{ $currentSortLabel }}</span>
                    </button>
                    <div class="bookings-sort-menu" x-cloak x-show="sortOpen" x-transition.opacity.duration.100ms>
                        @foreach ($sortLabels as $sortKey => $sortLabel)
                            <button type="button" class="bookings-sort-option {{ $pendingSort === $sortKey ? 'is-active' : '' }}"
                                wire:click="setPendingSort('{{ $sortKey }}')"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); sortOpen = false">
                                <span>{{ $sortLabel }}</span>
                                <img class="bookings-menu-check" src="{{ asset('images/business-hub/icon-booking-menu-check.svg') }}" alt="" width="10" height="8" aria-hidden="true">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="bookings-filter-chips" x-cloak x-show="activeFilters.length > 0">
            <template x-for="chip in activeFilters" :key="chip">
                <button type="button" class="bookings-filter-chip" @click="clearLocalFilter(chip)"
                    :aria-label="'Remove ' + chip + ' filter'">
                    <span x-text="chip"></span>
                    <span aria-hidden="true">&times;</span>
                </button>
            </template>
        </div>
    </div>

    <div class="bookings-card-list" x-ref="tableWrap">
        <template x-for="row in shownRows" :key="row.id">
            <article class="bh-booking-card" :class="'is-' + row.status" @click="openCard(row)" role="button" tabindex="0"
                @keydown.enter.prevent="openCard(row)" @keydown.space.prevent="openCard(row)">
                <div class="bh-booking-date">
                    <span class="bh-booking-date-month" x-text="row.dateMonth"></span>
                    <span class="bh-booking-date-day" x-text="row.dateDay"></span>
                    <span class="bh-booking-date-time" x-text="row.startTime"></span>
                </div>

                <div class="bh-booking-avatar">
                    <span class="bh-avatar" :class="row.petPhoto ? 'has-photo' : 'is-fallback'" data-bh-avatar
                        style="--bh-avatar-size: 56px; --bh-avatar-font: 20px;">
                        <img class="bh-avatar__photo" x-show="row.petPhoto" :src="row.petPhoto" :alt="row.avatarName || 'Pet'"
                            loading="lazy" decoding="async"
                            x-on:error="window.bhAvatarFallback && window.bhAvatarFallback($event.target)">
                        <span class="bh-avatar__initials" data-bh-avatar-fallback x-show="!row.petPhoto"
                            x-text="avatarInitials(row.avatarName)"></span>
                    </span>
                </div>

                <div class="bh-booking-cols">
                    <div class="bh-booking-col">
                        <span class="bh-booking-label">Booking ID</span>
                        <span class="bh-booking-value" x-text="row.idLabel"></span>
                    </div>
                    <div class="bh-booking-col">
                        <span class="bh-booking-label">Service Type</span>
                        <span class="bh-booking-value is-strong" x-text="row.service"></span>
                    </div>
                    <div class="bh-booking-col">
                        <span class="bh-booking-label" x-text="isSpace ? 'Space' : 'Pet'"></span>
                        <span class="bh-booking-value" x-show="isSpace" x-text="row.petLabel"></span>
                        <span class="bh-booking-value" x-show="!isSpace"><span class="bh-booking-pet-name"
                                x-text="row.petName"></span><span class="bh-booking-pet-meta"
                                x-show="row.petType || row.petMore"
                                x-text="' ' + (row.petType || '') + (row.petMore || '')"></span></span>
                    </div>
                    <div class="bh-booking-owner-block">
                        <div class="bh-booking-col bh-booking-owner-col">
                            <span class="bh-booking-label">Owner</span>
                            <span class="bh-booking-value" x-text="row.owner"></span>
                        </div>
                        <span class="bh-client-chip" x-show="row.status !== 'pending'" x-cloak
                            :class="'is-' + row.clientTierTone" x-text="row.clientTier"></span>
                    </div>
                </div>

                <div class="bh-booking-end">
                    <div class="bh-booking-trailing" x-show="row.status === 'pending'" x-cloak @click.stop>
                        <div class="bh-booking-pending-actions">
                            <div class="bh-booking-timer">
                                <img src="{{ asset('images/business-hub/icon-booking-timer.svg') }}" alt="" width="7"
                                    height="10">
                                <span x-text="row.timerLabel"></span>
                            </div>
                            <button type="button" class="bh-booking-decline"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); $wire.openDeclineModal(Number(row.id))">
                                Decline
                            </button>
                            <button type="button" class="bh-booking-accept" @click="$wire.acceptBooking(Number(row.id))">
                                <img src="{{ asset('images/business-hub/icon-request-accept-check.svg') }}" alt=""
                                    width="12" height="8">
                                <span>Accept</span>
                            </button>
                        </div>
                    </div>

                    <div class="bh-booking-status-price">
                        <div class="bh-status-row">
                            <img class="bh-auto-accept-bolt"
                                x-show="row.status === 'confirmed' && row.autoAccepted" x-cloak
                                src="{{ asset('images/business-hub/icon-booking-auto-accept.svg') }}" alt=""
                                width="9" height="10" aria-hidden="true">
                            <span class="bh-status-chip" :class="'is-' + row.status" x-text="row.statusLabel"></span>
                        </div>
                        <span class="bh-booking-price" x-text="'£' + row.amount"></span>
                    </div>
                </div>
            </article>
        </template>

        <div class="bookings-empty-state" x-cloak x-show="filteredList.length === 0">
            <span x-show="hasActiveSearchOrFilter">
                No bookings here — try a different filter or clear the search.
            </span>
            <span x-show="!hasActiveSearchOrFilter">
                There is no {{ $activeStatus === 'all' ? '' : ucfirst($activeStatus) . ' ' }}bookings.
            </span>
        </div>
    </div>

    <div class="bookings-load-more-wrap" x-show="showLoadMore" x-cloak>
        <button type="button" class="bookings-load-more-btn" @click="loadMore()">
            Load More
        </button>
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
            $cancelledOwnerInitial = \App\Support\BusinessHubAvatar::initials($cancelledOwnerName);
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
                        <span class="cancelled-booking-modal-avatar {{ $cancelledOwnerPhotoUrl ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                            @if ($cancelledOwnerPhotoUrl)
                                <img src="{{ $cancelledOwnerPhotoUrl }}" alt="" width="44" height="44"
                                    onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                                <span data-bh-avatar-fallback hidden>{{ $cancelledOwnerInitial }}</span>
                            @else
                                <span data-bh-avatar-fallback>{{ $cancelledOwnerInitial }}</span>
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
    (function() {
        const bhBookingsBoardFactory = () => ({
            query: window.__bhBookingsSearchQuery || '',
            visible: 08,
            isSpace: {{ $isSpaceUser ? 'true' : 'false' }},
            filterOpen: false,
            sortOpen: false,
            activeFilters: Array.isArray(window.__bhBookingsActiveFilters) ? [...window.__bhBookingsActiveFilters] : [],
            rows: [],
            filteredList: [],
            shownRows: [],
            showLoadMore: false,
            get hasActiveSearchOrFilter() {
                return String(this.query || '').trim() !== '' || (Array.isArray(this.activeFilters) && this.activeFilters.length > 0);
            },
            init() {
                this.syncRows();
                this.$watch('query', (value) => {
                    window.__bhBookingsSearchQuery = value;
                    this.visible = 10;
                    this.applyView();
                });
                this._onLivewireCommit = () => {
                    // Morph can replace the JSON template after commit — sync on the next frames.
                    queueMicrotask(() => this.syncRows());
                    requestAnimationFrame(() => this.syncRows());
                };
                document.addEventListener('livewire:commit', this._onLivewireCommit);
                if (window.Livewire?.hook) {
                    this._unhookMorph = window.Livewire.hook('morph.updated', ({
                        el
                    }) => {
                        if (!el || !this.$el) {
                            return;
                        }
                        if (el === this.$el || this.$el.contains(el) || el.contains?.(this.$el)) {
                            this.syncRows();
                        }
                    });
                    this._unhookCommit = window.Livewire.hook('commit', ({
                        succeed
                    }) => {
                        succeed?.(() => this.syncRows());
                    });
                }
                this.$nextTick(() => this.bindJsonObserver());
            },
            destroy() {
                this.observer?.disconnect();
                if (this._onLivewireCommit) {
                    document.removeEventListener('livewire:commit', this._onLivewireCommit);
                }
                if (typeof this._unhookMorph === 'function') {
                    this._unhookMorph();
                }
                if (typeof this._unhookCommit === 'function') {
                    this._unhookCommit();
                }
            },
            bindJsonObserver() {
                this.observer?.disconnect();
                const json = this.$refs.bookingRowsJson;
                if (!json) {
                    return;
                }
                this.observer = new MutationObserver(() => this.syncRows());
                this.observer.observe(json, {
                    characterData: true,
                    childList: true,
                    subtree: true
                });
            },
            syncRows() {
                this.readRows();
                this.applyView();
                this.bindJsonObserver();
            },
            readRows() {
                const json = this.$refs.bookingRowsJson;
                if (!json) {
                    return;
                }
                const raw = json.content ? json.content.textContent : json.textContent;
                try {
                    const parsed = JSON.parse(raw || '[]');
                    if (Array.isArray(parsed)) {
                        this.rows = parsed;
                    }
                } catch (error) {}
            },
            applyView() {
                let rows = Array.isArray(this.rows) ? this.rows : [];
                if (this.activeFilters.length > 0) {
                    rows = rows.filter((row) => this.activeFilters.some((filter) => this.rowMatchesFilter(row, filter)));
                } else {
                    // No service filter: keep pending bookings first (stable within each group).
                    rows = [...rows].sort((a, b) => {
                        const aPending = a.status === 'pending' ? 0 : 1;
                        const bPending = b.status === 'pending' ? 0 : 1;
                        return aPending - bPending;
                    });
                }
                const q = String(this.query || '').trim().toLowerCase();
                if (q) {
                    rows = rows.filter((row) => String(row.haystack || '').includes(q));
                }
                this.filteredList = rows;
                this.shownRows = q ? rows : rows.slice(0, this.visible);
                this.showLoadMore = !q && rows.length > this.visible;
                window.__bhBookingsActiveFilters = [...this.activeFilters];
            },
            toggleLocalFilter(option) {
                const value = String(option || '').trim();
                if (!value) {
                    return;
                }
                if (this.activeFilters.includes(value)) {
                    this.activeFilters = this.activeFilters.filter((item) => item !== value);
                } else {
                    this.activeFilters = [...this.activeFilters, value];
                }
                this.visible = 10;
                this.applyView();
            },
            clearLocalFilter(option) {
                const value = String(option || '').trim();
                this.activeFilters = this.activeFilters.filter((item) => item !== value);
                this.visible = 10;
                this.applyView();
            },
            rowMatchesFilter(row, filter) {
                const needle = String(filter || '').trim().toLowerCase();
                if (!needle) {
                    return false;
                }
                const haystack = String(row?.filterHaystack || row?.haystack || '').toLowerCase();
                const aliases = ({
                    'bath & tidy': ['bath & tidy', 'bath and tidy', 'bath & brush', 'bath and brush'],
                    'bath and tidy': ['bath & tidy', 'bath and tidy', 'bath & brush', 'bath and brush'],
                    'face trim only': ['face trim only', 'face trim'],
                    'at studio': ['at studio', 'salon', 'studio', 'salon visit'],
                    'home visit': ['home visit', 'home', 'homevisit'],
                    'garden/shed': ['garden/shed', 'garden / shed', 'garden shed'],
                    'private room': ['private room', 'private'],
                    'mobile station': ['mobile station', 'mobile'],
                })[needle] || [needle];
                return aliases.some((alias) => haystack.includes(alias));
            },
            avatarInitials(name) {
                const parts = String(name || 'P').trim().split(/\s+/).filter(Boolean);
                if (!parts.length) {
                    return '??';
                }
                if (parts.length === 1) {
                    return parts[0].slice(0, 2).toUpperCase();
                }
                return (parts[0][0] + parts[1][0]).toUpperCase();
            },
            loadMore() {
                this.visible += 10;
                this.applyView();
            },
            openCard(row) {
                if (!row || !row.id) {
                    return;
                }
                window.dispatchEvent(new CustomEvent('booking-details-open', {
                    detail: row
                }));
            },
        });

        window.__bhBookingsBoardFactory = bhBookingsBoardFactory;

        if (window.Alpine) {
            Alpine.data('bhBookingsBoard', bhBookingsBoardFactory);
        } else if (!window.__bhBookingsBoardInitBound) {
            window.__bhBookingsBoardInitBound = true;
            document.addEventListener('alpine:init', () => {
                Alpine.data('bhBookingsBoard', window.__bhBookingsBoardFactory);
            });
        }
    })();

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
        background: var(--bh-avatar-bg, #FFC97A);
        color: #FDFDFD;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
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
        max-width: 1025px;
        padding: 0.35rem 0 20px;
        box-sizing: border-box;
    }

    .bookings-board [x-cloak] {
        display: none !important;
    }

    .bookings-board-header {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 1.75rem;
        position: relative;
        z-index: 40;
    }

    .booking-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .booking-pill-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .booking-pill {
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        border-radius: 100px;
        min-height: 42px;
        padding: 0.6rem 1.15rem;
        border: none;
        cursor: pointer;
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

    .bookings-auto-accept {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
        color: #C7C7C7;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
    }

    .bookings-auto-accept svg {
        width: 10px;
        height: 15px;
        display: block;
        flex-shrink: 0;
    }

    .bookings-auto-accept.is-on {
        color: #8BAE40;
    }

    .bookings-toolbar {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: nowrap;
        width: 100%;
    }

    .bookings-search {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1 1 738px;
        width: 738px;
        min-width: 0;
        max-width: 738px;
        height: 42px;
    }

    .bookings-search input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        padding: 0 42px 0 20px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        outline: none;
    }

    .bookings-search.has-value input,
    .bookings-search input:focus {
        border-color: var(--bh-booking-accent, #FFC97A);
    }

    .bookings-search input::placeholder {
        color: #D4D4D4;
    }

    .bookings-search-icon {
        position: absolute;
        right: 14px;
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
        width: 16px;
        height: 16px;
        display: block;
    }

    .bookings-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-shrink: 0;
        margin-left: 0;
    }

    .bookings-tool-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 42px;
        width: 76px;
        padding: 0;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .bookings-tool-btn.is-sort {
        width: 171px;
        min-width: 171px;
        font-weight: 500;
        padding: 0 14px;
    }

    .bookings-tool-btn.is-sort strong {
        font-weight: 600;
    }

    .bookings-tool-btn img {
        display: block;
        width: auto;
        height: auto;
        flex-shrink: 0;
    }

    .bookings-filter-dropdown,
    .bookings-sort-dropdown {
        position: relative;
    }

    .bookings-filter-menu,
    .bookings-sort-menu {
        position: absolute;
        top: calc(100% + 20px);
        width: 207px;
        padding: 20px;
        background: #FFF;
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.12);
        box-sizing: border-box;
        z-index: 50;
    }

    .bookings-filter-menu {
        right: 0;
        left: auto;
    }

    .bookings-sort-menu {
        left: 0;
        right: auto;
    }

    .bookings-filter-option,
    .bookings-sort-option {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: 1;
        cursor: pointer;
        text-align: left;
    }

    .bookings-filter-option+.bookings-filter-option,
    .bookings-sort-option+.bookings-sort-option {
        margin-top: 14px;
    }

    .bookings-filter-option:hover,
    .bookings-sort-option:hover {
        color: #FFBA55;
    }

    .bookings-filter-option.is-active,
    .bookings-sort-option.is-active {
        color: #FFBA55;
    }

    .bookings-menu-check {
        width: 10px;
        height: 8px;
        display: block;
        flex-shrink: 0;
        opacity: 0;
        visibility: hidden;
    }

    .bookings-filter-option.is-active .bookings-menu-check,
    .bookings-sort-option.is-active .bookings-menu-check {
        opacity: 1;
        visibility: visible;
    }

    .bookings-filter-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .bookings-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 32px;
        padding: 0 12px;
        border-radius: 100px;
        border: none;
        background: #F6F5F5;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .bookings-card-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
    }

    .bh-booking-card {
        display: grid;
        grid-template-columns: 56px 56px minmax(0, 1fr) auto;
        align-items: center;
        column-gap: 20px;
        width: 100%;
        max-width: 1025px;
        height: 93px;
        min-height: 93px;
        padding: 10px 20px 10px 10px;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        box-sizing: border-box;
        cursor: pointer;
        transition: box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .bh-booking-card:hover {
        box-shadow: 0 0 12px rgba(59, 55, 49, 0.08);
    }

    .bh-booking-card.is-pending {
        border: 2px solid #FFC97A;
        padding: 9px 19px 9px 9px;
    }

    /* Confirmed / completed / cancelled: Figma 555:5465 — cols left-clustered, status+price flush right */
    .bh-booking-card.is-confirmed,
    .bh-booking-card.is-completed,
    .bh-booking-card.is-cancelled {
        grid-template-columns: 56px 56px auto 1fr;
    }

    .bh-booking-card.is-confirmed .bh-booking-cols,
    .bh-booking-card.is-completed .bh-booking-cols,
    .bh-booking-card.is-cancelled .bh-booking-cols {
        grid-template-columns: 80px max-content max-content max-content;
        column-gap: 40px;
        width: max-content;
        max-width: 100%;
    }

    .bh-booking-card.is-confirmed .bh-booking-cols .bh-booking-value,
    .bh-booking-card.is-completed .bh-booking-cols .bh-booking-value,
    .bh-booking-card.is-cancelled .bh-booking-cols .bh-booking-value {
        overflow: visible;
        text-overflow: unset;
    }

    .bh-booking-card.is-confirmed .bh-booking-end,
    .bh-booking-card.is-completed .bh-booking-end,
    .bh-booking-card.is-cancelled .bh-booking-end {
        justify-self: end;
        width: auto;
    }

    .bh-booking-owner-block {
        display: flex;
        align-items: center;
        gap: 20px;
        min-width: 0;
        position: relative;
        box-sizing: border-box;
    }

    .bh-booking-owner-block .bh-booking-col {
        flex: 0 1 auto;
        min-width: 0;
    }

    .bh-booking-date {
        width: 56px;
        height: 73px;
        border-radius: 5px;
        background: #F7F4EC;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        color: #3B3731;
        font-family: Lato;
        text-align: center;
        flex-shrink: 0;
    }

    .bh-booking-date-month {
        color: #3B3731;
        text-align: center;
        leading-trim: both;
        text-edge: cap;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 15px;
    }

    .bh-booking-date-day {
        color: #3B3731;
        leading-trim: both;
        text-edge: cap;
        font-family: "Playfair Display";
        font-size: 25px;
        font-style: normal;
        font-weight: 700;
        line-height: 25px;
    }

    .bh-booking-date-time {
        color: #9D9B98;
        leading-trim: both;
        text-edge: cap;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .bh-booking-avatar {
        width: 56px;
        height: 56px;
        flex-shrink: 0;
    }

    .bh-booking-avatar .bh-avatar {
        width: 56px;
        height: 56px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
    }

    .bh-booking-avatar .bh-avatar.is-fallback {
        background: var(--bh-avatar-bg, var(--bh-booking-accent, #FFC97A));
    }

    .bh-booking-avatar .bh-avatar__photo {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    .bh-booking-avatar .bh-avatar__initials {
        color: #FDFDFD;
        font-family: Lato, sans-serif;
        font-size: var(--bh-avatar-font, 20px);
        font-weight: 800;
        line-height: 1;
    }

    .bh-booking-cols {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr) minmax(0, 1.25fr) minmax(0, 1.35fr);
        column-gap: 40px;
        min-width: 0;
        align-items: center;
        width: 100%;
    }

    .bh-booking-col {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
        position: relative;
        box-sizing: border-box;
    }

    .bh-booking-col+.bh-booking-col::before,
    .bh-booking-cols>.bh-booking-owner-block::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 43px;
        background: #E2E2E2;
    }

    .bh-booking-label {
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        white-space: nowrap;
    }

    .bh-booking-value {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bh-booking-value.is-strong {
        font-weight: 600;
    }

    .bh-booking-pet-name {
        font-weight: 600;
        color: #3B3731;
    }

    .bh-booking-pet-meta {
        font-weight: 400;
        color: #9D9B98;
    }

    .bh-booking-end {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 16px;
        flex-shrink: 0;
        min-width: 0;
    }

    .bh-booking-trailing {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        min-width: 0;
        flex-shrink: 0;
    }

    .bh-client-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 24px;
        padding: 0 10px;
        border-radius: 100px;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .bh-client-chip.is-new {
        background: rgba(201, 221, 160, 0.2);
        color: #AFCD6F;
    }

    .bh-client-chip.is-repeat {
        background: #F7FAFC;
        color: #94BEDB;
    }

    .bh-client-chip.is-regular {
        background: #FDF8F0;
        color: #F9C45C;
    }

    .bh-booking-pending-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .bh-booking-timer {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #FFA899;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        white-space: nowrap;
    }

    .bh-booking-timer img {
        width: 9px;
        height: 13px;
        display: block;
        flex-shrink: 0;
    }

    .bh-booking-decline {
        width: 94px;
        height: 42px;
        border-radius: 75px;
        border: none;
        background: rgba(254, 111, 86, 0.1);
        color: #FE6F56;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .bh-booking-accept {
        width: 112px;
        height: 42px;
        border-radius: 75px;
        border: 1px solid #AFCD6F;
        background: #C9DDA0;
        color: #FFF;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .bh-booking-accept img {
        width: 12px;
        height: 8px;
        display: block;
    }

    .bh-booking-status-price {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 12px;
        min-width: 85px;
        flex-shrink: 0;
    }

    .bh-status-row {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .bh-auto-accept-bolt {
        width: 9px;
        height: 10px;
        display: block;
        flex-shrink: 0;
        color: #8BAE40;
    }

    .bh-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 30px;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 100px;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .bh-status-chip.is-pending {
        background: rgba(255, 201, 122, 0.2);
        color: #FFBA55;
    }

    .bh-status-chip.is-confirmed {
        background: rgba(201, 221, 160, 0.2);
        color: #AFCD6F;
    }

    .bh-status-chip.is-completed {
        background: rgba(148, 190, 219, 0.2);
        color: #94BEDB;
    }

    .bh-status-chip.is-cancelled {
        background: rgba(254, 111, 86, 0.12);
        color: #FE6F56;
    }

    .bh-booking-price {
        color: #3B3731;
        text-align: right;
        leading-trim: both;
        text-edge: cap;
        font-family: "Playfair Display";
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .bookings-empty-state {
        display: block;
        width: 100%;
        min-height: 220px;
        padding: 4rem 1.5rem;
        text-align: center;
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.5;
        box-sizing: border-box;
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
        border-radius: 75px;
        border: 1px solid #3B3731;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }

    .bookings-load-more-btn:hover {
        background: var(--bh-booking-accent, #FFC97A);
        color: #FFF;
        border-color: var(--bh-booking-accent, #FFC97A);
        transform: translateY(-1px);
    }

    .section-container:has(.section-panel.section-active .bookings-board) {
        overflow: visible;
    }

    @media (max-width: 900px) {
        .bh-booking-card {
            grid-template-columns: 56px 56px minmax(0, 1fr);
            grid-template-rows: auto auto;
            height: auto;
            min-height: 93px;
        }

        .bh-booking-end {
            grid-column: 1 / -1;
            justify-content: flex-start;
            flex-wrap: wrap;
            min-width: 0;
        }

        .bh-booking-cols {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 20px;
        }

        .bh-booking-col+.bh-booking-col::before,
        .bh-booking-cols>.bh-booking-owner-block::before {
            display: none;
        }

        .bookings-toolbar {
            flex-wrap: wrap;
            gap: 12px;
        }

        .bookings-search {
            width: 100%;
            max-width: none;
            flex: 1 1 100%;
        }

        .bookings-toolbar-actions {
            width: 100%;
        }

        .bookings-tool-btn {
            width: auto;
            padding: 0 14px;
        }

        .bookings-tool-btn.is-sort {
            flex: 1;
            width: auto;
        }
    }

    @media (max-width: 720px) {
        .bh-booking-cols {
            grid-template-columns: 1fr;
        }
    }
</style>
