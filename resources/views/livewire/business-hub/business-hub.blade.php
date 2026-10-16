<?php

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Models\Review;
use App\Support\BusinessHubAvatar;
use App\Support\BookingDeclineReasons;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public $activeTab = 'overview';
    public ?string $activeModal = null;
    public ?int $detailsBookingId = null;
    public ?int $confirmedBookingId = null;
    public ?int $declineBookingId = null;
    public ?int $rescheduleBookingId = null;
    public ?string $rescheduleSelectedDate = null;
    public ?string $rescheduleSelectedTime = null;
    public ?string $rescheduleCalendarMonth = null;
    public int $rescheduleDurationMinutes = 60;
    /** @var \Illuminate\Support\Collection<int, \App\Models\Booking> */
    public $rescheduleCalendarBookings;
    public $showAllCards = [
        'todays' => false,
        'pending' => false,
        'upcoming' => false,
    ];

    public function mount(): void
    {
        $this->rescheduleCalendarBookings = collect();
    }

    /**
     * @param  \App\Models\Booking|object  $booking
     * @return list<array{name: string, photo: ?string}>
     */
    private function bookingPetAvatars($booking): array
    {
        $pets = $this->bookingPets($booking);
        if ($pets->isEmpty()) {
            return [['name' => 'Pet', 'photo' => null]];
        }

        return $pets
            ->map(
                fn($pet) => [
                    'name' => trim((string) ($pet->name ?? '')) ?: 'Pet',
                    'photo' => BusinessHubAvatar::mediaUrl($pet->photo ?? null),
                ],
            )
            ->values()
            ->all();
    }

    public function openCardModal(string $card): void
    {
        if (!array_key_exists($card, $this->showAllCards)) {
            return;
        }
        $this->activeModal = $card;
        $this->showAllCards = [...$this->showAllCards, $card => true];
        unset($this->todaysBookings, $this->pendingRequests, $this->upcomingBookings);
    }

    public function closeCardModal(): void
    {
        if ($this->activeModal && array_key_exists($this->activeModal, $this->showAllCards)) {
            $this->showAllCards = [...$this->showAllCards, $this->activeModal => false];
        }
        $this->activeModal = null;
        unset($this->todaysBookings, $this->pendingRequests, $this->upcomingBookings);
    }

    private function visitTypeLabel(string $visitType): string
    {
        $normalized = strtolower(str_replace(['-', ' '], '_', trim($visitType)));

        return match (true) {
            $normalized === 'home_visit' || str_contains($normalized, 'home') => 'Home Visits',
            $normalized === 'salon' || str_contains($normalized, 'salon') => 'Salon',
            $normalized === 'mobile_station' || str_contains($normalized, 'mobile') => 'Mobile Station',
            $normalized === 'garden_shed' || str_contains($normalized, 'garden') || str_contains($normalized, 'shed') => 'Garden/Shed',
            $normalized !== '' => ucwords(str_replace('_', ' ', $normalized)),
            default => '',
        };
    }

    private function visitTypeBadgeLabel($booking): string
    {
        $visitType = trim((string) ($booking->visit_type ?? ''));
        if ($visitType === '') {
            return '';
        }

        return $this->visitTypeLabel($visitType);
    }

    private function resolvePetType($pet): string
    {
        return (string) ($pet?->pet_type ?? ($pet?->type ?? ($pet?->species ?? ($pet?->breed ?? '—'))));
    }

    private function resolvePetTypeForList($pet): ?string
    {
        $type = trim((string) ($pet?->pet_type ?? ($pet?->type ?? ($pet?->species ?? ($pet?->breed ?? '')))));
        return $type !== '' ? $type : null;
    }

    private function bookingPets($booking): Collection
    {
        if ($booking instanceof Booking) {
            return $booking->relationLoaded('pets') ? $booking->pets : $booking->pets()->get();
        }

        return collect(data_get($booking, 'pets', []));
    }

    private function resolveBookingPetType($booking): string
    {
        $types = $this->bookingPets($booking)->map(fn($pet) => $this->resolvePetTypeForList($pet))->filter()->unique()->values();

        return $types->isEmpty() ? '—' : $types->implode(', ');
    }

    private function resolveBookingPetWeight($booking): string
    {
        $weights = $this->bookingPets($booking)
            ->map(function ($pet) {
                $weight = trim((string) ($pet->weight ?? ''));

                return $weight !== '' ? $weight : null;
            })
            ->filter()
            ->unique()
            ->values();

        if ($weights->isEmpty()) {
            return '—';
        }

        $labels = $weights
            ->map(function ($weight) {
                $n = (float) $weight;
                if ($n <= 7) {
                    return 'small';
                }
                if ($n <= 18) {
                    return 'medium';
                }

                return 'large';
            })
            ->unique()
            ->values();

        return $labels->isEmpty() ? '—' : $labels->implode(', ');
    }

    private function loggedInSpacerId(): int
    {
        return (int) (Auth::guard('groomer_spacer')->id() ?? (Auth::id() ?? 0));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Booking>
     */
    private function bookingsQuery()
    {
        return Booking::query()->where('goormer_spacer_id', $this->loggedInSpacerId());
    }

    public function isSpaceAccount(): bool
    {
        return strtolower((string) (Auth::guard('groomer_spacer')->user()?->user_type ?? (GroomerSpacerProfile::query()->find($this->loggedInSpacerId())?->user_type ?? ''))) === 'space';
    }

    private function ownerDisplayName($owner): string
    {
        $name = trim((string) ($owner?->name ?? ($owner?->full_name ?? '')));

        return $name !== '' ? $name : 'Client';
    }

    private function ownerShortName($owner): string
    {
        $name = $this->ownerDisplayName($owner);
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parts) >= 2) {
            return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.';
        }

        return $name;
    }

    private function bookingDurationLabel($booking): string
    {
        $raw = trim((string) data_get($booking, 'duration', ''));
        if ($raw !== '' && is_numeric($raw)) {
            return ((int) $raw) . ' min';
        }

        $service = strtolower(trim((string) ($booking->service ?? '')));
        if (str_contains($service, 'half')) {
            return 'Half-day';
        }
        if (str_contains($service, 'full')) {
            return 'Full-day';
        }
        if (str_contains($service, 'hour')) {
            return 'Hourly';
        }

        return '60 min';
    }

    private function bookingTimeRangeLabel($booking): string
    {
        $time = trim((string) ($booking->time ?? ''));
        if ($time === '') {
            return '—';
        }

        if (str_contains($time, '-')) {
            return $time;
        }

        try {
            $start = \Carbon\Carbon::createFromFormat('H:i', substr($time, 0, 5));
            $end = $start->copy()->addMinutes(60);

            return $start->format('H:i') . ' - ' . $end->format('H:i');
        } catch (\Throwable) {
            return $time;
        }
    }

    private function bookingStartTimeLabel($booking): string
    {
        $time = trim((string) ($booking->time ?? ''));
        if ($time === '') {
            return '—';
        }

        if (preg_match('/^(\d{1,2}:\d{2})/', $time, $m)) {
            return $m[1];
        }

        return $time;
    }

    private function expiresInLabel($booking): string
    {
        $created = $booking->created_at ?? now();
        $deadline = $created->copy()->addHours(48);
        $seconds = now()->diffInSeconds($deadline, false);
        if ($seconds <= 0) {
            return 'Expired';
        }
        if ($seconds < 3600) {
            return 'Expires in ' . max(1, (int) ceil($seconds / 60)) . 'm';
        }
        if ($seconds < 86400) {
            return 'Expires in ' . max(1, (int) ceil($seconds / 3600)) . 'h';
        }

        return 'Expires in ' . max(1, (int) ceil($seconds / 86400)) . 'd';
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

    private function schedulePetOwnerLine($booking): string
    {
        $pets = $this->bookingPets($booking);
        $owner = $this->ownerShortName($booking->petOwner ?? null);
        $partyLabel = $this->isSpaceAccount() ? 'Client' : 'Owner';

        if ($pets->isEmpty()) {
            return $partyLabel . ': ' . $owner;
        }

        $first = $pets->first();
        $name = trim((string) ($first->name ?? 'Pet')) ?: 'Pet';
        $breed = trim((string) ($first->breed ?? ''));
        $extra = max(0, $pets->count() - 1);
        $petBit = $breed !== '' ? $name . ' (' . $breed . ')' : $name;
        if ($extra > 0) {
            $petBit .= ' +' . $extra;
        }

        return $petBit . ' · ' . $partyLabel . ': ' . $owner;
    }

    private function bookingIdLabel(int|string $bookingId): string
    {
        $prefix = $this->isSpaceAccount() ? 'SH-' : 'FG-';

        return $prefix . str_pad((string) $bookingId, 5, '0', STR_PAD_LEFT);
    }

    public function getTodaysBookings()
    {
        return $this->bookingsQuery()
            ->with(['pets', 'petOwner'])
            ->today()
            ->whereIn('booking_status', ['confirmed', 'completed', 'pending'])
            ->orderBy('time')
            ->when(!$this->showAllCards['todays'], fn($q) => $q->limit(3))
            ->get()
            ->map(
                fn($b) => [
                    'id' => $this->bookingIdLabel($b->id),
                    'booking_id' => $b->id,
                    'pet_avatars' => $this->bookingPetAvatars($b),
                    'service_type' => $this->visitTypeBadgeLabel($b),
                    'date' => $b->date?->format('d M'),
                    'time' => $this->bookingStartTimeLabel($b),
                    'time_range' => $this->bookingTimeRangeLabel($b),
                    'duration' => $this->bookingDurationLabel($b),
                    'service' => trim((string) ($b->service ?? '')) ?: 'Service',
                    'pet_owner_line' => $this->schedulePetOwnerLine($b),
                    'pet_type' => $this->resolveBookingPetType($b),
                    'weight' => $this->resolveBookingPetWeight($b),
                    'status' => (string) $b->booking_status,
                ],
            )
            ->toArray();
    }

    public function getTodaysBookingsCount(): int
    {
        return $this->bookingsQuery()
            ->today()
            ->whereIn('booking_status', ['confirmed', 'completed', 'pending'])
            ->count();
    }

    public function getEarnedToday(): array
    {
        $completedToday = $this->bookingsQuery()->today()->where('booking_status', 'completed');
        $done = (clone $completedToday)->count();
        $earned = (float) (clone $completedToday)->sum('amount');
        $left = $this->bookingsQuery()
            ->today()
            ->whereIn('booking_status', ['confirmed', 'pending'])
            ->count();

        return [
            'amount' => $earned,
            'done' => $done,
            'left' => $left,
            'badge' => $done . ' done · ' . $left . ' appointment' . ($left === 1 ? '' : 's') . ' left',
        ];
    }

    public function getNextAppointment(): ?array
    {
        $booking = $this->bookingsQuery()
            ->with(['pets', 'petOwner'])
            ->where('booking_status', 'confirmed')
            ->where(function ($q) {
                $q->whereDate('date', '>', today())->orWhere(function ($inner) {
                    $inner->whereDate('date', today())->where('time', '>=', now()->format('H:i'));
                });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->first();

        if (!$booking) {
            return null;
        }

        $startTime = $this->bookingStartTimeLabel($booking);
        $when = $booking->date?->isToday() ? 'Today, ' . $startTime : ($booking->date?->isTomorrow() ? 'Tomorrow, ' . $startTime : $booking->date?->format('D, j M') . ', ' . $startTime);

        $pets = $this->bookingPets($booking);
        $firstPet = trim((string) ($pets->first()?->name ?? ''));
        $service = trim((string) ($booking->service ?? 'Appointment')) ?: 'Appointment';
        $visit = strtolower(trim((string) ($booking->visit_type ?? '')));
        $profile = Auth::guard('groomer_spacer')->user() ?? GroomerSpacerProfile::query()->find($this->loggedInSpacerId());
        $businessName = trim((string) data_get($profile, 'business_name', data_get($profile, 'business_profiles.0.business_name', 'your space')));

        if ($this->isSpaceAccount()) {
            $subtitle = $service . ' · at ' . ($businessName !== '' ? $businessName : 'your space');
        } else {
            $visitBadge = $this->visitTypeBadgeLabel($booking);
            $location = str_contains($visit, 'home') ? "at customer's home" : (str_contains($visit, 'salon') ? 'at salon' : (str_contains($visit, 'garden') || str_contains($visit, 'shed') ? 'at garden/shed' : 'at ' . ($visitBadge !== '' ? strtolower($visitBadge) : 'location')));
            $subtitle = $service . ($firstPet !== '' ? ' · ' . $firstPet : '') . ' · ' . $location;
        }

        return [
            'booking_id' => $booking->id,
            'when' => $when,
            'subtitle' => $subtitle,
        ];
    }

    public function getLatestReview(): ?array
    {
        $review = Review::query()
            ->with(['petOwner', 'booking'])
            ->whereHas('booking', fn($q) => $q->where('goormer_spacer_id', $this->loggedInSpacerId()))
            ->orderByDesc('created_at')
            ->first();

        if (!$review) {
            return null;
        }

        $owner = $review->petOwner;
        $firstName = explode(' ', $this->ownerDisplayName($owner))[0] ?? 'Client';
        $quote = trim((string) ($review->review ?? ''));
        if (mb_strlen($quote) > 42) {
            $quote = rtrim(mb_substr($quote, 0, 42)) . '…';
        }

        return [
            'id' => $review->id,
            'owner_id' => (int) ($review->pet_owner_id ?? 0),
            'owner_first' => $firstName,
            'rating' => number_format((float) ($review->rating ?? 0), 1),
            'quote' => $quote !== '' ? '"' . $quote . '" — say thanks!' : 'Say thanks for the review!',
            'has_reply' => trim((string) ($review->reply ?? '')) !== '',
        ];
    }

    public function getPendingRequests()
    {
        $limit = $this->showAllCards['pending'] ? null : 3;

        return $this->bookingsQuery()
            ->with(['pets', 'petOwner'])
            ->pending()
            ->orderByRaw('CASE WHEN date >= ? THEN 0 ELSE 1 END', [today()->toDateString()])
            ->orderBy('date')
            ->orderBy('time')
            ->when($limit, fn($q) => $q->limit($limit))
            ->get()
            ->map(function ($b) {
                $previous = $this->bookingsQuery()->where('pet_owner_id', $b->pet_owner_id)->where('id', '!=', $b->id)->where('booking_status', 'completed')->count();
                $tier = $this->clientTierLabel($previous);
                $pets = $this->bookingPets($b);
                $petTypes = $pets->map(fn($p) => trim((string) ($p->pet_type ?? ($p->type ?? ($p->species ?? '')))))->filter()->unique()->values();
                $petNames = $pets->map(fn($p) => trim((string) ($p->name ?? '')))->filter()->values();
                $firstPet = $petNames->first() ?: 'Pet';
                $extraPets = max(0, $petNames->count() - 1);
                $petLabel = $extraPets > 0 ? $firstPet . ' +' . $extraPets : $firstPet;

                return [
                    'id' => $this->bookingIdLabel($b->id),
                    'booking_id' => $b->id,
                    'owner_name' => $this->ownerShortName($b->petOwner),
                    'owner_photo' => BusinessHubAvatar::mediaUrl($b->petOwner?->profile_image ?? null),
                    'pet_avatars' => $this->bookingPetAvatars($b),
                    'pet_label' => $petLabel,
                    'pet_types_label' => $petTypes->isEmpty() ? 'Pet' : $petTypes->implode(' & '),
                    'service' => trim((string) ($b->service ?? 'Service')) ?: 'Service',
                    'service_type' => $this->visitTypeBadgeLabel($b),
                    'date' => $b->date?->format('j M') ?? '—',
                    'time' => $this->bookingTimeRangeLabel($b),
                    'price' => (float) $b->amount,
                    'client_tier' => $tier['label'],
                    'client_tier_tone' => $tier['tone'],
                    'expires_in' => $this->expiresInLabel($b),
                ];
            })
            ->toArray();
    }

    public function getPendingRequestsCount(): int
    {
        return $this->bookingsQuery()->pending()->count();
    }

    public function getUpcomingBookings()
    {
        return $this->bookingsQuery()
            ->with('pets')
            ->upcoming()
            ->confirmed()
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->when(!$this->showAllCards['upcoming'], fn($q) => $q->limit(2))
            ->get()
            ->map(
                fn($b) => [
                    'id' => $this->bookingIdLabel($b->id),
                    'status' => ucfirst($b->booking_status),
                    'pet_avatars' => $this->bookingPetAvatars($b),
                    'pet_count' => $b->pets->count(),
                    'pets' => ($b->pets->count() > 1 ? $b->pets->map(fn($p) => $this->resolvePetType($p)) : $b->pets->map(fn($p) => $this->resolvePetType($p) . ($p->breed ? ' (' . $p->breed . ')' : '')))->unique()->values()->toArray(),
                    'pet_type' => $this->resolveBookingPetType($b),
                    'weight' => $this->resolveBookingPetWeight($b),
                    'service' => $b->service,
                    'visit_type' => $this->visitTypeBadgeLabel($b),
                    'time' => $b->time,
                    'date' => $b->date->format('d/m/Y'),
                    'amount' => (float) $b->amount,
                    'booking_id' => $b->id,
                ],
            )
            ->toArray();
    }

    public function getUpcomingBookingsCount(): int
    {
        return $this->bookingsQuery()->upcoming()->confirmed()->count();
    }

    public function getWeeklyRevenue()
    {
        $startOfWeek = now()->copy()->startOfWeek();
        $endOfWeek = now()->copy()->endOfWeek();

        $dailyTotals = $this->bookingsQuery()
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->selectRaw('date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $total = (float) $dailyTotals->sum();

        $lastWeekStart = $startOfWeek->copy()->subWeek();
        $lastWeekEnd = $endOfWeek->copy()->subWeek();
        $lastWeekTotal = (float) $this->bookingsQuery()
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->whereBetween('date', [$lastWeekStart->toDateString(), $lastWeekEnd->toDateString()])
            ->sum('amount');

        $changeAmount = $total - $lastWeekTotal;

        $chartData = collect(range(0, 6))
            ->map(function ($offset) use ($startOfWeek, $dailyTotals) {
                $day = $startOfWeek->copy()->addDays($offset);
                $key = $day->toDateString();

                return [
                    'date' => $day->format('D') . ' ' . $day->format('j'),
                    'amount' => (float) ($dailyTotals[$key] ?? 0),
                ];
            })
            ->toArray();

        return [
            'total' => $total,
            'change' => $changeAmount,
            'change_amount' => $changeAmount,
            'chart_data' => $chartData,
        ];
    }

    public function getStats()
    {
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $completedStats = $this->bookingsQuery()
            ->where('booking_status', 'completed')
            ->selectRaw(
                'COUNT(*) as completed_count,
                COALESCE(SUM(amount), 0) as total_revenue,
                SUM(CASE WHEN date BETWEEN ? AND ? THEN 1 ELSE 0 END) as this_month_completed,
                SUM(CASE WHEN date BETWEEN ? AND ? THEN 1 ELSE 0 END) as last_month_completed',
                [$thisMonthStart, $thisMonthEnd, $lastMonthStart, $lastMonthEnd],
            )
            ->first();

        $completedBookings = (int) ($completedStats->completed_count ?? 0);
        $thisMonthCompleted = (int) ($completedStats->this_month_completed ?? 0);
        $lastMonthCompleted = (int) ($completedStats->last_month_completed ?? 0);
        $bookingDiff = $thisMonthCompleted - $lastMonthCompleted;

        $totalRevenue = (float) ($completedStats->total_revenue ?? 0);
        $avgRevenue = $completedBookings > 0 ? round($totalRevenue / max($completedBookings, 1)) : 0;
        $profileVisits = (int) (GroomerSpacerProfile::query()->find($this->loggedInSpacerId())?->profile_visit ?? 0);

        $repeatClients = $this->bookingsQuery()->where('booking_status', 'completed')->select('pet_owner_id')->groupBy('pet_owner_id')->havingRaw('COUNT(*) > 1')->get()->count();

        $totalClients = $this->bookingsQuery()->where('booking_status', 'completed')->distinct('pet_owner_id')->count('pet_owner_id');
        $repeatPercent = $totalClients > 0 ? round(($repeatClients / $totalClients) * 100) : 0;

        return [
            'total_bookings' => [
                'value' => $completedBookings,
                'label' => 'bookings completed',
                'change' => ($bookingDiff >= 0 ? '+' : '') . $bookingDiff . ' vs last month',
                'change_type' => $bookingDiff >= 0 ? 'positive' : 'negative',
            ],
            'avg_revenue' => [
                'value' => $avgRevenue,
                'label' => 'per booking',
                'sublabel' => 'Based on ' . $completedBookings . ' bookings',
                'currency' => '£',
            ],
            'profile_views' => [
                'value' => $profileVisits,
                'label' => 'viewed this month',
                'sublabel' => $thisMonthCompleted . ' became bookings',
            ],
            'repeat_clients' => [
                'value' => $repeatClients,
                'label' => 'returning clients',
                'sublabel' => $repeatPercent . '% clients rebooked',
                'change_type' => 'positive',
            ],
        ];
    }

    public function acceptRequest($bookingId)
    {
        $booking = $this->bookingsQuery()
            ->with(['pets', 'petOwner'])
            ->findOrFail($bookingId);
        $booking->update([
            'booking_status' => 'confirmed',
            'auto_accepted' => false,
        ]);

        $spacerId = $this->loggedInSpacerId();
        if ($spacerId) {
            Cache::forget("dashboard_sidebar_booking_counts_{$spacerId}");
        }

        $this->detailsBookingId = null;
        unset($this->detailsBooking);
        $this->activeModal = null;
        $this->showAllCards = [...$this->showAllCards, 'pending' => false];
        $this->confirmedBookingId = (int) $booking->id;
        unset($this->confirmedBooking, $this->pendingRequests, $this->pendingRequestsCount, $this->todaysBookings, $this->todaysBookingsCount, $this->earnedToday, $this->nextAppointment);

        $this->dispatch('request-accepted', requestId: $bookingId);
    }

    public function closeConfirmedModal(): void
    {
        unset($this->confirmedBooking);
        $this->confirmedBookingId = null;
    }

    public function openDeclineFromOverview(int $bookingId): void
    {
        $this->detailsBookingId = null;
        unset($this->detailsBooking);
        $this->declineBookingId = $bookingId;
        unset($this->declineBooking);
    }

    public function closeDeclineModal(): void
    {
        $this->declineBookingId = null;
    }

    public function openRescheduleFromDecline(int $bookingId): void
    {
        $this->closeDeclineModal();
        $this->openRescheduleModal($bookingId);
    }

    public function openRescheduleModal(int $bookingId): void
    {
        $booking = $this->bookingsQuery()
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->find($bookingId);

        if (!$booking) {
            return;
        }

        $this->detailsBookingId = null;
        unset($this->detailsBooking);
        $this->rescheduleBookingId = $bookingId;
        $currentDate = $booking->date ? date('Y-m-d', strtotime((string) $booking->date)) : date('Y-m-d');
        $this->rescheduleSelectedDate = $currentDate;
        $this->rescheduleCalendarMonth = date('Y-m-01', strtotime($currentDate));
        $this->rescheduleDurationMinutes = 60;
        $this->rescheduleSelectedTime = null;
        $this->rescheduleCalendarBookings = $this->bookingsQuery()
            ->where('booking_status', '!=', 'cancelled')
            ->select(['id', 'date', 'booking_status'])
            ->get();

        $timeRaw = trim((string) $booking->time);
        if (preg_match('/(\d{1,2}:\d{2})/', $timeRaw, $mStart)) {
            $this->rescheduleSelectedTime = date('H:i', strtotime($mStart[1]));
        }

        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[0] ?? ''), $mStart);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[1] ?? ''), $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                try {
                    $startTs = strtotime($mStart[1]);
                    $endTs = strtotime($mEnd[1]);
                    if ($endTs < $startTs) {
                        $endTs = strtotime('+1 day', $endTs);
                    }
                    $this->rescheduleDurationMinutes = (int) max(1, ($endTs - $startTs) / 60);
                } catch (\Throwable) {
                    $this->rescheduleDurationMinutes = 60;
                }
            }
        }

        unset($this->rescheduleBooking);
    }

    public function closeRescheduleModal(): void
    {
        $this->rescheduleBookingId = null;
        $this->rescheduleCalendarMonth = null;
        $this->rescheduleSelectedDate = null;
        $this->rescheduleSelectedTime = null;
        $this->rescheduleDurationMinutes = 60;
        $this->rescheduleCalendarBookings = collect();
        unset($this->rescheduleBooking);
    }

    public function confirmRescheduleBooking(): void
    {
        if (!$this->rescheduleBookingId || !$this->rescheduleSelectedDate || !$this->rescheduleSelectedTime) {
            return;
        }

        $start = \DateTime::createFromFormat('H:i', $this->rescheduleSelectedTime) ?: \DateTime::createFromFormat('H:i A', $this->rescheduleSelectedTime) ?: \DateTime::createFromFormat('h:i A', $this->rescheduleSelectedTime);
        if (!$start) {
            return;
        }

        $duration = max(1, $this->rescheduleDurationMinutes);
        $end = (clone $start)->modify('+' . $duration . ' minutes');
        $timeRange = $start->format('H:i') . ' - ' . $end->format('H:i');

        $updated = $this->bookingsQuery()
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->whereKey($this->rescheduleBookingId)
            ->update([
                'date' => $this->rescheduleSelectedDate,
                'time' => $timeRange,
            ]);

        if (!$updated) {
            $this->closeRescheduleModal();

            return;
        }

        $spacerId = $this->loggedInSpacerId();
        if ($spacerId) {
            Cache::forget("dashboard_sidebar_booking_counts_{$spacerId}");
        }

        unset($this->pendingRequests, $this->pendingRequestsCount, $this->todaysBookings, $this->upcomingBookings);
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

    public function confirmDeclineBooking(?string $reason = null): void
    {
        if (!$this->declineBookingId) {
            return;
        }

        $booking = $this->bookingsQuery()->find($this->declineBookingId);
        if ($booking) {
            $booking->update([
                'booking_status' => 'cancelled',
                'cancelled_by' => 'groomer_spacer',
                'cancellation_reason' => BookingDeclineReasons::normalize($reason),
            ]);
        }

        $spacerId = $this->loggedInSpacerId();
        if ($spacerId) {
            Cache::forget("dashboard_sidebar_booking_counts_{$spacerId}");
        }

        if ($this->detailsBookingId === $this->declineBookingId) {
            $this->closeDetailsModal();
        }

        $this->declineBookingId = null;
        unset($this->pendingRequests, $this->pendingRequestsCount, $this->declineBooking);
    }

    public function viewDetails($bookingId): void
    {
        $bookingId = (int) $bookingId;
        unset($this->detailsBooking);

        $exists = $this->bookingsQuery()->pending()->whereDate('date', '>=', today())->whereKey($bookingId)->exists();

        $this->detailsBookingId = $exists ? $bookingId : null;
    }

    public function closeDetailsModal(): void
    {
        unset($this->detailsBooking);
        $this->detailsBookingId = null;
    }

    public function replyToLatestReview(): void
    {
        $review = $this->latestReview;
        $ownerId = (int) ($review['owner_id'] ?? 0);
        $reviewId = (int) ($review['id'] ?? 0);

        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-start"));' . 'Alpine.$data(document.body).activeSection = "clients";' . ($ownerId > 0 ? 'setTimeout(() => window.dispatchEvent(new CustomEvent("open-client-profile", { detail: { clientId: ' . $ownerId . ', reviewId: ' . $reviewId . ' } })), 120);' : ''));
    }

    #[Computed]
    public function detailsBooking(): ?Booking
    {
        if (!$this->detailsBookingId) {
            return null;
        }

        return $this->bookingsQuery()
            ->with(['petOwner', 'pets'])
            ->pending()
            ->find($this->detailsBookingId);
    }

    #[Computed]
    public function confirmedBooking(): ?Booking
    {
        if (!$this->confirmedBookingId) {
            return null;
        }

        return $this->bookingsQuery()
            ->with(['petOwner', 'pets'])
            ->find($this->confirmedBookingId);
    }

    #[Computed]
    public function declineBooking(): ?Booking
    {
        if (!$this->declineBookingId) {
            return null;
        }

        return $this->bookingsQuery()
            ->with(['petOwner', 'pets'])
            ->find($this->declineBookingId);
    }

    #[Computed]
    public function rescheduleBooking(): ?Booking
    {
        if (!$this->rescheduleBookingId) {
            return null;
        }

        return $this->bookingsQuery()
            ->with(['petOwner', 'pets'])
            ->find($this->rescheduleBookingId);
    }

    #[Computed]
    public function weeklyRevenue(): array
    {
        return $this->getWeeklyRevenue();
    }

    #[Computed]
    public function earnedToday(): array
    {
        return $this->getEarnedToday();
    }

    #[Computed]
    public function nextAppointment(): ?array
    {
        return $this->getNextAppointment();
    }

    #[Computed]
    public function latestReview(): ?array
    {
        return $this->getLatestReview();
    }

    #[Computed]
    public function stats(): array
    {
        return $this->getStats();
    }

    #[Computed]
    public function todaysBookings(): array
    {
        return $this->getTodaysBookings();
    }

    #[Computed]
    public function todaysBookingsCount(): int
    {
        return $this->getTodaysBookingsCount();
    }

    #[Computed]
    public function pendingRequests(): array
    {
        return $this->getPendingRequests();
    }

    #[Computed]
    public function pendingRequestsCount(): int
    {
        return $this->getPendingRequestsCount();
    }

    #[Computed]
    public function upcomingBookings(): array
    {
        return $this->getUpcomingBookings();
    }

    #[Computed]
    public function upcomingBookingsCount(): int
    {
        return $this->getUpcomingBookingsCount();
    }
}; ?>

