<?php

use App\Models\Booking;
use App\Models\PetDetail;
use App\Models\Payment;
use App\Models\Review;
use App\Models\PetMedicationDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Volt\Component;

new class extends Component {
    private const BOOKING_LIST_COLUMNS = ['id', 'pet_owner_id', 'goormer_spacer_id', 'date', 'time', 'service', 'amount', 'staff', 'rating', 'visit_type', 'booking_status', 'created_at', 'extra_add_ons', 'discount'];

    public ?int $completedBookingId = null;

    private const PET_LIST_COLUMNS = ['id', 'user_id', 'name', 'pet_type', 'breed', 'sex', 'birthday', 'weight', 'notes', 'photo'];

    public ?int $selectedClientId = null;

    public string $activeFilter = 'all';

    public string $search = '';

    public string $sort = 'name_asc';

    public int $perPage = 6;

    public string $profileActiveTab = 'upcoming';

    public string $profileSort = 'date_asc';

    public string $profilePetSort = 'name_asc';

    public int $profilePerPage = 6;

    public ?int $selectedPetId = null;

    public bool $profileIsBlocked = false;

    public ?int $openReviewReplyId = null;

    public string $reviewReplyMessage = '';

    public int $reviewReplyClientRating = 0;

    private static ?bool $usersHaveProfileImage = null;

    private function usersHaveProfileImage(): bool
    {
        if (self::$usersHaveProfileImage !== null) {
            return self::$usersHaveProfileImage;
        }

        return self::$usersHaveProfileImage = Cache::rememberForever('users_has_profile_image', fn() => Schema::hasColumn('users', 'profile_image'));
    }

    private function profilePetCount(): int
    {
        if (!$this->selectedClientId) {
            return 0;
        }

        $row = $this->allClientRows->firstWhere('id', $this->selectedClientId);

        if ($row !== null) {
            return $row['pets']->count();
        }

        $count = PetDetail::query()->where('user_id', $this->selectedClientId)->count();

        if ($count > 0) {
            return $count;
        }

        return $this->profileBookings->flatMap(fn($booking) => $booking->pets)->unique('id')->count();
    }

    private function filterUpcomingBookings(Collection $bookings): Collection
    {
        return $bookings->filter(function ($booking) {
            if (!in_array($booking->booking_status, ['pending', 'confirmed'], true)) {
                return false;
            }

            return $booking->date && $booking->date->gte(today());
        });
    }

    private function spacerId(): int
    {
        return (int) (auth('groomer_spacer')->id() ?? 0);
    }

    private function clientInitialsFromName(?string $name): string
    {
        if (!filled($name)) {
            return '??';
        }

        return Str::upper(Str::substr(Str::of($name)->explode(' ')->map(fn(string $part) => Str::substr($part, 0, 1))->implode(''), 0, 2));
    }

    #[Computed(persist: true)]
    public function allClientRows(): Collection
    {
        $spacerId = $this->spacerId();
        if ($spacerId === 0) {
            return collect();
        }

        $recentCutoff = now()->subDays(60)->getTimestamp();
        $today = today()->toDateString();

        $ownerStats = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereNotNull('pet_owner_id')
            ->selectRaw(
                'pet_owner_id,
                COUNT(*) as total_bookings,
                SUM(CASE WHEN booking_status = ? THEN 1 ELSE 0 END) as completed_count,
                COALESCE(SUM(CASE WHEN booking_status = ? THEN amount ELSE 0 END), 0) as total_paid,
                MAX(created_at) as last_booking_at',
                ['completed', 'completed'],
            )
            ->groupBy('pet_owner_id')
            ->get()
            ->keyBy('pet_owner_id');

        if ($ownerStats->isEmpty()) {
            return collect();
        }

        $ownerIds = $ownerStats->keys();

        $upcomingByOwner = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereIn('pet_owner_id', $ownerIds)
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', $today)
            ->selectRaw('pet_owner_id, MIN(date) as upcoming_date')
            ->groupBy('pet_owner_id')
            ->pluck('upcoming_date', 'pet_owner_id');

        $owners = User::query()
            ->select(['id', 'name'])
            ->whereIn('id', $ownerIds)
            ->get()
            ->keyBy('id');

        $petsByUser = PetDetail::query()->select(self::PET_LIST_COLUMNS)->whereIn('user_id', $ownerIds)->get()->groupBy('user_id');

        return $ownerStats
            ->map(function ($stat) use ($owners, $petsByUser, $upcomingByOwner, $recentCutoff) {
                $ownerId = (int) $stat->pet_owner_id;
                $owner = $owners->get($ownerId);
                $lastBookingAt = $stat->last_booking_at ? strtotime((string) $stat->last_booking_at) : 0;
                $upcomingDate = $upcomingByOwner->get($ownerId);

                return [
                    'id' => $ownerId,
                    'name' => $owner?->name ?? 'Unknown',
                    'initials' => $this->clientInitialsFromName($owner?->name),
                    'is_repeat' => (int) $stat->completed_count > 1,
                    'pets' => $petsByUser->get($ownerId) ?? collect(),
                    'upcoming_date' => $upcomingDate ? Carbon::parse($upcomingDate)->format('d/m/Y') : null,
                    'total_bookings' => (int) $stat->total_bookings,
                    'total_paid' => (float) $stat->total_paid,
                    'last_booking_at' => $lastBookingAt,
                    'recently_booked' => $lastBookingAt >= $recentCutoff,
                ];
            })
            ->values();
    }

    #[Computed]
    public function profileBookings(): Collection
    {
        if (!$this->selectedClientId) {
            return collect();
        }

        return Booking::query()
            ->select(self::BOOKING_LIST_COLUMNS)
            ->where('goormer_spacer_id', $this->spacerId())
            ->where('pet_owner_id', $this->selectedClientId)
            ->with(['petOwner:id,name', 'pets:' . implode(',', self::PET_LIST_COLUMNS)])
            ->get();
    }

    #[Computed]
    public function profileClient(): ?User
    {
        if (!$this->selectedClientId) {
            return null;
        }

        $columns = ['id', 'name', 'address', 'email_verified_at', 'created_at', 'user_status'];

        if ($this->usersHaveProfileImage()) {
            $columns[] = 'profile_image';
        }

        return User::query()->select($columns)->find($this->selectedClientId);
    }

    private function clientAvatarUrl(?User $client): ?string
    {
        if (!$client || !$this->usersHaveProfileImage()) {
            return null;
        }

        $profileImage = $client->profile_image ?? null;

        return filled($profileImage) ? asset('storage/' . ltrim((string) $profileImage, '/')) : null;
    }

    #[Computed]
    public function profilePetsBase(): Collection
    {
        if (!$this->selectedClientId) {
            return collect();
        }

        $row = $this->allClientRows->firstWhere('id', $this->selectedClientId);

        if ($row !== null && $row['pets']->isNotEmpty()) {
            return $row['pets']->values();
        }

        $pets = PetDetail::query()->select(self::PET_LIST_COLUMNS)->where('user_id', $this->selectedClientId)->get();

        if ($pets->isNotEmpty()) {
            return $pets;
        }

        return $this->profileBookings->flatMap(fn($booking) => $booking->pets)->unique('id')->values();
    }

    #[Computed]
    public function tabCounts(): array
    {
        $rows = $this->allClientRows;

        return [
            'all' => $rows->count(),
            'repeat' => $rows->where('is_repeat', true)->count(),
            'recent' => $rows->where('recently_booked', true)->count(),
        ];
    }

    #[Computed]
    public function clients(): Collection
    {
        $rows = $this->allClientRows;

        if ($this->activeFilter === 'repeat') {
            $rows = $rows->where('is_repeat', true);
        } elseif ($this->activeFilter === 'recent') {
            $rows = $rows->where('recently_booked', true);
        }

        $query = Str::lower(trim($this->search));
        if ($query !== '') {
            $rows = $rows->filter(fn(array $row) => Str::contains(Str::lower($row['name']), $query));
        }

        $rows = match ($this->sort) {
            'name_desc' => $rows->sortByDesc(fn(array $row) => Str::lower($row['name'])),
            'bookings_desc' => $rows->sortByDesc('total_bookings'),
            'bookings_asc' => $rows->sortBy('total_bookings'),
            'paid_desc' => $rows->sortByDesc('total_paid'),
            'paid_asc' => $rows->sortBy('total_paid'),
            'upcoming_asc' => $rows->sortBy(fn(array $row) => $row['upcoming_date'] ?? 'z'),
            default => $rows->sortBy(fn(array $row) => Str::lower($row['name'])),
        };

        return $rows->values();
    }

    #[Computed]
    public function visibleClients(): Collection
    {
        return $this->clients->take($this->perPage);
    }

    #[Computed]
    public function canLoadMore(): bool
    {
        return $this->visibleClients->count() < $this->clients->count();
    }

    public function setActiveFilter(string $filter): void
    {
        if (!in_array($filter, ['all', 'repeat', 'recent'], true)) {
            return;
        }

        $this->activeFilter = $filter;
        $this->perPage = 6;
    }

    public function setSort(string $sort): void
    {
        $allowed = ['name_asc', 'name_desc', 'bookings_desc', 'bookings_asc', 'paid_desc', 'paid_asc', 'upcoming_asc'];

        if (!in_array($sort, $allowed, true)) {
            return;
        }

        $this->sort = $sort;
        $this->perPage = 6;
    }

    public function updatedSearch(): void
    {
        $this->perPage = 6;
    }

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function viewProfile(int $clientId): void
    {
        $this->selectedClientId = $clientId;
        $this->selectedPetId = null;
        $this->profileActiveTab = 'upcoming';
        $this->profileSort = 'date_asc';
        $this->profilePetSort = 'name_asc';
        $this->profilePerPage = 6;
        $this->completedBookingId = null;
        $this->profileIsBlocked = ($this->profileClient?->user_status ?? 'active') === 'blocked';
        $this->resetReviewReplyDraft();
    }

    public function closeProfile(): void
    {
        $this->selectedClientId = null;
        $this->selectedPetId = null;
        $this->completedBookingId = null;
        $this->resetReviewReplyDraft();
    }

    private function resetReviewReplyDraft(): void
    {
        $this->openReviewReplyId = null;
        $this->reviewReplyMessage = '';
        $this->reviewReplyClientRating = 0;
    }

    private function findProfileReview(int $reviewId): ?Review
    {
        if (!$this->selectedClientId) {
            return null;
        }

        return Review::query()->whereKey($reviewId)->where('pet_owner_id', $this->selectedClientId)->whereHas('booking', fn($query) => $query->where('goormer_spacer_id', $this->spacerId()))->first();
    }

    public function closeReviewReply(): void
    {
        $this->resetReviewReplyDraft();
    }

    public function toggleReviewReply(int $reviewId): void
    {
        if ($this->openReviewReplyId === $reviewId) {
            $this->resetReviewReplyDraft();

            return;
        }

        $review = $this->findProfileReview($reviewId);

        if (!$review) {
            return;
        }

        $this->openReviewReplyId = $reviewId;
        $this->reviewReplyMessage = (string) ($review->reply ?? '');
        $this->reviewReplyClientRating = (int) round((float) ($review->rating ?? 0));
    }

    public function submitReviewReply(int $reviewId): void
    {
        if ($this->openReviewReplyId !== $reviewId) {
            return;
        }

        $this->validate(
            [
                'reviewReplyMessage' => ['required', 'string', 'max:3000'],
                'reviewReplyClientRating' => ['required', 'integer', 'min:1', 'max:5'],
            ],
            [
                'reviewReplyMessage.required' => 'Please write a reply message.',
                'reviewReplyClientRating.min' => 'Please rate the client.',
            ],
        );

        $review = $this->findProfileReview($reviewId);

        if (!$review) {
            return;
        }

        $spacerId = $this->spacerId();

        if ($spacerId === 0) {
            return;
        }

        $review->update([
            'reply' => trim($this->reviewReplyMessage),
            'rating' => $this->reviewReplyClientRating,
            'reply_from' => $spacerId,
        ]);

        $this->resetReviewReplyDraft();
    }

    public function openCompletedBookingModal(int $bookingId): void
    {
        if (!$this->selectedClientId) {
            return;
        }

        $bookingExists = Booking::query()->where('goormer_spacer_id', $this->spacerId())->where('pet_owner_id', $this->selectedClientId)->whereKey($bookingId)->where('booking_status', 'completed')->exists();

        if (!$bookingExists) {
            return;
        }

        $this->completedBookingId = $bookingId;
    }

    public function closeCompletedBookingModal(): void
    {
        $this->completedBookingId = null;
    }

    public function viewPetDetails(int $petId): void
    {
        if (!$this->selectedClientId) {
            return;
        }

        if (!$this->profilePetsBase->contains('id', $petId)) {
            return;
        }

        $this->selectedPetId = $petId;
    }

    public function closePetDetails(): void
    {
        $this->selectedPetId = null;
        $this->profileActiveTab = 'pets';
    }

    public function updateGroomerGuidanceNotes(string $notes): void
    {
        if (!$this->selectedPetId || !$this->selectedClientId) {
            return;
        }

        $pet = PetDetail::query()->where('user_id', $this->selectedClientId)->find($this->selectedPetId);

        if (!$pet) {
            return;
        }

        $value = trim($notes) ?: null;

        if ($pet->medicationDetail) {
            $pet->medicationDetail->update(['groomer_guidance_notes' => $value]);
        } else {
            PetMedicationDetail::create([
                'pet_detail_id' => $pet->id,
                'pet_owner_id' => $this->selectedClientId,
                'groomer_guidance_notes' => $value,
            ]);
        }

        unset($this->selectedPet, $this->selectedPetMedication);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vaccinationRowsFor(?PetMedicationDetail $medication): array
    {
        return $medication?->vaccinationRows() ?? [];
    }

    #[Computed]
    public function selectedPet(): ?PetDetail
    {
        if (!$this->selectedPetId || !$this->selectedClientId) {
            return null;
        }

        return PetDetail::query()->with('medicationDetail')->where('user_id', $this->selectedClientId)->find($this->selectedPetId);
    }

    #[Computed]
    public function selectedPetMedication(): ?PetMedicationDetail
    {
        return $this->selectedPet?->medicationDetail;
    }

    #[Computed]
    public function petVaccinationRows(): array
    {
        return $this->vaccinationRowsFor($this->selectedPetMedication);
    }

    #[Computed]
    public function petOverdueVaccinationCount(): int
    {
        return collect($this->petVaccinationRows)->where('is_overdue', true)->count();
    }

    public function setProfileTab(string $tab): void
    {
        $allowed = ['upcoming', 'pets', 'bookings', 'reviews', 'payments'];
        $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';

        if (!in_array($tab, $allowed, true)) {
            return;
        }

        if ($isSpaceUser && $tab === 'pets') {
            $tab = 'upcoming';
        }

        $this->profileActiveTab = $tab;
        $this->profilePerPage = 6;
        $this->resetReviewReplyDraft();
    }

    public function setProfileSort(string $sort): void
    {
        $allowed = ['date_asc', 'date_desc', 'amount_high', 'amount_low'];

        if (!in_array($sort, $allowed, true)) {
            return;
        }

        $this->profileSort = $sort;
        $this->profilePerPage = 6;
    }

    public function setProfilePetSort(string $sort): void
    {
        $allowed = ['name_asc', 'name_desc', 'type_asc', 'type_desc', 'weight_high', 'weight_low'];

        if (!in_array($sort, $allowed, true)) {
            return;
        }

        $this->profilePetSort = $sort;
    }

    #[Computed]
    public function profilePets(): Collection
    {
        $pets = $this->profilePetsBase->loadMissing('medicationDetail');

        $sorted = match ($this->profilePetSort) {
            'name_desc' => $pets->sortByDesc(fn($pet) => Str::lower((string) ($pet->name ?? ''))),
            'type_asc' => $pets->sortBy(fn($pet) => Str::lower((string) ($pet->pet_type ?? ''))),
            'type_desc' => $pets->sortByDesc(fn($pet) => Str::lower((string) ($pet->pet_type ?? ''))),
            'weight_high' => $pets->sortByDesc(fn($pet) => (float) ($pet->weight ?? 0)),
            'weight_low' => $pets->sortBy(fn($pet) => (float) ($pet->weight ?? 0)),
            default => $pets->sortBy(fn($pet) => Str::lower((string) ($pet->name ?? ''))),
        };

        return $sorted->values();
    }

    public function loadMoreProfile(): void
    {
        $this->profilePerPage += 6;
    }

    #[Renderless]
    public function toggleClientBlock(): void
    {
        if (!$this->selectedClientId) {
            $this->profileIsBlocked = !$this->profileIsBlocked;

            return;
        }

        $hasBookings = Booking::query()->where('goormer_spacer_id', $this->spacerId())->where('pet_owner_id', $this->selectedClientId)->exists();

        if (!$hasBookings) {
            $this->profileIsBlocked = !$this->profileIsBlocked;

            return;
        }

        User::query()
            ->whereKey($this->selectedClientId)
            ->update(['user_status' => $this->profileIsBlocked ? 'blocked' : 'active']);
    }

    #[Computed]
    public function profileSummary(): array
    {
        $client = $this->profileClient;
        $bookings = $this->profileBookings;
        $completed = $bookings->where('booking_status', 'completed');
        $upcoming = $this->filterUpcomingBookings($bookings);

        $ratings = $completed->pluck('rating')->filter(fn($rating) => $rating !== null && $rating !== '');
        $avgRating = $ratings->isNotEmpty() ? round((float) $ratings->avg(), 1) : null;

        $firstBookingAt = $bookings->min(fn($booking) => $booking->created_at?->timestamp ?? PHP_INT_MAX);
        $clientSince = $firstBookingAt && $firstBookingAt < PHP_INT_MAX ? Carbon::createFromTimestamp($firstBookingAt)->format('d M Y') : optional($client?->created_at)->format('d M Y');

        $petCount = $this->profilePetCount();
        $petsLabel = $petCount > 3 ? '3+' : (string) $petCount;
        $isBlocked = $this->profileIsBlocked;

        return [
            'meta' => [
                'name' => $client?->name ?? 'Unknown',
                'initials' => $client ? Str::upper(Str::substr($client->initials(), 0, 2)) : '??',
                'is_verified' => filled($client?->email_verified_at),
                'location' => trim((string) ($client?->address ?? '')) ?: 'Location not set',
                'client_since' => $clientSince ?? '—',
                'pets_label' => $petsLabel,
                'avatar_url' => $this->clientAvatarUrl($client),
                'upcoming_count' => $upcoming->count(),
                'completed_count' => $completed->count(),
                'total_paid' => (float) $completed->sum('amount'),
                'avg_rating' => $avgRating,
                'is_active' => $upcoming->isNotEmpty(),
                'is_blocked' => $isBlocked,
            ],
            'tab_counts' => [
                'upcoming' => $upcoming->count(),
                'pets' => $petCount,
                'bookings' => $bookings->count(),
                'reviews' => $this->profileReviews->count(),
                'payments' => $this->profilePayments->count(),
            ],
        ];
    }

    #[Computed]
    public function profileMeta(): array
    {
        return $this->profileSummary['meta'];
    }

    #[Computed]
    public function profileTabCounts(): array
    {
        return $this->profileSummary['tab_counts'];
    }

    #[Computed]
    public function profileTabBookings(): Collection
    {
        $bookings = $this->profileBookings;

        $rows = match ($this->profileActiveTab) {
            'upcoming' => $this->filterUpcomingBookings($bookings),
            'bookings' => $bookings,
            default => collect(),
        };

        $rows = match ($this->profileSort) {
            'date_desc' => $rows->sortByDesc(fn($booking) => $booking->date?->timestamp ?? 0),
            'amount_high' => $rows->sortByDesc(fn($booking) => (float) $booking->amount),
            'amount_low' => $rows->sortBy(fn($booking) => (float) $booking->amount),
            default => $rows->sortBy(fn($booking) => $booking->date?->timestamp ?? PHP_INT_MAX),
        };

        return $rows->values();
    }

    #[Computed]
    public function profileReviews(): Collection
    {
        if (!$this->selectedClientId) {
            return collect();
        }

        return Review::query()
            ->where('pet_owner_id', $this->selectedClientId)
            ->whereHas('booking', fn($query) => $query->where('goormer_spacer_id', $this->spacerId()))
            ->with(['booking:' . implode(',', self::BOOKING_LIST_COLUMNS)])
            ->get();
    }

    #[Computed]
    public function profileTabReviews(): Collection
    {
        $reviews = $this->profileReviews;

        $rows = match ($this->profileSort) {
            'date_desc' => $reviews->sortByDesc(fn($review) => $review->booking?->date?->timestamp ?? 0),
            'amount_high' => $reviews->sortByDesc(fn($review) => (float) ($review->booking?->amount ?? 0)),
            'amount_low' => $reviews->sortBy(fn($review) => (float) ($review->booking?->amount ?? 0)),
            default => $reviews->sortBy(fn($review) => $review->booking?->date?->timestamp ?? PHP_INT_MAX),
        };

        return $rows->values();
    }

    #[Computed]
    public function profileVisibleTabBookings(): Collection
    {
        return $this->profileTabBookings->take($this->profilePerPage);
    }

    #[Computed]
    public function profileVisibleTabReviews(): Collection
    {
        return $this->profileTabReviews->take($this->profilePerPage);
    }

    #[Computed]
    public function profilePayments(): Collection
    {
        if (!$this->selectedClientId) {
            return collect();
        }

        return Payment::query()
            ->where('pet_owner_id', $this->selectedClientId)
            ->whereHas('booking', fn($query) => $query->where('goormer_spacer_id', $this->spacerId()))
            ->with(['booking:' . implode(',', self::BOOKING_LIST_COLUMNS), 'pet:' . implode(',', self::PET_LIST_COLUMNS)])
            ->get();
    }

    #[Computed]
    public function profileTabPayments(): Collection
    {
        $payments = $this->profilePayments;

        $rows = match ($this->profileSort) {
            'date_desc' => $payments->sortByDesc(fn($payment) => $payment->date?->timestamp ?? 0),
            'amount_high' => $payments->sortByDesc(fn($payment) => (float) $payment->amount),
            'amount_low' => $payments->sortBy(fn($payment) => (float) $payment->amount),
            default => $payments->sortBy(fn($payment) => $payment->date?->timestamp ?? PHP_INT_MAX),
        };

        return $rows->values();
    }

    #[Computed]
    public function profileVisibleTabPayments(): Collection
    {
        return $this->profileTabPayments->take($this->profilePerPage);
    }

    #[Computed]
    public function profileCanLoadMore(): bool
    {
        if ($this->profileActiveTab === 'reviews') {
            return $this->profileVisibleTabReviews->count() < $this->profileTabReviews->count();
        }

        if ($this->profileActiveTab === 'payments') {
            return $this->profileVisibleTabPayments->count() < $this->profileTabPayments->count();
        }

        return in_array($this->profileActiveTab, ['upcoming', 'bookings'], true) && $this->profileVisibleTabBookings->count() < $this->profileTabBookings->count();
    }

    #[Computed]
    public function profileCompletedBooking(): ?Booking
    {
        if (!$this->completedBookingId) {
            return null;
        }

        return $this->profileBookings->firstWhere('id', $this->completedBookingId);
    }

    public function formatProfileLocationLabel(?string $visitType): string
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

    public function formatProfileSpaceLabel(?string $visitType): string
    {
        $raw = trim((string) $visitType);

        if ($raw === '') {
            return 'N/A';
        }

        if (str_contains($raw, '/') || str_contains($raw, ' ')) {
            return $raw;
        }

        $normalized = str_replace('_', ' ', strtolower($raw));

        return match ($normalized) {
            'garden shed', 'garden/shed' => 'Garden / Shed',
            'salon', 'salon visit' => 'Salon',
            default => ucwords($normalized),
        };
    }

    public function formatProfileBookingTimeDisplay(string $raw, bool $stripExtras = false): string
    {
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

            $diffMinutes = max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
            $hours = (int) floor($diffMinutes / 60);
            $minutes = (int) ($diffMinutes % 60);
            $durationLabel = $minutes === 0 ? $hours . 'hr' : $hours . 'hr ' . $minutes . 'm';

            $startMeridiem = strtolower($startDt->format('a'));
            $endMeridiem = strtolower($endDt->format('a'));

            $display = $startMeridiem === $endMeridiem ? $startDt->format('H:i') . ' - ' . $endDt->format('H:i') . ' ' . $startMeridiem . ' (' . $durationLabel . ')' : $startDt->format('H:i a') . ' - ' . $endDt->format('H:i a') . ' (' . $durationLabel . ')';

            if ($stripExtras) {
                $display = trim((string) preg_replace('/\s*\([^)]*\)\s*$/', '', $display));
                $display = trim((string) preg_replace('/\s+(am|pm)$/i', '', $display));
            }

            return $display;
        } catch (\Throwable $e) {
            return $raw;
        }
    }
}; ?>

