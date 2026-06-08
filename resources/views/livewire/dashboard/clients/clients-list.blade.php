<?php

use App\Models\Booking;
use App\Models\PetDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Volt\Component;

new class extends Component {
    public string $activeFilter = 'all';

    public string $search = '';

    public string $sort = 'name_asc';

    public int $perPage = 6;

    private function spacerId(): int
    {
        return (int) (auth('groomer_spacer')->id() ?? 0);
    }

    private function bookingsForSpacer(): Collection
    {
        return Booking::query()
            ->where('goormer_spacer_id', $this->spacerId())
            ->whereNotNull('pet_owner_id')
            ->with(['petOwner:id,name', 'pets:id,name,pet_type,photo,user_id'])
            ->get();
    }

    public function getAllClientRowsProperty(): Collection
    {
        $bookings = $this->bookingsForSpacer();

        if ($bookings->isEmpty()) {
            return collect();
        }

        $petsByUser = PetDetail::query()
            ->whereIn('user_id', $bookings->pluck('pet_owner_id')->unique())
            ->get()
            ->groupBy('user_id');

        return $bookings
            ->groupBy('pet_owner_id')
            ->map(function (Collection $ownerBookings, $ownerId) use ($petsByUser) {
                $owner = $ownerBookings->first()->petOwner;
                $completed = $ownerBookings->where('booking_status', 'completed');
                $completedCount = $completed->count();

                $upcoming = $ownerBookings
                    ->filter(function ($booking) {
                        if (!in_array($booking->booking_status, ['pending', 'confirmed'], true)) {
                            return false;
                        }

                        return $booking->date && $booking->date->gte(today());
                    })
                    ->sortBy(fn($booking) => $booking->date?->timestamp ?? PHP_INT_MAX)
                    ->first();

                $userPets = $petsByUser->get($ownerId) ?? collect();
                if ($userPets->isEmpty()) {
                    $userPets = $ownerBookings->flatMap(fn($booking) => $booking->pets)->unique('id')->values();
                }

                $lastBookingAt = $ownerBookings->max(fn($booking) => $booking->created_at?->timestamp ?? 0);

                return [
                    'id' => (int) $ownerId,
                    'name' => $owner?->name ?? 'Unknown',
                    'initials' => $owner ? Str::upper(Str::substr($owner->initials(), 0, 2)) : '??',
                    'is_repeat' => $completedCount > 1,
                    'pets' => $userPets,
                    'upcoming_date' => $upcoming?->date?->format('d/m/Y'),
                    'total_bookings' => $ownerBookings->count(),
                    'total_paid' => (float) $completed->sum('amount'),
                    'last_booking_at' => (int) $lastBookingAt,
                    'recently_booked' => $lastBookingAt >= now()->subDays(60)->getTimestamp(),
                ];
            })
            ->values();
    }

    public function getTabCountsProperty(): array
    {
        $rows = $this->allClientRows;

        return [
            'all' => $rows->count(),
            'repeat' => $rows->where('is_repeat', true)->count(),
            'recent' => $rows->where('recently_booked', true)->count(),
        ];
    }

    public function getClientsProperty(): Collection
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

    public function getVisibleClientsProperty(): Collection
    {
        return $this->clients->take($this->perPage);
    }

    public function getCanLoadMoreProperty(): bool
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
}; ?>

@php
    $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
@endphp

<section class="clients-list-wrapper" aria-label="Clients list">
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

            <div class="clients-list-sort" x-data="{ open: false }" @keydown.escape.window="open = false">
                <div class="sort-dropdown">
                    <button type="button" class="sort-trigger" @click="open = !open" aria-label="Sort clients"
                        :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                            fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="sort-menu" x-cloak x-show="open" @click.outside="open = false"
                        x-transition.opacity.duration.100ms>
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
                                <span class="clients-avatar" aria-hidden="true">{{ $client['initials'] }}</span>
                                <div class="clients-name-meta">
                                    <span class="clients-name">{{ $client['name'] }}</span>
                                    <span class="clients-badge {{ $client['is_repeat'] ? 'is-repeat' : 'is-new' }}">
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
                                aria-label="View {{ $client['name'] }} profile">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M1 13A9 9 0 0 1 19 13" stroke="black" stroke-width="1"
                                        stroke-linecap="butt" />
                                    <circle cx="10" cy="13" r="4" stroke="black" stroke-width="1" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isSpaceUser ? 5 : 6 }}" class="clients-empty-cell">No clients found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($this->canLoadMore)
        <div class="clients-load-more-wrap">
            <button type="button" class="clients-load-more-btn" wire:click="loadMore" wire:loading.attr="disabled"
                wire:target="loadMore">
                <span wire:loading.remove wire:target="loadMore">Load More</span>
                <span class="clients-load-more-loading" wire:loading.inline-flex wire:target="loadMore">
                    <span class="clients-load-more-spinner" aria-hidden="true"></span>
                </span>
            </button>
        </div>
    @endif
</section>

<style>
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

    .clients-list-sort .sort-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        min-width: 220px;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
        z-index: 20;
        overflow: hidden;
    }

    .clients-list-sort .sort-options {
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

    .clients-list-sort .sort-options:last-child {
        border-bottom: none;
    }

    .clients-list-sort .sort-options:hover {
        background: #F2F2F2;
    }

    .clients-list-sort .sort-indicator {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: transparent;
        position: relative;
        flex-shrink: 0;
    }

    .clients-list-sort .sort-options.is-active .sort-indicator::after {
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

    .clients-avatar {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: #f0ebe4;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
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