@php
    $activeColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
    $lightColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFE8E4' : '#FFF8EB';
    $chartColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
    $bookingItemHoverBg = auth()->check() && auth()->user()->user_type === 'space' ? '#FFF7F5' : '#FFFBF4';
    $overviewIsSpace = strtolower((string) (Auth::guard('groomer_spacer')->user()?->user_type ?? '')) === 'space';
@endphp

<div x-data="{ activeTab: @entangle('activeTab') }"
    class="business-hub-container{{ $overviewIsSpace ? ' is-space' : '' }}"
    wire:loading.class="business-hub-container--navigating"
    wire:target="openCardModal,closeCardModal,acceptRequest,viewDetails,closeDetailsModal,closeConfirmedModal,openDeclineFromOverview,closeDeclineModal,confirmDeclineBooking,openRescheduleFromDecline,openRescheduleModal,closeRescheduleModal,confirmRescheduleBookingFromClient,replyToLatestReview">

    <div class="bh-overview-loading-bridge" wire:loading.class="is-loading"
        wire:target="openCardModal,closeCardModal,acceptRequest,viewDetails,closeDetailsModal,closeConfirmedModal,openDeclineFromOverview,closeDeclineModal,confirmDeclineBooking,openRescheduleFromDecline,openRescheduleModal,closeRescheduleModal,confirmRescheduleBookingFromClient,replyToLatestReview"
        x-data
        x-init="let active = false;
        const sync = () => {
            const next = $el.classList.contains('is-loading');
            if (next === active) return;
            active = next;
            window.dispatchEvent(new CustomEvent('bh-overview-loading', { detail: { active } }));
        };
        new MutationObserver(sync).observe($el, { attributes: true, attributeFilter: ['class'] });"
        aria-hidden="true"></div>

    <style>
        :root {
            --business-hub-active:
                {{ $activeColor }};
            --business-hub-light:
                {{ $lightColor }};
            --business-hub-chart:
                {{ $chartColor }};
            --booking-item-hover-bg:
                {{ $bookingItemHoverBg }};
            --weekly-revenue-axis: #D9D5CF;
        }

        .business-hub-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 0 0 2.5rem;
            flex: 1 1 0;
            min-height: 0;
            width: 100%;
            align-self: stretch;
            position: relative;
        }

        .business-hub-container--navigating {
            cursor: wait;
        }

        .bh-overview-loading-bridge {
            display: none;
        }

        /* Dashboard Cards */
        .dashboard-card {
            padding: 0 1.25rem 1.25rem 1.25rem 0;
        }

        .card-header h3 {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 0 0 1rem 0;
            border-bottom: 1px solid #D4D4D4;
            padding-bottom: 1rem;
        }

        .weekly-revenue .card-header {
            padding-bottom: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .weekly-revenue .card-header h3 {
            margin-bottom: 0;
        }

        .count {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* Top Row Layout — Figma: 190 / 400 / 400 with 20px gaps */
        .dashboard-top-row {
            display: flex;
            flex: 0 1 auto;
            width: 100%;
            gap: 1.25rem;
            align-items: stretch;
            min-height: 0;
        }

        .dashboard-top-row>.dashboard-card {
            display: flex;
            flex-direction: column;
            align-self: stretch;
            min-height: 0;
            min-width: 0;
        }

        .dashboard-top-row>.dashboard-card>.card-header {
            flex-shrink: 0;
        }

        .card-content {
            max-height: 400px;
        }

        .dashboard-top-row>.dashboard-card>.card-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
            gap: 1rem;
        }

        .dashboard-top-row>.dashboard-card>.view-all-link {
            flex-shrink: 0;
        }

        .todays-bookings {
            flex: 190 1 0;
            max-width: 220px;
        }

        /* Booking Items */
        .booking-item {
            padding: 12px;
            border-radius: 10px;
            background-color: transparent;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .card-content .booking-item:hover,
        .card-modal-body .booking-item:hover {
            background: var(--booking-item-hover-bg, #FFFBF4);
        }

        .booking-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .pet-avatar {
            border: 2px solid var(--business-hub-active);
            padding: 2px;
            width: 40px;
            height: 40px;
            aspect-ratio: 1/1;
            border-radius: 50%;
            object-fit: cover;
        }

        .pet-avatar-wrap {
            position: relative;
            display: inline-flex;
            flex-shrink: 0;
            align-items: flex-start;
        }

        .pet-avatar-wrap .avatar-status-dot {
            position: absolute;
            top: 2px;
            right: 2px;
            z-index: 3;
        }

        .pet-avatar-wrap .pet-avatar-stack {
            position: relative;
            z-index: 1;
        }

        .pet-avatar-stack .pet-avatar-dot-wrap {
            position: relative;
            display: inline-flex;
            margin-top: -12px;
        }

        .pet-avatar-stack .pet-avatar-dot-wrap .avatar-status-dot {
            position: absolute;
            top: 1px;
            right: 1px;
            z-index: 3;
        }

        .pet-avatar-large {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pet-avatar-stack {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 50px;
        }

        .pet-avatar-stack .pet-avatar-large {
            width: 40px;
            height: 40px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            object-fit: cover;
            background: #fff;
            box-sizing: border-box;
        }

        .pet-avatar-stack .bh-avatar.pet-avatar-large.has-photo {
            border: 0;
            padding: 0;
            background: transparent;
            overflow: visible;
        }

        .pet-avatar-stack .bh-avatar.pet-avatar-large.is-fallback {
            background: var(--bh-avatar-bg, #FFC97A);
            border: 0;
            padding: 0;
        }

        .pet-avatar-stack .pet-avatar-large+.pet-avatar-large {
            margin-top: -12px;
        }

        .booking-item .pet-avatar-stack {
            flex-direction: row;
            align-items: center;
            min-width: auto;
        }

        .booking-item .pet-avatar-stack .pet-avatar-large+.pet-avatar-large {
            margin-top: 0;
            margin-left: -12px;
        }

        .service-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--business-hub-active);
            padding: 10px !important;
            height: 24px;
            border-radius: 100px;
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .bh-booking-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-row>span {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-transform: capitalize;
        }

        .divider {
            height: 1px;
            background: #F0EDE8;
            margin: 0.5rem 0;
        }

        .view-all-link {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
            margin-top: 1rem;
            text-align: center;
        }

        button.view-all-link {
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .card-modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: auto !important;
            height: auto !important;
            background: rgba(0, 0, 0, 0.42);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            overflow: hidden;
            z-index: 2147483000;
            animation: fadeInModal 0.25s ease;
        }

        .card-modal-shell {
            width: 100%;
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
            backdrop-filter: blur(5px);
        }

        .card-modal {
            width: min(40%, calc(100vw - 2rem));
            max-height: calc(100vh - 3rem);
            overflow: hidden;
            background: #fff;
            border-radius: 14px;
            border: 1px solid #ECE7DF;
            padding: 0;
            box-shadow: 0 24px 54px rgba(0, 0, 0, 0.18);
            animation: slideUpModal 0.3s ease;
            display: flex;
            flex-direction: column;
            font-family: Lato;
            color: #3B3731;
        }

        .card-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 2;
            background: #fff;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #EFEAE3;
        }

        .card-modal-title {
            color: #3B3731;
            font-family: Lato;
            font-size: 26px;
            font-weight: 700;
            line-height: normal;
        }

        .card-modal-close {
            border: none;
            background: transparent;
            color: #706A62;
            font-size: 30px;
            line-height: 1;
            cursor: pointer;
            padding: 0 0.25rem;
        }

        .card-modal-body {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1rem 1.25rem 1.25rem;
            overflow-y: auto;
            max-height: calc(100vh - 9rem);
            scrollbar-width: thin;
            scrollbar-color: #E3E3E3 transparent;
            font-family: inherit;
        }

        .card-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .card-modal-body::-webkit-scrollbar-button,
        .card-modal-body::-webkit-scrollbar-button:single-button,
        .card-modal-body::-webkit-scrollbar-button:double-button,
        .card-modal-body::-webkit-scrollbar-button:vertical:decrement,
        .card-modal-body::-webkit-scrollbar-button:vertical:increment,
        .card-modal-body::-webkit-scrollbar-button:horizontal:decrement,
        .card-modal-body::-webkit-scrollbar-button:horizontal:increment {
            display: none;
            width: 0;
            height: 0;
        }

        .card-modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .card-modal-body::-webkit-scrollbar-thumb {
            background: #E3E3E3;
            border-radius: 12px;
            border: 0;
        }

        .card-modal-body::-webkit-scrollbar-thumb:hover {
            background: #E3E3E3;
        }

        html.no-page-scroll,
        body.no-page-scroll {
            overflow: hidden !important;
            height: 100%;
        }

        @keyframes fadeInModal {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUpModal {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Weekly Revenue */
        .weekly-revenue {
            flex: 400 1 0;
        }

        .pending-requests {
            flex: 400 1 0;
        }

        .weekly-revenue .card-content {
            flex: 0 1 auto;
            padding: 0.5rem 0;
        }

        .revenue-header {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .revenue-amount {
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .revenue-period {
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
        }

        .revenue-change {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 4px;
            font-family: Lato;
            font-size: 10px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            letter-spacing: 0.1px;
            border-radius: 74px;
            padding: 4px 10px;
        }

        .revenue-change.positive {
            background: rgba(209, 235, 154, 0.20);
            color: #AFCD6F;
        }

        .weekly-revenue .chart-container {
            position: relative;
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
            width: 100%;
        }

        .weekly-revenue .weekly-chart-js-wrap {
            position: relative;
            width: 100%;
            min-width: 0;
            height: 280px;
            max-height: 320px;
            border-radius: 10px;
            overflow: hidden;
        }

        .weekly-revenue .revenue-chart {
            display: block;
            width: 100%;
            height: 280px;
            max-height: 320px;
            vertical-align: bottom;
            box-sizing: border-box;
        }

        .request-item {
            padding: 1rem;
            border-radius: 10px;
            background-color: transparent;
            transition: background-color 0.2s ease;
        }

        .card-content .request-item:hover,
        .card-modal-body .request-item:hover {
            background: #F6F6F6;
        }

        .request-item>div:first-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .request-info-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .request-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .business-hub-pet-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .request-details {
            display: flex !important;
            justify-content: space-between;
            gap: 1rem;
        }

        .price {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .request-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-accept {
            flex: 1;
            width: 170px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 75px;
            background: #C9DDA0;
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-accept:hover {
            background: color-mix(in srgb, #C9DDA0 80%, white);
        }

        .btn-view {
            flex: 1;
            width: 170px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 75px;
            background: #D9D9D9;
            color: #706A62;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: #E5E2DD;
        }

        /* Bottom Row — Figma: 610 / 400 with 20px gap.
           This Month lines up with Upcoming booking items, not the section title. */
        .dashboard-bottom-row {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: minmax(0, 610fr) minmax(0, 400fr);
            grid-template-rows: auto auto auto;
            column-gap: 1.25rem;
            align-items: start;
        }

        .upcoming-bookings {
            display: grid;
            grid-template-rows: subgrid;
            grid-row: 1 / 4;
            min-width: 0;
        }

        .upcoming-bookings .card-content {
            max-height: none;
            overflow-y: auto;
        }

        .upcoming-bookings>.view-all-link {
            flex-shrink: 0;
            margin-top: 0.75rem;
        }

        .stats-column {
            grid-column: 2;
            grid-row: 2;
            min-width: 0;
            align-self: stretch;
        }

        /* Upcoming Bookings */
        .upcoming-booking-item {
            border: 1px solid #D8E8B7;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: background-color 0.2s ease;
        }

        /* Flex modal body would otherwise shrink these (overflow:hidden → min-height:0) and clip details */
        .card-modal-body .upcoming-booking-item {
            flex-shrink: 0;
            margin-bottom: 0;
        }

        .card-modal-body .booking-body {
            flex-wrap: wrap;
            row-gap: 0.75rem;
        }

        .card-modal-body .booking-body-divider {
            flex-shrink: 0;
        }

        .upcoming-bookings .upcoming-booking-item:last-child {
            margin-bottom: 0;
        }

        .card-content .upcoming-booking-item:hover,
        .card-modal-body .upcoming-booking-item:hover {
            background: #F6F6F6;
        }

        .booking-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #F5F9ED;
            border-radius: 10px 10px 0 0;
            padding: 1rem 2rem;
            border: 1px solid #D8E8B7;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            text-align: right;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .status-badge.confirmed {
            color: #B5CA89;
        }

        .booking-id {
            color: #3B3731;
            text-align: right;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .booking-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.5rem 2rem;
        }

        .booking-body-divider {
            width: 1px;
            height: 54px;
            background: #D4D4D4;
        }

        .pet-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            /* min-width: 120px; */
        }

        .pet-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .pet-count {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .pet-types {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .booking-info-grid {
            display: flex;
            gap: 1.5rem;
        }

        .info-cell {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .info-value {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .booking-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-navigate {
            background: #C9DDA0;
        }

        .btn-navigate:hover {
            background: #B5CA89;
        }

        .btn-message {
            background: #CBDCE8;
        }

        .btn-message:hover {
            background: #A5B8D1;
        }

        /* Stats Column — single “This Month” card */
        .this-month-card {
            border-radius: 10px;
            border: 1px solid #E6E6E6;
            background: #FFF;
            padding: 1.25rem 1.25rem 1.5rem;
            box-sizing: border-box;
            height: 100%;
        }

        .this-month-card>.card-header h3 {
            margin: 0 0 1.25rem;
            padding-bottom: 0;
            border-bottom: none;
        }

        .this-month-rows {
            display: flex;
            flex-direction: column;
        }

        .this-month-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #E6E6E6;
        }

        .this-month-row:first-child {
            padding-top: 0;
        }

        .this-month-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .this-month-row .stat-icon {
            position: static;
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .this-month-row .stat-icon svg {
            width: 56px;
            height: 56px;
        }

        .this-month-row .stat-copy {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .this-month-row .stat-copy h4 {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 0;
        }

        .this-month-row .stat-main {
            display: flex;
            align-items: baseline;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .stat-value {
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .stat-label {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            max-width: 100%;
            padding: 0.5rem 0.65rem !important;
            height: auto;
            min-height: 28px;
            border-radius: 74px;
            font-family: Lato;
            font-size: 12px !important;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            letter-spacing: 0.1px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .stat-change.positive {
            background: rgba(209, 235, 154, 0.20);
            color: #AFCD6F;
        }

        .stat-change.avg-revenue,
        .stat-change.profile-views {
            background: #E5EEF4;
            color: #A4CAE4;
        }

        .stat-change.repeat-clients {
            background: rgba(255, 168, 153, 0.25);
            color: #FFA899;
        }

        .stat-sublabel {
            font-size: 11px;
            color: #9B958C;
            background: #F0EDE8;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            display: inline-block;
            width: fit-content;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-top-row {
                flex-direction: column;
            }

            .todays-bookings,
            .weekly-revenue,
            .pending-requests {
                flex: 1 1 auto;
                max-width: none;
                width: 100%;
            }

            .dashboard-bottom-row {
                grid-template-columns: 1fr;
                grid-template-rows: none;
            }

            .upcoming-bookings {
                display: flex;
                flex-direction: column;
                grid-row: auto;
            }

            .stats-column {
                grid-column: auto;
                grid-row: auto;
            }

            .booking-info-grid {
                flex-direction: column;
                gap: 0.75rem;
            }

            .booking-body {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .request-actions {
                flex-direction: column;
            }

            .this-month-row {
                flex-wrap: wrap;
            }

            .stat-change {
                white-space: normal;
            }
        }

        /* Overview redesign (Figma dashboard) */
        .bh-overview {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            max-width: 1030px;
        }

        .bh-metrics {
            display: grid;
            grid-template-columns: 242px minmax(0, 1fr) minmax(0, 1fr);
            gap: 20px;
        }

        .bh-metric-card {
            border-radius: 10px;
            border: 1px solid #E2E2E2;
            background: #fff;
            padding: 20px;
            height: 140px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0;
            position: relative;
            overflow: hidden;
        }

        .bh-metric-card.is-earned {
            border-color: #C9DDA0;
            background:
                linear-gradient(-44.193070073608deg,
                    rgba(201, 221, 160, 0) 16.58%,
                    rgba(201, 221, 160, 0.35) 89.896%),
                #fff;
        }

        .bh-metric-card.is-earned .bh-metric-label-row {
            gap: 10px;
            padding-right: 0;
            align-items: center;
        }

        .bh-metric-card.is-earned .bh-metric-label-icon {
            background: transparent;
            width: 30px;
            height: 30px;
        }

        .bh-metric-card.is-earned .bh-metric-label-icon img {
            width: 30px;
            height: 30px;
            display: block;
        }

        .bh-metric-card.is-earned .bh-metric-value {
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-metric-card.is-earned .bh-metric-badge {
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: normal;
        }

        .bh-metric-card.is-next {
            border-color: #CBDCE8;
            background:
                linear-gradient(-32.17deg, rgba(203, 220, 232, 0) 16.58%, rgb(203, 220, 232) 89.9%),
                #fff;
        }

        .bh-metric-card.is-review {
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
        }

        .bh-metric-label-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-right: 90px;
            flex-shrink: 0;
        }

        .bh-metric-label-icon {
            width: 30px;
            height: 30px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.65);
        }

        .bh-metric-label-icon img {
            width: 16px;
            height: 16px;
        }

        .bh-metric-label {
            margin: 0;
            color: #565149;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

        .bh-metric-value {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-weight: 600;
            line-height: 1.15;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .bh-metric-card.is-next .bh-metric-value,
        .bh-metric-card.is-review .bh-metric-value {
            font-size: 30px;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-metric-card.is-next .bh-metric-label,
        .bh-metric-card.is-review .bh-metric-label {
            padding-right: 110px;
        }

        .bh-metric-value.is-muted {
            font-size: 22px;
            color: #9D9B98;
        }

        .bh-metric-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            max-width: 100%;
            min-height: 28px;
            padding: 6px 12px;
            border-radius: 74px;
            background: rgba(201, 221, 160, 0.2);
            color: #8BAE40;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.12px;
            flex-shrink: 0;
        }

        .bh-metric-sub {
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            max-width: 100%;
            height: 28px;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 74px;
            background: rgba(255, 255, 255, 0.5);
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            line-height: normal;
            letter-spacing: 0.12px;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .bh-metric-card.is-review .bh-metric-sub {
            background: rgba(226, 226, 226, 0.2);
        }

        .bh-metric-link {
            position: absolute;
            top: 26px;
            right: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.12px;
            cursor: pointer;
            padding: 0;
        }

        .bh-metric-link::after {
            content: "";
            width: 5px;
            height: 5px;
            border-right: 1.5px solid currentColor;
            border-top: 1.5px solid currentColor;
            transform: translateX(0) rotate(45deg);
            transition: transform 0.2s ease;
        }

        .bh-metric-card.is-next:hover .bh-metric-link::after,
        .bh-metric-card.is-review:hover .bh-metric-link::after,
        .bh-metric-link:hover::after,
        .bh-metric-link:focus-visible::after {
            transform: translateX(3px) rotate(45deg);
        }

        @media (prefers-reduced-motion: reduce) {
            .bh-metric-link::after {
                transition: none;
            }
        }

        .bh-metric-review-title {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .bh-metric-star {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .bh-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 20px;
            align-items: stretch;
        }

        .bh-main-left {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .bh-panel {
            border-radius: 10px;
            border: 1px solid #E2E2E2;
            background: #FFF;
            padding: 18px 20px 20px;
            box-sizing: border-box;
            width: 100%;
        }

        .bh-schedule {
            min-height: 241px;
            height: 241px;
            padding: 20px;
        }

        .bh-schedule .bh-panel-header {
            margin-bottom: 12px;
        }

        .bh-weekly {
            height: 260px;
            min-height: 260px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .bh-weekly .bh-panel-header {
            margin-bottom: 20px;
            flex-shrink: 0;
        }

        .bh-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .bh-panel-header h3 {
            margin: 0;
            color: #565149;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
        }

        .bh-panel-header h3 .count {
            color: #9D9B98;
            font-weight: 600;
        }

        .bh-panel-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.12px;
            cursor: pointer;
            padding: 0;
            text-decoration: none;
            white-space: nowrap;
        }

        .bh-panel-link::after {
            content: "";
            width: 5px;
            height: 5px;
            border-right: 1.5px solid currentColor;
            border-top: 1.5px solid currentColor;
            transform: translateX(0) rotate(45deg);
            transition: transform 0.2s ease;
        }

        .bh-schedule:hover .bh-panel-link::after,
        .bh-weekly:hover .bh-panel-link::after,
        .bh-pending-panel:hover .bh-panel-link::after,
        .bh-panel-link:hover::after,
        .bh-panel-link:focus-visible::after {
            transform: translateX(3px) rotate(45deg);
        }

        @media (prefers-reduced-motion: reduce) {
            .bh-panel-link::after {
                transition: none;
            }
        }

        .bh-schedule-list {
            display: flex;
            flex-direction: column;
        }

        .bh-schedule-row {
            display: grid;
            grid-template-columns: 43px 1px minmax(0, 1fr);
            column-gap: 20px;
            align-items: center;
            min-height: 60px;
            height: 60px;
            padding: 0 10px;
            border-radius: 10px;
            background: transparent;
            box-sizing: border-box;
            transition: background-color 0.15s ease;
            cursor: pointer;
        }

        .bh-schedule-row:hover {
            background: #F8FAF5;
            cursor: pointer;
        }

        .bh-schedule-time {
            width: 43px;
            text-align: left;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #3B3731;
            line-height: normal;
            white-space: nowrap;
        }

        .bh-schedule-time span {
            display: block;
            color: #9D9B98;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
        }

        .bh-schedule-rule {
            width: 1px;
            height: 40px;
            align-self: center;
            justify-self: center;
            background: #C9DDA0;
            border-radius: 1px;
        }

        .bh-schedule-body {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            justify-content: center;
        }

        .bh-schedule-title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .bh-schedule-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
        }

        .bh-visit-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            min-width: 0;
            padding: 0 10px;
            border-radius: 100px;
            background: rgba(255, 201, 122, 0.1);
            color: #FFC97A;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            box-sizing: border-box;
        }

        .business-hub-container.is-space .bh-visit-badge {
            background: rgba(255, 168, 153, 0.15);
            color: #FFA899;
        }

        .bh-schedule-meta {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bh-weekly .revenue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
            flex-shrink: 0;
        }

        .bh-weekly .revenue-summary {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
            min-width: 0;
        }

        .bh-weekly .revenue-amount {
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-weight: 600;
            line-height: normal;
        }

        .bh-weekly .revenue-period {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            text-decoration: none;
        }

        .bh-weekly .revenue-change {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 28px;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 74px;
            background: rgba(201, 221, 160, 0.2);
            color: #8BAE40;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.12px;
            box-sizing: border-box;
        }

        .bh-weekly .revenue-change-icon {
            width: 8px;
            height: 12px;
            flex-shrink: 0;
            transform: rotate(45deg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: inherit;
        }

        .bh-weekly .revenue-change-icon svg {
            display: block;
            width: 6px;
            height: 9px;
        }

        .bh-weekly .revenue-change.is-neg {
            background: rgba(255, 110, 110, 0.12);
            color: #FF6E6E;
        }

        .bh-weekly .revenue-change.is-neg .revenue-change-icon {
            transform: rotate(135deg);
        }

        .bh-weekly .chart-container {
            flex: 1;
            min-height: 0;
            height: auto;
            display: flex;
            flex-direction: column;
        }

        .bh-weekly .weekly-chart-js-wrap,
        .bh-weekly .revenue-chart {
            width: 100%;
            height: 100%;
            min-height: 110px;
        }

        .bh-pending-panel {
            min-height: 521px;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 20px;
        }

        .bh-pending-panel .bh-panel-header {
            margin-bottom: 20px;
        }

        .bh-pending-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
            overflow: auto;
            padding-right: 2px;
        }

        .bh-pending-card {
            border: 0;
            border-radius: 10px;
            background: #F9F9F9;
            height: 134px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 10px;
            box-sizing: border-box;
            box-shadow: none;
            overflow: hidden;
        }

        .bh-pending-top {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
        }

        .bh-pending-copy {
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1px;
        }

        .bh-pending-service {
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .bh-pending-service img {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }

        .bh-pending-client-row {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 10px;
            min-height: 24px;
        }

        .bh-pending-client {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .bh-tier {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            padding: 0 10px;
            border-radius: 100px;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
            white-space: nowrap;
            box-sizing: border-box;
        }

        .bh-tier.is-new {
            background: rgba(186, 207, 142, 0.1);
            color: #AFCD6F;
        }

        .bh-tier.is-repeat {
            background: #F7FAFC;
            color: #94BEDB;
        }

        .bh-tier.is-regular {
            background: #FDF8F0;
            color: #F9C45C;
        }

        .bh-pending-price {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
            line-height: normal;
            flex-shrink: 0;
        }

        .bh-pending-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: nowrap;
            min-height: 16px;
        }

        .bh-pending-meta {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 20px;
            min-width: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
        }

        .bh-pending-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #9D9B98;
            line-height: normal;
            white-space: nowrap;
        }

        .bh-pending-meta span:first-child {
            gap: 5px;
            color: white;
        }

        .bh-pending-meta>span>img {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }

        .bh-pending-meta .bh-pending-date-icon {
            width: 13px;
            height: 13px;
        }

        .bh-pending-meta .bh-pending-pet-avatar {
            width: 16px;
            height: 16px;
            border-radius: 199.5px;
            overflow: hidden;
            flex-shrink: 0;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            background: transparent;
            border: 0;
            padding: 0;
            box-shadow: none;
        }

        .bh-pending-meta .bh-pending-pet-avatar.is-fallback {
            background: var(--bh-avatar-bg, #FFC97A);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bh-pending-meta .bh-pending-pet-avatar img {
            width: 16px !important;
            height: 16px !important;
            max-width: none !important;
            margin: 0 !important;
            object-fit: cover;
            object-position: center;
            border-radius: 199.5px;
            display: block;
        }

        .bh-pending-meta .bh-pending-pet-avatar__initials {
            color: #FDFDFD;
            font-family: Lato, sans-serif;
            font-size: 8px;
            font-weight: 800;
            line-height: 1;
        }

        .bh-expires {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #FFA899;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
            margin: 0;
            background: transparent;
            padding: 0;
            border: 0;
            flex-shrink: 0;
        }

        .bh-expires svg {
            width: 8px;
            height: 12px;
            flex-shrink: 0;
            display: block;
        }

        .bh-pending-actions {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        .bh-btn-details,
        .bh-btn-accept {
            height: 36px;
            border-radius: 75px;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            line-height: normal;
            padding: 0;
        }

        .bh-btn-details {
            flex: 0 0 129px;
            width: 129px;
            border: 1px solid #E2E2E2;
            background: #fff;
            color: #9D9B98;
        }

        .bh-btn-accept {
            flex: 1 1 auto;
            min-width: 0;
            border: 0;
            background: #C9DDA0;
            color: #fff;
        }

        .bh-empty {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            padding: 12px 0;
        }

        @media (max-width: 1100px) {
            .bh-overview {
                max-width: none;
            }

            .bh-metrics {
                grid-template-columns: 1fr;
            }

            .bh-main-grid {
                grid-template-columns: 1fr;
            }

            .bh-pending-panel {
                min-height: 0;
            }
        }
    </style>

    @php
        $isSpaceAccount = $this->isSpaceAccount();
        $earnedToday = $this->earnedToday;
        $nextAppointment = $this->nextAppointment;
        $latestReview = $this->latestReview;
        $weeklyRevenue = $this->weeklyRevenue;
        $changeAmount = (float) ($weeklyRevenue['change_amount'] ?? ($weeklyRevenue['change'] ?? 0));
    @endphp

    <div class="bh-overview">
        <div class="bh-metrics">
            <article class="bh-metric-card is-earned">
                <div class="bh-metric-label-row">
                    <span class="bh-metric-label-icon" aria-hidden="true">
                        <img src="{{ asset('images/business-hub/icon-earned-today-calendar.svg') }}" alt="" width="30"
                            height="30">
                    </span>
                    <p class="bh-metric-label">Earned today</p>
                </div>
                <p class="bh-metric-value">£{{ number_format((float) $earnedToday['amount'], 2) }}</p>
                <span class="bh-metric-badge">{{ $earnedToday['badge'] }}</span>
            </article>

            <article class="bh-metric-card is-next">
                <p class="bh-metric-label">Next appointment</p>
                @if ($nextAppointment)
                    <button type="button" class="bh-metric-link"
                        onclick="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); Alpine.$data(document.body).activeSection = 'bookings'; window.dispatchEvent(new CustomEvent('booking-status-intent', { detail: { status: 'confirmed' } }));">View details</button>
                    <p class="bh-metric-value">{{ $nextAppointment['when'] }}</p>
                    <p class="bh-metric-sub">{{ $nextAppointment['subtitle'] }}</p>
                @else
                    <p class="bh-metric-value is-muted">No upcoming</p>
                    <p class="bh-metric-sub">Your next confirmed booking will show here.</p>
                @endif
            </article>

            <article class="bh-metric-card is-review">
                <p class="bh-metric-label">New review</p>
                @if ($latestReview)
                    <button type="button" class="bh-metric-link" wire:click="replyToLatestReview">Reply to review</button>
                    <div class="bh-metric-review-title">
                        <p class="bh-metric-value">{{ $latestReview['owner_first'] }} left you</p>
                        <img class="bh-metric-star" src="{{ asset('images/business-hub/icon-profile-star.svg') }}" alt=""
                            width="15" height="15">
                        <p class="bh-metric-value">{{ $latestReview['rating'] }}</p>
                    </div>
                    <p class="bh-metric-sub">{{ $latestReview['quote'] }}</p>
                @else
                    <p class="bh-metric-value is-muted">No reviews yet</p>
                    <p class="bh-metric-sub">New client reviews will appear here.</p>
                @endif
            </article>
        </div>

        <div class="bh-main-grid">
            <div class="bh-main-left">
                <section class="bh-panel bh-schedule">
                    <div class="bh-panel-header">
                        <h3>Today’s schedule <span class="count">({{ $this->todaysBookingsCount }})</span></h3>
                        <button type="button" class="bh-panel-link"
                            onclick="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); Alpine.$data(document.body).activeSection = 'availability';">Availability calendar</button>
                    </div>
                    <div class="bh-schedule-list">
                        @forelse ($this->todaysBookings as $booking)
                            @php
                                $visitLabel = trim((string) ($booking['service_type'] ?? ''));
                                $showVisitBadge = $visitLabel !== '' && $visitLabel !== '—';
                            @endphp
                            <div class="bh-schedule-row">
                                <div class="bh-schedule-time">
                                    {{ $booking['time'] }}
                                    <span>{{ $booking['duration'] }}</span>
                                </div>
                                <div class="bh-schedule-rule" aria-hidden="true"></div>
                                <div class="bh-schedule-body">
                                    <div class="bh-schedule-title-row">
                                        <p class="bh-schedule-title">{{ $booking['service'] }}</p>
                                        @if ($showVisitBadge)
                                            <span class="bh-visit-badge">{{ $visitLabel }}</span>
                                        @endif
                                    </div>
                                    <p class="bh-schedule-meta">{{ $booking['pet_owner_line'] }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="bh-empty">No appointments on today’s schedule.</p>
                        @endforelse
                    </div>
                </section>

                <section class="bh-panel bh-weekly weekly-revenue">
                    <div class="bh-panel-header">
                        <h3>Weekly revenue</h3>
                        <button type="button" class="bh-panel-link"
                            onclick="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); Alpine.$data(document.body).activeSection = 'earnings'; window.dispatchEvent(new CustomEvent('earnings-menu-selected', { detail: { menu: 'overview' } }));">Earnings</button>
                    </div>
                    <div class="revenue-header">
                        <div class="revenue-summary">
                            <span class="revenue-amount">£{{ number_format((float) $weeklyRevenue['total'], 2) }}</span>
                            <span class="revenue-period">this week</span>
                        </div>
                        <span class="revenue-change{{ $changeAmount < 0 ? ' is-neg' : '' }}">
                            <span class="revenue-change-icon" aria-hidden="true">
                                <svg width="6" height="9" viewBox="0 0 5.8 8.93" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.91 0L5.8 2.895L5.415 3.265C5.33833 3.34167 5.26167 3.37333 5.185 3.36C5.105 3.34333 5.02833 3.3 4.955 3.23L3.74 2.005C3.65333 1.91833 3.575 1.835 3.505 1.755C3.43167 1.675 3.36667 1.59833 3.31 1.525C3.33333 1.72167 3.35333 1.92833 3.37 2.145C3.38333 2.35833 3.39 2.575 3.39 2.795L3.39 8.93H2.415L2.415 2.795C2.415 2.575 2.42333 2.35667 2.44 2.14C2.45333 1.92333 2.47333 1.71667 2.5 1.52C2.44333 1.59667 2.38 1.675 2.31 1.755C2.23667 1.835 2.15667 1.91833 2.07 2.005L0.845 3.24C0.775 3.31 0.7 3.35333 0.62 3.37C0.54 3.38333 0.461667 3.35167 0.385 3.275L0 2.905L2.91 0Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            @if ($changeAmount >= 0)
                                +£{{ number_format(abs($changeAmount), 0) }} vs last week
                            @else
                                -£{{ number_format(abs($changeAmount), 0) }} vs last week
                            @endif
                        </span>
                    </div>
                    <div class="chart-container">
                        <div class="weekly-chart-js-wrap" wire:ignore x-data x-init="const bootWeeklyRevenueChart = () => {
                            const canvas = $el.querySelector('#weeklyRevenueChart');
                            if (canvas && window.mountWeeklyRevenueChart) {
                                window.mountWeeklyRevenueChart(canvas);
                                return;
                            }
                            window.scheduleWeeklyRevenueChartInit?.();
                        };
                        $nextTick(() => {
                            bootWeeklyRevenueChart();
                            requestAnimationFrame(bootWeeklyRevenueChart);
                            setTimeout(bootWeeklyRevenueChart, 200);
                            setTimeout(bootWeeklyRevenueChart, 600);
                        });">
                            <canvas id="weeklyRevenueChart" class="revenue-chart" role="img"
                                aria-label="Weekly revenue chart" data-fill="{{ $chartColor }}"
                                data-labels='@json(array_column($weeklyRevenue['chart_data'], 'date'))'
                                data-values='@json(array_column($weeklyRevenue['chart_data'], 'amount'))'></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <section class="bh-panel bh-pending-panel pending-requests">
                <div class="bh-panel-header">
                    <h3>Pending Requests <span class="count">({{ $this->pendingRequestsCount }})</span></h3>
                    <button type="button" class="bh-panel-link" wire:click.prevent="openCardModal('pending')">View all pending requests</button>
                </div>
                <div class="bh-pending-list">
                    @forelse ($this->pendingRequests as $request)
                        <article class="bh-pending-card">
                            <div class="bh-pending-top">
                                <x-business-hub.common.avatar :name="$request['owner_name']" :src="$request['owner_photo']"
                                    :size="42" :font-size="16" />
                                <div class="bh-pending-copy">
                                    <p class="bh-pending-service">
                                        <img src="{{ asset('images/business-hub/icon-reschedule-service.svg') }}" alt=""
                                            width="12" height="12">
                                        {{ $request['service'] }} · {{ $request['pet_types_label'] }}
                                    </p>
                                    <div class="bh-pending-client-row">
                                        <span class="bh-pending-client">{{ $request['owner_name'] }}</span>
                                        <span class="bh-tier is-{{ $request['client_tier_tone'] }}">{{ $request['client_tier'] }}</span>
                                    </div>
                                </div>
                                <p class="bh-pending-price">£{{ number_format((float) $request['price'], 2) }}</p>
                            </div>
                            <div class="bh-pending-meta-row">
                                <div class="bh-pending-meta">
                                    <span>
                                        @php
                                            $pendingPetAvatar = $request['pet_avatars'][0] ?? null;
                                            $pendingPetPhoto = $pendingPetAvatar['photo'] ?? null;
                                            $pendingPetName = $pendingPetAvatar['name'] ?? 'Pet';
                                            $pendingPetInitials = \App\Support\BusinessHubAvatar::initials($pendingPetName);
                                        @endphp
                                        @if ($pendingPetPhoto)
                                            <span class="bh-pending-pet-avatar has-photo" aria-hidden="true">
                                                <img src="{{ $pendingPetPhoto }}" alt="" width="16" height="16"
                                                    loading="lazy" decoding="async"
                                                    onerror="this.hidden=true; this.nextElementSibling.hidden=false; this.parentElement.classList.remove('has-photo'); this.parentElement.classList.add('is-fallback');">
                                                <span class="bh-pending-pet-avatar__initials" hidden>{{ $pendingPetInitials }}</span>
                                            </span>
                                        @else
                                            <span class="bh-pending-pet-avatar is-fallback" aria-hidden="true">
                                                <span class="bh-pending-pet-avatar__initials">{{ $pendingPetInitials }}</span>
                                            </span>
                                        @endif
                                        <span style="color: #3B3731;">{{ $request['pet_label'] }}</span>
                                    </span>
                                    <span>
                                        <img class="bh-pending-date-icon" src="{{ asset('images/business-hub/icon-pending-calendar.svg') }}" alt="" width="13" height="13">
                                        {{ $request['date'] }}
                                    </span>
                                    <span>
                                        <img src="{{ asset('images/business-hub/icon-pending-clock.svg') }}" alt="" width="12" height="12">
                                        {{ $request['time'] }}
                                    </span>
                                </div>
                                <p class="bh-expires">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 9 13" fill="none" aria-hidden="true">
                                        <path d="M0.5 0.5H8.5" stroke="#FFA899" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M0.5 12.5H8.5" stroke="#FFA899" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M1.16707 0.5V3.16667L4.5004 6.5L7.83373 3.16667V0.5" stroke="#FFA899" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M1.16707 12.5V9.83333L4.5004 6.5L7.83373 9.83333V12.5" stroke="#FFA899" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    {{ $request['expires_in'] }}
                                </p>
                            </div>
                            <div class="bh-pending-actions">
                                <button type="button" class="bh-btn-details"
                                    wire:click="viewDetails({{ (int) $request['booking_id'] }})">View Details</button>
                                <button type="button" class="bh-btn-accept"
                                    wire:click="acceptRequest({{ (int) $request['booking_id'] }})">Accept Request</button>
                            </div>
                        </article>
                    @empty
                        <p class="bh-empty">No pending requests right now.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div wire:key="pending-detail-{{ $this->detailsBookingId ?? 'closed' }}">
        <x-business-hub.common.pending-request-modal :booking="$this->detailsBooking" />
    </div>

    <div wire:key="booking-confirmed-{{ $this->confirmedBookingId ?? 'closed' }}">
        <x-business-hub.common.booking-confirmed-modal :booking="$this->confirmedBooking" />
    </div>

    <x-business-hub.common.decline-modal :decline-booking="$this->declineBooking" />

    <x-business-hub.common.reschedule-modal :reschedule-booking="$this->rescheduleBooking"
        :bookings="$rescheduleCalendarBookings" :reschedule-selected-date="$rescheduleSelectedDate"
        :reschedule-selected-time="$rescheduleSelectedTime" :reschedule-calendar-month="$rescheduleCalendarMonth"
        :reschedule-duration-minutes="$rescheduleDurationMinutes" />

    @if ($activeModal)
        @teleport('body')
            <div class="card-modal-backdrop" wire:click="closeCardModal" wire:keydown.escape.window="closeCardModal">
                <div class="card-modal-shell">
                    <div class="card-modal" wire:click.stop>
                        <div class="card-modal-header">
                            <h3 class="card-modal-title">
                                @if ($activeModal === 'todays')
                                    Today's Bookings
                                @elseif ($activeModal === 'pending')
                                    Pending Requests
                                @else
                                    Upcoming Bookings
                                @endif
                            </h3>
                            <button type="button" class="card-modal-close" wire:click="closeCardModal"
                                aria-label="Close modal">&times;</button>
                        </div>

                        <div class="card-modal-body">
                            @if ($activeModal === 'todays')
                                @foreach ($this->todaysBookings as $booking)
                                    <div class="booking-item">
                                        <div class="booking-header">
                                            <div class="pet-avatar-stack">
                                                @foreach ($booking['pet_avatars'] ?? [] as $petAvatar)
                                                    <x-business-hub.common.avatar :name="$petAvatar['name']" :src="$petAvatar['photo']" class="pet-avatar-large" :size="40" :font-size="16" />
                                                @endforeach
                                            </div>
                                            <span
                                                class="service-badge">{{ $isSpaceAccount ? 'Garden / Shed' : $booking['service_type'] }}</span>
                                        </div>
                                        <div class="bh-booking-details">
                                            @if ($isSpaceAccount)
                                                <div class="detail-row">
                                                    <span>{{ $booking['date'] }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span>{{ $booking['time'] }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <span>{{ $booking['pet_type'] }} <span class="black-dot"></span>
                                                        {{ $booking['weight'] }}</span>
                                                </div>
                                            @else
                                                <div class="detail-row">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="14" height="14"
                                                        fill="none">
                                                        <circle cx="50" cy="50" r="42" stroke="#2f2c28" stroke-width="7" />
                                                        <line x1="50" y1="50" x2="50" y2="28" stroke="#2f2c28" stroke-width="8"
                                                            stroke-linecap="round" />
                                                        <line x1="50" y1="50" x2="70" y2="70" stroke="#2f2c28" stroke-width="8"
                                                            stroke-linecap="round" />
                                                        <circle cx="50" cy="50" r="3.5" fill="#2f2c28" />
                                                    </svg>
                                                    <span>{{ $booking['time'] }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16"
                                                        fill="none">
                                                        <path
                                                            d="M4.69389 10.9529C5.90917 12.1682 8.8649 11.1832 11.2955 8.7522C13.7264 6.32163 14.7114 3.36588 13.4961 2.1506M8.26971 1.3251L8.81977 1.87556M6.34448 3.25073L6.89454 3.8008M4.6935 5.4514L5.24357 6.00147M4.14343 8.20213L4.6935 8.7522M11.2955 0.5L11.8455 1.05007M10.7454 3.80119L11.8455 4.90133M8.82016 5.72682L9.92029 6.82696M6.61951 7.37703L7.71964 8.47717"
                                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M4.6929 12.6031C5.14866 12.1474 5.14866 11.4084 4.6929 10.9527C4.23714 10.4969 3.49821 10.4969 3.04245 10.9527L0.841854 13.1533C0.386096 13.609 0.386096 14.348 0.841854 14.8037C1.29761 15.2595 2.03654 15.2595 2.4923 14.8037L4.6929 12.6031Z"
                                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $booking['service'] }}</span>
                                                </div>
                                                <div class="detail-row">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                                        fill="none">
                                                        <path
                                                            d="M7.5 5.63158C5.38867 5.63158 3.60467 7.52376 3.02867 9.95408C2.77533 11.0228 3.15733 12.1572 4.095 12.6902C4.83833 13.1127 5.944 13.5 7.5 13.5C9.056 13.5 10.162 13.1127 10.9053 12.6902C11.843 12.1572 12.2247 11.0228 11.9713 9.95408C11.3953 7.52342 9.61133 5.63158 7.5 5.63158ZM0.5 5.09926C0.5 6.04416 1.09667 7 1.83333 7C2.57 7 3.16667 6.04416 3.16667 5.09926C3.16667 4.15437 2.57 3.57895 1.83333 3.57895C1.09667 3.57895 0.5 4.15471 0.5 5.09926ZM14.5 5.09926C14.5 6.04416 13.9033 7 13.1667 7C12.43 7 11.8333 6.04416 11.8333 5.09926C11.8333 4.15437 12.43 3.57895 13.1667 3.57895C13.9033 3.57895 14.5 4.15471 14.5 5.09926ZM4 2.02032C4 2.96521 4.59667 3.92105 5.33333 3.92105C6.07 3.92105 6.66667 2.96521 6.66667 2.02032C6.66667 1.07542 6.07 0.5 5.33333 0.5C4.59667 0.5 4 1.07576 4 2.02032ZM11 2.02032C11 2.96521 10.4033 3.92105 9.66667 3.92105C8.93 3.92105 8.33333 2.96521 8.33333 2.02032C8.33333 1.07542 8.93 0.5 9.66667 0.5C10.4033 0.5 11 1.07576 11 2.02032Z"
                                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $booking['pet_type'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="divider"></div>
                                    @endif
                                @endforeach
                            @elseif ($activeModal === 'pending')
                                @foreach ($this->pendingRequests as $request)
                                    <div class="request-item">
                                        <div>
                                            <div class="pet-avatar-wrap">
                                                <div class="pet-avatar-stack">
                                                    @foreach ($request['pet_avatars'] ?? [] as $petAvatar)
                                                        <div class="pet-avatar-dot-wrap">
                                                            <x-business-hub.common.avatar :name="$petAvatar['name']" :src="$petAvatar['photo']" class="pet-avatar-large" :size="40" :font-size="16" />
                                                            <svg class="avatar-status-dot" xmlns="http://www.w3.org/2000/svg" width="10"
                                                                height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                                                <circle cx="5" cy="5" r="5" fill="#C9DDA0" />
                                                            </svg>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="request-info-wrapper">
                                                <div class="request-info">
                                                    <div class="business-hub-pet-type">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                                            viewBox="0 0 15 14" fill="none">
                                                            <path
                                                                d="M7.5 5.63158C5.38867 5.63158 3.60467 7.52376 3.02867 9.95408C2.77533 11.0228 3.15733 12.1572 4.095 12.6902C4.83833 13.1127 5.944 13.5 7.5 13.5C9.056 13.5 10.162 13.1127 10.9053 12.6902C11.843 12.1572 12.2247 11.0228 11.9713 9.95408C11.3953 7.52342 9.61133 5.63158 7.5 5.63158ZM0.5 5.09926C0.5 6.04416 1.09667 7 1.83333 7C2.57 7 3.16667 6.04416 3.16667 5.09926C3.16667 4.15437 2.57 3.57895 1.83333 3.57895C1.09667 3.57895 0.5 4.15471 0.5 5.09926ZM14.5 5.09926C14.5 6.04416 13.9033 7 13.1667 7C12.43 7 11.8333 6.04416 11.8333 5.09926C11.8333 4.15437 12.43 3.57895 13.1667 3.57895C13.9033 3.57895 14.5 4.15471 14.5 5.09926ZM4 2.02032C4 2.96521 4.59667 3.92105 5.33333 3.92105C6.07 3.92105 6.66667 2.96521 6.66667 2.02032C6.66667 1.07542 6.07 0.5 5.33333 0.5C4.59667 0.5 4 1.07576 4 2.02032ZM11 2.02032C11 2.96521 10.4033 3.92105 9.66667 3.92105C8.93 3.92105 8.33333 2.96521 8.33333 2.02032C8.33333 1.07542 8.93 0.5 9.66667 0.5C10.4033 0.5 11 1.07576 11 2.02032Z"
                                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <span>{{ $request['pet_type'] }} <span class="black-dot"></span>
                                                            {{ $request['weight'] }}</span>
                                                    </div>
                                                    @if (!$isSpaceAccount)
                                                        <span class="service-badge">{{ $request['service_type'] }}</span>
                                                    @endif
                                                </div>
                                                <div class="request-details">
                                                    <div class="detail-row">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                            viewBox="0 0 15 15" fill="none">
                                                            <path
                                                                d="M0.5 6.86606C0.5 4.33928 0.5 3.07555 1.3204 2.29092C2.1408 1.50629 3.4603 1.50562 6.1 1.50562H8.9C11.5397 1.50562 12.8599 1.50562 13.6796 2.29092C14.4993 3.07622 14.5 4.33928 14.5 6.86606V8.20616C14.5 10.7329 14.5 11.9967 13.6796 12.7813C12.8592 13.5659 11.5397 13.5666 8.9 13.5666H6.1C3.4603 13.5666 2.1401 13.5666 1.3204 12.7813C0.5007 11.996 0.5 10.7329 0.5 8.20616V6.86606Z"
                                                                stroke="#3B3731" />
                                                            <path d="M4.00034 1.50508V0.5M11.0003 1.50508V0.5M0.850342 4.85536H14.1503"
                                                                stroke="#3B3731" stroke-linecap="round" />
                                                            <path
                                                                d="M11.7003 10.2158C11.7003 10.3935 11.6265 10.5639 11.4953 10.6896C11.364 10.8152 11.1859 10.8858 11.0003 10.8858C10.8146 10.8858 10.6366 10.8152 10.5053 10.6896C10.374 10.5639 10.3003 10.3935 10.3003 10.2158C10.3003 10.038 10.374 9.86761 10.5053 9.74195C10.6366 9.61629 10.8146 9.5457 11.0003 9.5457C11.1859 9.5457 11.364 9.61629 11.4953 9.74195C11.6265 9.86761 11.7003 10.038 11.7003 10.2158ZM11.7003 7.53553C11.7003 7.71324 11.6265 7.88367 11.4953 8.00933C11.364 8.13499 11.1859 8.20559 11.0003 8.20559C10.8146 8.20559 10.6366 8.13499 10.5053 8.00933C10.374 7.88367 10.3003 7.71324 10.3003 7.53553C10.3003 7.35782 10.374 7.18739 10.5053 7.06173C10.6366 6.93607 10.8146 6.86548 11.0003 6.86548C11.1859 6.86548 11.364 6.93607 11.4953 7.06173C11.6265 7.18739 11.7003 7.35782 11.7003 7.53553ZM8.20029 10.2158C8.20029 10.3935 8.12654 10.5639 7.99527 10.6896C7.86399 10.8152 7.68594 10.8858 7.50029 10.8858C7.31464 10.8858 7.13659 10.8152 7.00532 10.6896C6.87404 10.5639 6.80029 10.3935 6.80029 10.2158C6.80029 10.038 6.87404 9.86761 7.00532 9.74195C7.13659 9.61629 7.31464 9.5457 7.50029 9.5457C7.68594 9.5457 7.86399 9.61629 7.99527 9.74195C8.12654 9.86761 8.20029 10.038 8.20029 10.2158ZM8.20029 7.53553C8.20029 7.71324 8.12654 7.88367 7.99527 8.00933C7.86399 8.13499 7.68594 8.20559 7.50029 8.20559C7.31464 8.20559 7.13659 8.13499 7.00532 8.00933C6.87404 7.88367 6.80029 7.71324 6.80029 7.53553C6.80029 7.35782 6.87404 7.18739 7.00532 7.06173C7.13659 6.93607 7.31464 6.86548 7.50029 6.86548C7.68594 6.86548 7.86399 6.93607 7.99527 7.06173C8.12654 7.18739 8.20029 7.35782 8.20029 7.53553ZM4.70029 10.2158C4.70029 10.3935 4.62654 10.5639 4.49527 10.6896C4.36399 10.8152 4.18594 10.8858 4.00029 10.8858C3.81464 10.8858 3.63659 10.8152 3.50532 10.6896C3.37404 10.5639 3.30029 10.3935 3.30029 10.2158C3.30029 10.038 3.37404 9.86761 3.50532 9.74195C3.63659 9.61629 3.81464 9.5457 4.00029 9.5457C4.18594 9.5457 4.36399 9.61629 4.49527 9.74195C4.62654 9.86761 4.70029 10.038 4.70029 10.2158ZM4.70029 7.53553C4.70029 7.71324 4.62654 7.88367 4.49527 8.00933C4.36399 8.13499 4.18594 8.20559 4.00029 8.20559C3.81464 8.20559 3.63659 8.13499 3.50532 8.00933C3.37404 7.88367 3.30029 7.71324 3.30029 7.53553C3.30029 7.35782 3.37404 7.18739 3.50532 7.06173C3.63659 6.93607 3.81464 6.86548 4.00029 6.86548C4.18594 6.86548 4.36399 6.93607 4.49527 7.06173C4.62654 7.18739 4.70029 7.35782 4.70029 7.53553Z"
                                                                fill="#3B3731" />
                                                        </svg>
                                                        <span>{{ $request['date'] }}</span>
                                                    </div>
                                                    <div class="detail-row">
                                                        @if ($isSpaceAccount)
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13"
                                                                viewBox="0 0 15 13" fill="none">
                                                                <path
                                                                    d="M5.5438 12.2186C2.69175 11.808 0.5 9.35597 0.5 6.39044C0.5 3.13724 3.13744 0.5 6.39088 0.5C9.08249 0.5 11.3519 2.30515 12.0556 4.77069"
                                                                    stroke="#3B3731" stroke-linecap="round" />
                                                                <path
                                                                    d="M12.5529 9.66857C12.6785 9.54095 12.817 9.43786 12.9682 9.35932C13.1213 9.28079 13.2971 9.24152 13.4954 9.24152C13.6604 9.24152 13.8047 9.269 13.9284 9.32398C14.0541 9.37896 14.1591 9.45652 14.2436 9.55665C14.328 9.65483 14.3918 9.77362 14.435 9.91303C14.4782 10.0524 14.4998 10.2066 14.4998 10.3754V12.281H13.9225V10.3754C13.9225 10.1614 13.8734 9.99549 13.7752 9.87768C13.6771 9.75791 13.5268 9.69803 13.3246 9.69803C13.1773 9.69803 13.0389 9.73337 12.9093 9.80405C12.7816 9.87278 12.6628 9.96702 12.5529 10.0868V12.281H11.9785V7.93384H12.5529V9.66857Z"
                                                                    fill="#3B3731" />
                                                                <path
                                                                    d="M11.1684 11.8425V12.2814H8.88277V11.8425H9.77818V9.07109C9.77818 8.98274 9.78113 8.89144 9.78702 8.79719L9.06833 9.40685C9.04084 9.42845 9.01335 9.44219 8.98586 9.44808C8.95837 9.45397 8.93186 9.45496 8.90633 9.45103C8.88277 9.4471 8.86117 9.43925 8.84153 9.42747C8.82386 9.41372 8.80913 9.39998 8.79735 9.38623L8.61768 9.13589L9.88422 8.04321H10.3525V11.8425H11.1684Z"
                                                                    fill="#3B3731" />
                                                                <path
                                                                    d="M6.68523 4.62362C6.68523 4.46096 6.81711 4.3291 6.97978 4.3291C7.14245 4.3291 7.27432 4.46096 7.27432 4.62362V7.27432H5.21251C5.04984 7.27432 4.91797 7.14246 4.91797 6.9798C4.91797 6.81714 5.04984 6.68528 5.21251 6.68528H6.68523V4.62362Z"
                                                                    fill="#3B3731" />
                                                            </svg>
                                                        @else
                                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                                                <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                                                    stroke-linecap="round" />
                                                            </svg>
                                                        @endif
                                                        <span>{{ $request['time'] }}</span>
                                                    </div>
                                                    <span class="price">£{{ number_format($request['price'], 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="request-actions">
                                            <button type="button" wire:click="acceptRequest({{ (int) $request['booking_id'] }})" class="btn-accept">Accept
                                                Request</button>
                                            <button type="button" wire:click="viewDetails({{ (int) $request['booking_id'] }})" class="btn-view">View
                                                details</button>
                                        </div>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="divider"></div>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($this->upcomingBookings as $booking)
                                    <div class="upcoming-booking-item">
                                        <div class="booking-status-bar">
                                            <div class="status-badge confirmed">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                                    fill="none">
                                                    <path
                                                        d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z"
                                                        fill="#B5CA89" />
                                                </svg>
                                                <span>{{ $booking['status'] }}</span>
                                            </div>
                                            <span class="booking-id">Booking ID: {{ $booking['id'] }}</span>
                                        </div>
                                        <div class="booking-body">
                                            @if ($isSpaceAccount)
                                                <div class="pet-section">
                                                    <div class="pet-avatar-stack">
                                                        @foreach ($booking['pet_avatars'] ?? [] as $petAvatar)
                                                            <x-business-hub.common.avatar :name="$petAvatar['name']" :src="$petAvatar['photo']" class="pet-avatar-large" :size="40" :font-size="16" />
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                                            fill="none">
                                                            <path
                                                                d="M0.5 6.8664C0.5 4.33952 0.5 3.07574 1.3204 2.29107C2.1408 1.50641 3.4603 1.50574 6.1 1.50574H8.9C11.5397 1.50574 12.8599 1.50574 13.6796 2.29107C14.4993 3.07641 14.5 4.33952 14.5 6.8664V8.20656C14.5 10.7334 14.5 11.9972 13.6796 12.7819C12.8592 13.5666 11.5397 13.5672 8.9 13.5672H6.1C3.4603 13.5672 2.1401 13.5672 1.3204 12.7819C0.5007 11.9965 0.5 10.7334 0.5 8.20656V6.8664Z"
                                                                stroke="#3B3731" />
                                                            <path d="M3.99985 1.50512V0.5M10.9999 1.50512V0.5M0.849854 4.85554H14.1499"
                                                                stroke="#3B3731" stroke-linecap="round" />
                                                            <path
                                                                d="M11.7 10.2163C11.7 10.394 11.6263 10.5644 11.495 10.6901C11.3637 10.8157 11.1857 10.8863 11 10.8863C10.8144 10.8863 10.6363 10.8157 10.5051 10.6901C10.3738 10.5644 10.3 10.394 10.3 10.2163C10.3 10.0385 10.3738 9.8681 10.5051 9.74244C10.6363 9.61677 10.8144 9.54617 11 9.54617C11.1857 9.54617 11.3637 9.61677 11.495 9.74244C11.6263 9.8681 11.7 10.0385 11.7 10.2163ZM11.7 7.53593C11.7 7.71364 11.6263 7.88408 11.495 8.00975C11.3637 8.13541 11.1857 8.20601 11 8.20601C10.8144 8.20601 10.6363 8.13541 10.5051 8.00975C10.3738 7.88408 10.3 7.71364 10.3 7.53593C10.3 7.35821 10.3738 7.18777 10.5051 7.06211C10.6363 6.93644 10.8144 6.86584 11 6.86584C11.1857 6.86584 11.3637 6.93644 11.495 7.06211C11.6263 7.18777 11.7 7.35821 11.7 7.53593ZM8.20005 10.2163C8.20005 10.394 8.1263 10.5644 7.99502 10.6901C7.86375 10.8157 7.6857 10.8863 7.50005 10.8863C7.3144 10.8863 7.13635 10.8157 7.00507 10.6901C6.8738 10.5644 6.80005 10.394 6.80005 10.2163C6.80005 10.0385 6.8738 9.8681 7.00507 9.74244C7.13635 9.61677 7.3144 9.54617 7.50005 9.54617C7.6857 9.54617 7.86375 9.61677 7.99502 9.74244C8.1263 9.8681 8.20005 10.0385 8.20005 10.2163ZM8.20005 7.53593C8.20005 7.71364 8.1263 7.88408 7.99502 8.00975C7.86375 8.13541 7.6857 8.20601 7.50005 8.20601C7.3144 8.20601 7.13635 8.13541 7.00507 8.00975C6.8738 7.88408 6.80005 7.71364 6.80005 7.53593C6.80005 7.35821 6.8738 7.18777 7.00507 7.06211C7.13635 6.93644 7.3144 6.86584 7.50005 6.86584C7.6857 6.86584 7.86375 6.93644 7.99502 7.06211C8.1263 7.18777 8.20005 7.35821 8.20005 7.53593ZM4.70005 10.2163C4.70005 10.394 4.6263 10.5644 4.49502 10.6901C4.36375 10.8157 4.1857 10.8863 4.00005 10.8863C3.8144 10.8863 3.63635 10.8157 3.50507 10.6901C3.3738 10.5644 3.30005 10.394 3.30005 10.2163C3.30005 10.0385 3.3738 9.8681 3.50507 9.74244C3.63635 9.61677 3.8144 9.54617 4.00005 9.54617C4.1857 9.54617 4.36375 9.61677 4.49502 9.74244C4.6263 9.8681 4.70005 10.0385 4.70005 10.2163ZM4.70005 7.53593C4.70005 7.71364 4.6263 7.88408 4.49502 8.00975C4.36375 8.13541 4.1857 8.20601 4.00005 8.20601C3.8144 8.20601 3.63635 8.13541 3.50507 8.00975C3.3738 7.88408 3.30005 7.71364 3.30005 7.53593C3.30005 7.35821 3.3738 7.18777 3.50507 7.06211C3.63635 6.93644 3.8144 6.86584 4.00005 6.86584C4.1857 6.86584 4.36375 6.93644 4.49502 7.06211C4.6263 7.18777 4.70005 7.35821 4.70005 7.53593Z"
                                                                fill="#3B3731" />
                                                        </svg>Date
                                                    </div>
                                                    <div class="info-value">{{ $booking['date'] }}</div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15"
                                                            fill="none">
                                                            <path
                                                                d="M6.49357 14.4253C3.10447 13.9373 0.5 11.0236 0.5 7.49964C0.5 3.63384 3.63408 0.5 7.50016 0.5C10.6986 0.5 13.3953 2.64507 14.2315 5.57488"
                                                                stroke="#3B3731" stroke-width="1" stroke-linecap="round" />
                                                            <path
                                                                d="M14.8222 11.3951C14.9715 11.2435 15.136 11.121 15.3157 11.0277C15.4977 10.9343 15.7065 10.8877 15.9422 10.8877C16.1382 10.8877 16.3097 10.9203 16.4567 10.9857C16.606 11.051 16.7309 11.1431 16.8312 11.2621C16.9315 11.3788 17.0074 11.52 17.0587 11.6856C17.1101 11.8513 17.1357 12.0344 17.1357 12.2351V14.4995H16.4497V12.2351C16.4497 11.9808 16.3914 11.7836 16.2747 11.6436C16.158 11.5013 15.9795 11.4301 15.7392 11.4301C15.5642 11.4301 15.3997 11.4721 15.2457 11.5561C15.094 11.6378 14.9528 11.7498 14.8222 11.8921V14.4995H14.1396V9.33374H14.8222V11.3951Z"
                                                                fill="#3B3731" />
                                                            <path
                                                                d="M13.1771 13.9785V14.5H10.461V13.9785H11.525V10.6852C11.525 10.5802 11.5285 10.4717 11.5355 10.3597L10.6815 11.0842C10.6488 11.1098 10.6162 11.1262 10.5835 11.1332C10.5508 11.1402 10.5193 11.1413 10.489 11.1367C10.461 11.132 10.4353 11.1227 10.412 11.1087C10.391 11.0923 10.3735 11.076 10.3595 11.0597L10.146 10.7622L11.651 9.46375H12.2075V13.9785H13.1771Z"
                                                                fill="#3B3731" />
                                                            <path
                                                                d="M7.85005 5.40003C7.85005 5.20674 8.00675 5.05005 8.20006 5.05005C8.39336 5.05005 8.55007 5.20674 8.55007 5.40003V8.54987H6.10001C5.9067 8.54987 5.75 8.39318 5.75 8.19989C5.75 8.0066 5.9067 7.8499 6.10001 7.8499H7.85005V5.40003Z"
                                                                fill="#3B3731" />
                                                        </svg>Time
                                                    </div>
                                                    <div class="info-value">{{ $booking['time'] }}</div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13"
                                                            fill="none">
                                                            <path
                                                                d="M13.1094 12.1166V3.83417C13.1094 3.81429 13.1111 3.79482 13.1141 3.77576L10.8748 1.86616C10.3986 1.46067 10.0696 1.18119 9.79046 0.998982C9.52095 0.823101 9.33983 0.766834 9.16658 0.766834C8.99347 0.766835 8.81349 0.823306 8.54428 0.998982C8.26512 1.18121 7.93524 1.46044 7.45838 1.86616L5.21745 3.77576C5.22054 3.7949 5.22374 3.81422 5.22374 3.83417V12.1166C5.2234 12.3281 5.04341 12.5 4.82144 12.5C4.59961 12.4998 4.41948 12.328 4.41914 12.1166V4.45573L4.00427 4.81069C3.83864 4.95183 3.58349 4.93709 3.43539 4.77924C3.28788 4.62148 3.30169 4.3796 3.46682 4.23856L6.92094 1.29553H6.92251C7.38342 0.90337 7.75667 0.583679 8.08855 0.366942C8.43046 0.143752 8.76989 2.24995e-07 9.16658 0C9.56323 0 9.9026 0.143743 10.2446 0.366942C10.5767 0.583731 10.9515 0.903225 11.4122 1.29553L14.8663 4.23856C15.0315 4.3796 15.0453 4.62148 14.8978 4.77924C14.7497 4.93709 14.4945 4.95183 14.3289 4.81069L13.914 4.45573V12.1166C13.9137 12.328 13.7336 12.4998 13.5117 12.5C13.2898 12.5 13.1098 12.3281 13.1094 12.1166Z"
                                                                fill="#3B3731" />
                                                            <path
                                                                d="M1.82418 6.66737C1.82418 6.37816 1.74192 6.13002 1.62487 5.96249C1.50777 5.79507 1.37173 5.7247 1.25 5.7247C1.12833 5.7248 0.992145 5.79519 0.875132 5.96249C0.758177 6.13002 0.675818 6.37832 0.675818 6.66737C0.675926 6.95653 0.758033 7.20483 0.875132 7.37226C0.992124 7.53946 1.12837 7.60853 1.25 7.60863C1.37164 7.60863 1.50783 7.53939 1.62487 7.37226C1.74197 7.20483 1.82407 6.95653 1.82418 6.66737ZM2.5 6.66737C2.49989 7.09818 2.37897 7.50235 2.16605 7.80679C1.95294 8.11149 1.63215 8.33333 1.25 8.33333C0.868121 8.33323 0.548331 8.11124 0.335269 7.80679C0.12233 7.50234 0.000106589 7.0982 0 6.66737C0 6.23634 0.122237 5.83113 0.335269 5.52654C0.548331 5.22219 0.868196 5.0001 1.25 5C1.63209 5 1.95294 5.22191 2.16605 5.52654C2.37908 5.83113 2.5 6.23634 2.5 6.66737Z"
                                                                fill="#3B3731" />
                                                            <path
                                                                d="M0.833252 12.1094V7.8906C0.833252 7.67488 1.0198 7.5 1.24992 7.5C1.48004 7.5 1.66659 7.67488 1.66659 7.8906V12.1094C1.66641 12.325 1.47993 12.5 1.24992 12.5C1.01991 12.5 0.833428 12.325 0.833252 12.1094Z"
                                                                fill="#3B3731" />
                                                            <path
                                                                d="M10.6579 9.31364C10.6579 8.9734 10.6564 8.75738 10.6348 8.59906C10.6147 8.4523 10.584 8.41411 10.5654 8.39576C10.5468 8.37748 10.5083 8.34577 10.3588 8.32597C10.1978 8.30466 9.97715 8.30473 9.63096 8.30473H8.92167C8.57549 8.30473 8.35488 8.30466 8.19387 8.32597C8.04438 8.34577 8.00583 8.37748 7.98725 8.39576C7.96865 8.41411 7.93793 8.4523 7.91787 8.59906C7.89622 8.75738 7.89474 8.9734 7.89474 9.31364V11.7229H10.6579V9.31364ZM9.98715 5.42972C10.2048 5.42988 10.3816 5.60399 10.3819 5.81811C10.3819 6.03251 10.205 6.20634 9.98715 6.2065H8.56548C8.34762 6.20634 8.17074 6.03251 8.17074 5.81811C8.17108 5.60399 8.34782 5.42988 8.56548 5.42972H9.98715ZM9.98715 3.33301L10.0658 3.34059C10.246 3.37657 10.3819 3.53349 10.3819 3.7214C10.3819 3.90931 10.246 4.06623 10.0658 4.10221L9.98715 4.10979H8.56548C8.34762 4.10963 8.17074 3.9358 8.17074 3.7214C8.17074 3.507 8.34762 3.33317 8.56548 3.33301H9.98715ZM11.4474 11.7229H14.6053C14.8233 11.7229 15 11.8968 15 12.1113C14.9997 12.3255 14.8231 12.4997 14.6053 12.4997H0.394737C0.176935 12.4997 0.000332468 12.3255 0 12.1113C0 11.8968 0.17673 11.7229 0.394737 11.7229H7.10526V9.31364C7.10526 8.99552 7.10427 8.71791 7.13456 8.4959C7.16648 8.26247 7.23958 8.03308 7.42907 7.84655C7.61867 7.66 7.85172 7.58819 8.08902 7.55678C8.31486 7.52691 8.59793 7.52795 8.92167 7.52795H9.63096C9.95471 7.52795 10.2378 7.52691 10.4636 7.55678C10.7009 7.58819 10.934 7.66 11.1236 7.84655C11.313 8.03308 11.3862 8.26247 11.4181 8.4959C11.4484 8.71791 11.4474 8.99552 11.4474 9.31364V11.7229Z"
                                                                fill="#3B3731" />
                                                        </svg>Space
                                                    </div>
                                                    <div class="info-value">{{ $booking['visit_type'] }}</div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="pet-section">
                                                    <div class="pet-info">
                                                        <div class="pet-count">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                                                viewBox="0 0 15 14" fill="none">
                                                                <path
                                                                    d="M7.5 5.63158C5.38867 5.63158 3.60467 7.52376 3.02867 9.95408C2.77533 11.0228 3.15733 12.1572 4.095 12.6902C4.83833 13.1127 5.944 13.5 7.5 13.5C9.056 13.5 10.162 13.1127 10.9053 12.6902C11.843 12.1572 12.2247 11.0228 11.9713 9.95408C11.3953 7.52342 9.61133 5.63158 7.5 5.63158ZM0.5 5.09926C0.5 6.04416 1.09667 7 1.83333 7C2.57 7 3.16667 6.04416 3.16667 5.09926C3.16667 4.15437 2.57 3.57895 1.83333 3.57895C1.09667 3.57895 0.5 4.15471 0.5 5.09926ZM14.5 5.09926C14.5 6.04416 13.9033 7 13.1667 7C12.43 7 11.8333 6.04416 11.8333 5.09926C11.8333 4.15437 12.43 3.57895 13.1667 3.57895C13.9033 3.57895 14.5 4.15471 14.5 5.09926ZM4 2.02032C4 2.96521 4.59667 3.92105 5.33333 3.92105C6.07 3.92105 6.66667 2.96521 6.66667 2.02032C6.66667 1.07542 6.07 0.5 5.33333 0.5C4.59667 0.5 4 1.07576 4 2.02032ZM11 2.02032C11 2.96521 10.4033 3.92105 9.66667 3.92105C8.93 3.92105 8.33333 2.96521 8.33333 2.02032C8.33333 1.07542 8.93 0.5 9.66667 0.5C10.4033 0.5 11 1.07576 11 2.02032Z"
                                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <span>Pet</span>
                                                        </div>
                                                        <span class="pet-types">{{ $booking['pet_type'] }} <span class="black-dot"></span>
                                                            {{ $booking['weight'] }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="pet-section">
                                                    <div class="pet-avatar-stack">
                                                        @foreach ($booking['pet_avatars'] ?? [] as $petAvatar)
                                                            <x-business-hub.common.avatar :name="$petAvatar['name']" :src="$petAvatar['photo']" class="pet-avatar-large" :size="40" :font-size="16" />
                                                        @endforeach
                                                    </div>
                                                    <div class="pet-info">
                                                        <div class="pet-count">
                                                            @if ($booking['pet_count'] > 1)
                                                                <span>+{{ $booking['pet_count'] }} Pets</span>
                                                            @else
                                                                <span>Other</span>
                                                            @endif
                                                        </div>
                                                        <span class="pet-types">{{ implode(', ', $booking['pets']) }}</span>
                                                    </div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                            viewBox="0 0 15 16" fill="none">
                                                            <path
                                                                d="M4.69342 10.9533C5.90875 12.1686 8.8646 11.1836 11.2953 8.75251C13.7263 6.32185 14.7113 3.36599 13.496 2.15066M8.26939 1.32513L8.81947 1.87561M6.34408 3.25084L6.89416 3.80093M4.69303 5.45159L5.24312 6.00167M4.14294 8.20242L4.69303 8.75251M11.2953 0.5L11.8454 1.05009M10.7452 3.80132L11.8454 4.9015M8.81986 5.72702L9.92004 6.8272M6.61912 7.37729L7.7193 8.47747"
                                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path
                                                                d="M4.69305 12.6018C5.14883 12.146 5.14883 11.407 4.69306 10.9512C4.23728 10.4955 3.49832 10.4955 3.04254 10.9512L0.841854 13.1519C0.386077 13.6077 0.386077 14.3467 0.841854 14.8024C1.29763 15.2582 2.03659 15.2582 2.49237 14.8024L4.69305 12.6018Z"
                                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>Service</div>
                                                    <div class="info-value">{{ $booking['service'] }}</div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label">
                                                        <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
                                                            <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"></circle>
                                                            <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                                                stroke-linecap="round"></path>
                                                        </svg>
                                                        Time
                                                    </div>
                                                    <div class="info-value">{{ $booking['time'] }}</div>
                                                </div>
                                                <div class="booking-body-divider"></div>
                                                <div class="info-cell">
                                                    <div class="info-label">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                                            fill="none">
                                                            <path
                                                                d="M0.5 6.8664C0.5 4.33952 0.5 3.07574 1.3204 2.29107C2.1408 1.50641 3.4603 1.50574 6.1 1.50574H8.9C11.5397 1.50574 12.8599 1.50574 13.6796 2.29107C14.4993 3.07641 14.5 4.33952 14.5 6.8664V8.20656C14.5 10.7334 14.5 11.9972 13.6796 12.7819C12.8592 13.5666 11.5397 13.5672 8.9 13.5672H6.1C3.4603 13.5672 2.1401 13.5672 1.3204 12.7819C0.5007 11.9965 0.5 10.7334 0.5 8.20656V6.8664Z"
                                                                stroke="#3B3731"></path>
                                                            <path d="M3.99985 1.50512V0.5M10.9999 1.50512V0.5M0.849854 4.85554H14.1499"
                                                                stroke="#3B3731" stroke-linecap="round"></path>
                                                            <path
                                                                d="M11.7 10.2163C11.7 10.394 11.6263 10.5644 11.495 10.6901C11.3637 10.8157 11.1857 10.8863 11 10.8863C10.8144 10.8863 10.6363 10.8157 10.5051 10.6901C10.3738 10.5644 10.3 10.394 10.3 10.2163C10.3 10.0385 10.3738 9.8681 10.5051 9.74244C10.6363 9.61677 10.8144 9.54617 11 9.54617C11.1857 9.54617 11.3637 9.61677 11.495 9.74244C11.6263 9.8681 11.7 10.0385 11.7 10.2163ZM11.7 7.53593C11.7 7.71364 11.6263 7.88408 11.495 8.00975C11.3637 8.13541 11.1857 8.20601 11 8.20601C10.8144 8.20601 10.6363 8.13541 10.5051 8.00975C10.3738 7.88408 10.3 7.71364 10.3 7.53593C10.3 7.35821 10.3738 7.18777 10.5051 7.06211C10.6363 6.93644 10.8144 6.86584 11 6.86584C11.1857 6.86584 11.3637 6.93644 11.495 7.06211C11.6263 7.18777 11.7 7.35821 11.7 7.53593ZM8.20005 10.2163C8.20005 10.394 8.1263 10.5644 7.99502 10.6901C7.86375 10.8157 7.6857 10.8863 7.50005 10.8863C7.3144 10.8863 7.13635 10.8157 7.00507 10.6901C6.8738 10.5644 6.80005 10.394 6.80005 10.2163C6.80005 10.0385 6.8738 9.8681 7.00507 9.74244C7.13635 9.61677 7.3144 9.54617 7.50005 9.54617C7.6857 9.54617 7.86375 9.61677 7.99502 9.74244C8.1263 9.8681 8.20005 10.0385 8.20005 10.2163ZM8.20005 7.53593C8.20005 7.71364 8.1263 7.88408 7.99502 8.00975C7.86375 8.13541 7.6857 8.20601 7.50005 8.20601C7.3144 8.20601 7.13635 8.13541 7.00507 8.00975C6.8738 7.88408 6.80005 7.71364 6.80005 7.53593C6.80005 7.35821 6.8738 7.18777 7.00507 7.06211C7.13635 6.93644 7.3144 6.86584 7.50005 6.86584C7.6857 6.86584 7.86375 6.93644 7.99502 7.06211C8.1263 7.18777 8.20005 7.35821 8.20005 7.53593ZM4.70005 10.2163C4.70005 10.394 4.6263 10.5644 4.49502 10.6901C4.36375 10.8157 4.1857 10.8863 4.00005 10.8863C3.8144 10.8863 3.63635 10.8157 3.50507 10.6901C3.3738 10.5644 3.30005 10.394 3.30005 10.2163C3.30005 10.0385 3.3738 9.8681 3.50507 9.74244C3.63635 9.61677 3.8144 9.54617 4.00005 9.54617C4.1857 9.54617 4.36375 9.61677 4.49502 9.74244C4.6263 9.8681 4.70005 10.0385 4.70005 10.2163ZM4.70005 7.53593C4.70005 7.71364 4.6263 7.88408 4.49502 8.00975C4.36375 8.13541 4.1857 8.20601 4.00005 8.20601C3.8144 8.20601 3.63635 8.13541 3.50507 8.00975C3.3738 7.88408 3.30005 7.71364 3.30005 7.53593C3.30005 7.35821 3.3738 7.18777 3.50507 7.06211C3.63635 6.93644 3.8144 6.86584 4.00005 6.86584C4.1857 6.86584 4.36375 6.93644 4.49502 7.06211C4.6263 7.18777 4.70005 7.35821 4.70005 7.53593Z"
                                                                fill="#3B3731"></path>
                                                        </svg>
                                                        Date
                                                    </div>
                                                    <div class="info-value">{{ $booking['date'] }}</div>
                                                </div>
                                            @endif
                                            <div class="booking-body-divider"></div>
                                            <div class="booking-actions">
                                                <button class="btn-action btn-navigate"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="17" height="11" viewBox="0 0 17 11" fill="none">
                                                        <path
                                                            d="M16.1264 4.31767C16.5221 4.70945 16.5215 5.34892 16.1252 5.74001L12.0214 9.78966C11.6966 10.1102 11.1731 10.106 10.8535 9.78024C10.7269 9.6553 10.6762 9.53037 10.7016 9.40543C10.7332 9.28049 10.8029 9.1618 10.9105 9.04935L13.6357 6.3788C13.7876 6.22887 13.9301 6.09457 14.063 5.97587C14.1683 5.8819 14.0872 5.69393 13.9468 5.70816C13.7843 5.72462 13.6173 5.7389 13.4458 5.75099C13.0976 5.77597 12.7368 5.78847 12.3633 5.78847H0.763684C0.341912 5.78847 0 5.44655 0 5.02478C0 4.60301 0.341913 4.2611 0.763685 4.2611H12.3633C12.7368 4.2611 13.1008 4.27359 13.4553 4.29858C13.6242 4.31049 13.7888 4.32452 13.9491 4.34068C14.0915 4.35504 14.1719 4.16651 14.063 4.07369C13.9301 3.955 13.7876 3.82069 13.6357 3.67076L10.8915 0.981471C10.7775 0.869026 10.7079 0.750335 10.6826 0.625397C10.6573 0.500459 10.7047 0.375521 10.825 0.250583C11.1497 -0.0802029 11.6815 -0.0839592 12.0109 0.242206L16.1264 4.31767Z"
                                                            fill="white" />
                                                    </svg></button>
                                                <button class="btn-action btn-message"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="18" height="17" viewBox="0 0 18 17" fill="none">
                                                        <path
                                                            d="M8.77344 0.75C13.2901 0.750138 16.7959 3.98439 16.7959 7.79297C16.7959 11.6016 13.2901 14.8358 8.77344 14.8359H8.77148C7.96957 14.8378 7.17035 14.732 6.39551 14.5215L6.11523 14.4453L5.85645 14.5781C5.3514 14.8374 4.20543 15.3396 2.29883 15.7285L2.30078 15.7188C2.58443 14.8788 2.82006 13.9064 2.90723 12.9844L2.93945 12.6377L2.69531 12.3887C1.47357 11.1454 0.75 9.53663 0.75 7.79297C0.750026 3.98431 4.25668 0.75 8.77344 0.75Z"
                                                            stroke="white" stroke-width="1.5" />
                                                    </svg></button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/weekly-revenue-chart.js') }}"></script>
@endassets

@script
    <script>
        const bootWeeklyRevenueChartFromComponent = () => {
            const canvas = $wire.$el?.querySelector('#weeklyRevenueChart');
            if (canvas && window.mountWeeklyRevenueChart) {
                window.mountWeeklyRevenueChart(canvas);
                return;
            }

            window.scheduleWeeklyRevenueChartInit?.();
        };

        bootWeeklyRevenueChartFromComponent();
        requestAnimationFrame(() => {
            bootWeeklyRevenueChartFromComponent();
            setTimeout(bootWeeklyRevenueChartFromComponent, 150);
            setTimeout(bootWeeklyRevenueChartFromComponent, 400);
            setTimeout(bootWeeklyRevenueChartFromComponent, 900);
        });
    </script>
@endscript

@push('script')
    <script>
        (function() {
            function syncPageScrollLock() {
                const isModalOpen = !!document.querySelector('.card-modal-backdrop');
                document.documentElement.classList.toggle('no-page-scroll', isModalOpen);
                document.body.classList.toggle('no-page-scroll', isModalOpen);
            }

            const observer = new MutationObserver(syncPageScrollLock);
            observer.observe(document.body, {
                childList: true,
                subtree: true,
            });

            document.addEventListener('DOMContentLoaded', syncPageScrollLock);
            document.addEventListener('livewire:navigated', syncPageScrollLock);
        })();
    </script>
@endpush