@php
    $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
    $profileWireTargets =
        'viewProfile, closeProfile, setProfileTab, setProfileSort, setProfilePetSort, loadMoreProfile, viewPetDetails, closePetDetails, openCompletedBookingModal, closeCompletedBookingModal, toggleReviewReply, closeReviewReply, submitReviewReply';
@endphp

<div class="clients-section" x-data="{
    profileLoading: false,
    openProfile(clientId) {
        this.profileLoading = true;
        $wire.viewProfile(clientId).finally(() => {
            this.profileLoading = false;
        });
    },
    closeProfileView() {
        $wire.set('selectedClientId', null);
        $wire.set('selectedPetId', null);
    },
    closePetDetailsView() {
        $wire.set('selectedPetId', null);
        $wire.set('profileActiveTab', 'pets');
    },
}"
    x-effect="window.dispatchEvent(new CustomEvent('client-profile-visible', { detail: { visible: !!$wire.selectedClientId } }))">
    <div x-show="profileLoading && !$wire.selectedClientId" x-cloak class="clients-profile-opening" aria-busy="true"
        aria-label="Loading client profile">
        <div class="client-profile-back-loader is-visible" aria-hidden="true">
            <div class="active-section-loading-bar">
                <span class="active-section-loading-bar__sweep"></span>
            </div>
        </div>
    </div>

    @if ($selectedClientId)
        <div class="clients-profile-host" x-show="$wire.selectedClientId" x-cloak
            x-transition:enter="client-profile-panel-enter" x-transition:enter-start="client-profile-panel-enter-start"
            x-transition:enter-end="client-profile-panel-enter-end" wire:loading.class="is-profile-loading"
            wire:target="viewProfile, setProfileTab, setProfileSort, setProfilePetSort, loadMoreProfile, viewPetDetails, closePetDetails">
            @include('livewire.business-hub.clients.partials.profile-panel')
        </div>
    @endif

    <section class="clients-list-wrapper" wire:key="clients-list-panel" aria-label="Clients list"
        x-show="!$wire.selectedClientId" x-cloak>
        <div class="clients-list-toolbar">
            <div class="clients-pill-row">
                @php
                    $clientPills = [
                        ['filter' => 'all', 'label' => 'All Clients', 'class' => 'all'],
                        ['filter' => 'repeat', 'label' => 'Repeat Clients', 'class' => 'repeat'],
                        ['filter' => 'recent', 'label' => 'Recently Booked', 'class' => 'recent'],
                    ];
                    if (in_array($activeFilter, ['all', 'repeat', 'recent'], true)) {
                        usort($clientPills, function ($a, $b) use ($activeFilter) {
                            return ($b['filter'] === $activeFilter) <=> ($a['filter'] === $activeFilter);
                        });
                    }
                @endphp
                @foreach ($clientPills as $pill)
                    <button type="button" wire:click="setActiveFilter('{{ $pill['filter'] }}')"
                        @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))"
                        class="clients-pill {{ $pill['class'] }} {{ $activeFilter !== $pill['filter'] ? 'is-muted' : '' }}">
                        {{ $pill['label'] }} ({{ $this->tabCounts[$pill['filter']] ?? 0 }})
                    </button>
                @endforeach
            </div>

            <div class="clients-list-actions">
                <label class="clients-search">
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Type to search..." />
                    <span class="clients-search-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <path
                                d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z"
                                stroke="#A8A8A8" />
                            <path
                                d="M14.6466 15.3547C14.8419 15.55 15.1585 15.55 15.3537 15.3547C15.549 15.1594 15.549 14.8429 15.3537 14.6476L15.0002 15.0011L14.6466 15.3547ZM9.70605 9.70703L9.3525 10.0606L14.6466 15.3547L15.0002 15.0011L15.3537 14.6476L10.0596 9.35348L9.70605 9.70703Z"
                                fill="#A8A8A8" />
                        </svg>
                    </span>
                </label>

                <div class="clients-list-sort" x-data="{
                    open: false,
                    menuLeft: 0,
                    menuTop: 0,
                    menuWidth: 220,
                    repositionMenu() {
                        const rect = $refs.sortBtn.getBoundingClientRect();
                        this.menuLeft = Math.max(8, rect.right - this.menuWidth);
                        this.menuTop = rect.bottom + 8;
                    },
                    toggleMenu() {
                        if (!this.open) {
                            this.repositionMenu();
                        }
                        this.open = !this.open;
                    }
                }" @keydown.escape.window="open = false"
                    @resize.window="if (open) repositionMenu()" @scroll.window="if (open) repositionMenu()"
                    @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                    <div class="sort-dropdown">
                        <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
                            aria-label="Sort clients" :aria-expanded="open.toString()">
                            <span>Sort</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                                fill="none">
                                <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <template x-teleport="body">
                            <div class="sort-menu clients-list-sort-menu" x-cloak x-show="open" x-ref="sortMenu"
                                x-transition.opacity.duration.100ms
                                :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; z-index: 99999;`">
                                @foreach ([
        'name_asc' => 'Name (A–Z)',
        'name_desc' => 'Name (Z–A)',
        'bookings_desc' => 'Most Bookings',
        'bookings_asc' => 'Fewest Bookings',
        'paid_desc' => 'Highest Paid',
        'paid_asc' => 'Lowest Paid',
        'upcoming_asc' => 'Upcoming Booking',
    ] as $sortKey => $sortLabel)
                                    <button type="button" class="sort-options"
                                        :class="{ 'is-active': @js($sort) === '{{ $sortKey }}' }"
                                        wire:click="setSort('{{ $sortKey }}')"
                                        @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); open = false">
                                        <span>{{ $sortLabel }}</span>
                                        <span class="sort-indicator"></span>
                                    </button>
                                @endforeach
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="clients-list-table-shell">
            <table class="clients-list-table">
                <thead>
                    <tr>
                        <th style="text-align: center;width: 15rem;">Client Name</th>
                        @unless ($isSpaceUser)
                            <th>Pets</th>
                        @endunless
                        <th>Upcoming Booking</th>
                        <th>Total Bookings</th>
                        <th>Total Paid</th>
                        <th class="clients-view-col">View Profile</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->visibleClients as $client)
                        <tr wire:key="client-row-{{ $client['id'] }}">
                            <td>
                                <div class="clients-name-cell">
                                    <span class="clients-avatar-wrap" aria-hidden="true">
                                        <span class="clients-avatar">{{ $client['initials'] }}</span>
                                    </span>
                                    <div class="clients-name-meta">
                                        <span class="clients-name">{{ $client['name'] }}</span>
                                        <span
                                            class="clients-badge {{ $client['is_repeat'] ? 'is-repeat' : 'is-new' }}">
                                            {{ $client['is_repeat'] ? 'Repeat Client' : 'New Client' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            @unless ($isSpaceUser)
                                <td>
                                    @if ($client['pets']->isEmpty())
                                        —
                                    @elseif ($client['pets']->count() === 1)
                                        @php $pet = $client['pets']->first(); @endphp
                                        <div class="clients-pet-cell">
                                            <span
                                                class="clients-pet-name">{{ trim((string) ($pet->name ?? '')) ?: '—' }}</span>
                                            @if (trim((string) ($pet->pet_type ?? '')) !== '')
                                                <span style="color: #9D9B98;font-weight: 400;">{{ $pet->pet_type }}</span>
                                            @endif
                                        </div>
                                    @else
                                        +{{ $client['pets']->count() }} Pets
                                    @endif
                                </td>
                            @endunless
                            <td style="font-weight: 400;">{{ $client['upcoming_date'] ?? '—' }}</td>
                            <td>{{ $client['total_bookings'] }}</td>
                            <td>£{{ number_format($client['total_paid'], 2) }}</td>
                            <td class="clients-view-col">
                                <button type="button" class="clients-view-btn"
                                    @click="openProfile({{ $client['id'] }})"
                                    aria-label="View {{ $client['name'] }} profile">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="M1 13A9 9 0 0 1 19 13" stroke="black" stroke-width="1"
                                            stroke-linecap="butt" />
                                        <circle cx="10" cy="13" r="4" stroke="black"
                                            stroke-width="1" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSpaceUser ? 5 : 6 }}" class="clients-empty-cell">No clients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->canLoadMore)
            <div class="clients-load-more-wrap">
                <button type="button" class="clients-load-more-btn"
                    x-on:click="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))" wire:click="loadMore"
                    wire:loading.attr="disabled" wire:target="loadMore">
                    <span wire:loading.remove wire:target="loadMore">Load More</span>
                    <span class="clients-load-more-loading" wire:loading.inline-flex wire:target="loadMore">
                        <span class="clients-load-more-spinner" aria-hidden="true"></span>
                    </span>
                </button>
            </div>
        @endif
    </section>

    <x-business-hub.common.completed-booking-modal :booking="$this->profileCompletedBooking" />
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .clients-section {
        width: 100%;
    }

    .clients-profile-host {
        margin-top: 0;
        position: relative;
    }

    .clients-profile-host.is-profile-loading {
        pointer-events: none;
    }

    .client-profile-back-block {
        margin-bottom: 4rem;
    }

    .client-profile-back {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        border: 0;
        background: transparent;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        margin-bottom: 0.75rem;
    }

    .clients-profile-opening {
        margin-bottom: 1.5rem;
    }

    .client-profile-back-loader {
        display: none;
        position: relative;
        height: 4px;
    }

    .client-profile-back-loader.is-visible {
        display: block;
    }

    .client-profile-back-loader .active-section-loading-bar {
        position: relative;
        left: 0;
        right: 0;
        bottom: auto;
        height: 4px;
    }

    .client-profile-panel-enter {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .client-profile-panel-enter-start {
        opacity: 0;
        transform: translateY(20px);
    }

    .client-profile-panel-enter-end {
        opacity: 1;
        transform: translateY(0);
    }

    .client-profile-panel-leave {
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .client-profile-panel-leave-start {
        opacity: 1;
        transform: translateY(0);
    }

    .client-profile-panel-leave-end {
        opacity: 0;
        transform: translateY(12px);
    }

    .clients-list-wrapper {
        margin-top: 4rem;
    }

    .clients-list-toolbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }

    .clients-pill-row {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
    }

    .clients-pill {
        text-align: center;
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

    .clients-pill.all {
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.10);
    }

    .clients-pill.repeat {
        color: #AFCD6F;
        background: rgba(175, 205, 111, 0.10);
    }

    .clients-pill.recent {
        color: #9FC7E4;
        background: rgba(159, 199, 228, 0.10);
    }

    .clients-pill.is-muted {
        opacity: 0.5;
        background: #ECEBEB;
        color: #9D9B98;
    }

    .clients-list-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.5rem;
        margin-left: auto;
    }

    .clients-search {
        position: relative;
        display: flex;
        align-items: center;
        width: 200px;
        height: 42px;
        max-width: 100%;
    }

    .clients-search input {
        width: 100%;
        min-width: 0;
        height: 42px;
        border-radius: 83px;
        border: 1px solid #A8A8A8;
        padding: 0 35px 0 15px;
        color: #8b8781;
        font-size: 12px;
        font-family: Lato, sans-serif;
        outline: none;
    }

    .clients-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: inline-flex;
    }

    .clients-list-sort .sort-dropdown {
        position: relative;
    }

    .clients-list-sort .sort-trigger {
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
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    .clients-list-sort-menu {
        min-width: 220px;
        width: max-content;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
        overflow: hidden;
    }

    .clients-list-sort-menu .sort-options {
        width: 100%;
        border: 0;
        border-bottom: 2px solid #e6e6e5;
        background: #FFF;
        padding: 1rem;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .clients-list-sort-menu .sort-options:last-child {
        border-bottom: none;
    }

    .clients-list-sort-menu .sort-options:hover {
        background: #F2F2F2;
    }

    .clients-list-sort-menu .sort-indicator {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: transparent;
        position: relative;
        flex-shrink: 0;
    }

    .clients-list-sort-menu .sort-options.is-active .sort-indicator::after {
        content: '';
        position: absolute;
        inset: 2px;
        border-radius: 999px;
        background: #FFC97A;
    }

    .clients-list-table-shell {
        overflow-x: auto;
    }

    .clients-list-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .clients-list-table th,
    .clients-list-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        padding: 1.2rem 0;
        vertical-align: middle;
        width: 10rem;
    }

    .clients-list-table th {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .clients-list-table td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .clients-list-table .clients-view-col {
        text-align: center;
        width: 120px;
        border-left: 1px solid #E2E2E2;
    }

    .clients-name-cell {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .clients-avatar-wrap {
        width: 44px;
        height: 44px;
        box-sizing: border-box;
        padding: 2px;
        border-radius: 999px;
        overflow: hidden;
        background: #FFF;
        border: 1px solid #FFC97A;
        display: inline-flex;
        flex-shrink: 0;
    }

    .clients-avatar {
        width: 100%;
        height: 100%;
        border-radius: 999px;
        background: #f0ebe4;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .clients-name-meta {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.35rem;
    }

    .clients-name {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .clients-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        height: 32px;
        border-radius: 100px;
        padding: 0.2rem 0.65rem;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .clients-badge.is-new {
        color: #AFCD6F;
        background: rgba(186, 207, 142, 0.10);
    }

    .clients-badge.is-repeat {
        color: #94BEDB;
        background: rgba(216, 229, 238, 0.20);
    }

    .clients-pet-cell {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .clients-view-btn {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .clients-empty-cell {
        text-align: center !important;
        color: #9D9B98 !important;
        padding: 2rem 0 !important;
    }

    .clients-load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 4rem;
    }

    .clients-load-more-btn {
        width: 133px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 75px;
        border: 1px solid #3B3731;
        background: transparent;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }

    .clients-load-more-btn[disabled] {
        opacity: 0.9;
        cursor: wait;
    }

    .clients-load-more-loading {
        display: none;
        align-items: center;
        justify-content: center;
    }

    .clients-load-more-spinner {
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        border: 2px solid #3B3731;
        border-top-color: transparent;
        animation: clients-load-more-spin 0.7s linear infinite;
    }

    @keyframes clients-load-more-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
