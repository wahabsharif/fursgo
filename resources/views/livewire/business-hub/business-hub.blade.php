<?php

use App\Models\Booking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public $activeTab = 'overview';
    public ?string $activeModal = null;
    public $showAllCards = [
        'todays' => false,
        'pending' => false,
        'upcoming' => false,
    ];

    public function openCardModal(string $card): void
    {
        if (!array_key_exists($card, $this->showAllCards)) {
            return;
        }
        $this->activeModal = $card;
        $this->showAllCards[$card] = true;
    }

    public function closeCardModal(): void
    {
        if ($this->activeModal && array_key_exists($this->activeModal, $this->showAllCards)) {
            $this->showAllCards[$this->activeModal] = false;
        }
        $this->activeModal = null;
    }

    private function visitTypeLabel(string $visitType): string
    {
        return match ($visitType) {
            'home_visit' => 'Home Visit',
            'salon' => 'Salon',
            'mobile_station' => 'Mobile Station',
            'garden_shed' => 'Garden / Shed',
            default => ucfirst(str_replace('_', ' ', $visitType)),
        };
    }

    private function visitTypeBadgeLabel($booking): string
    {
        $visitType = trim((string) ($booking->visit_type ?? ''));
        if ($visitType !== '') {
            return $this->visitTypeLabel($visitType);
        }

        return '—';
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
        return $booking->relationLoaded('pets') ? $booking->pets : $booking->pets()->get();
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

    private function loggedInSpacerId(): ?int
    {
        return auth('groomer_spacer')->id();
    }

    private function bookingsQuery()
    {
        $spacerId = $this->loggedInSpacerId();

        return Booking::query()->where('goormer_spacer_id', $spacerId ?? 0);
    }

    public function getTodaysBookings()
    {
        return $this->bookingsQuery()
            ->with('pets')
            ->today()
            ->where('booking_status', '!=', 'cancelled')
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->when(!$this->showAllCards['todays'], fn($q) => $q->limit(2))
            ->get()
            ->map(
                fn($b) => [
                    'id' => 'FG-' . str_pad($b->id, 5, '0', STR_PAD_LEFT),
                    'pet_image' => $b->pets->first()?->photo ?? 'https://i.pravatar.cc/150?img=12',
                    'pet_images' => $b->pets->pluck('photo')->filter()->values()->all(),
                    'service_type' => $this->visitTypeBadgeLabel($b),
                    'date' => $b->date?->format('d/m/Y'),
                    'time' => $b->time,
                    'service' => $b->service,
                    'pet_type' => $this->resolveBookingPetType($b),
                    'weight' => $this->resolveBookingPetWeight($b),
                ],
            )
            ->toArray();
    }

    public function getTodaysBookingsCount(): int
    {
        return $this->bookingsQuery()->today()->where('booking_status', '!=', 'cancelled')->count();
    }

    public function getPendingRequests()
    {
        return $this->bookingsQuery()
            ->with('pets')
            ->pending()
            ->whereDate('date', '>=', today())
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->when(!$this->showAllCards['pending'], fn($q) => $q->limit(2))
            ->get()
            ->map(
                fn($b) => [
                    'id' => 'FG-' . str_pad($b->id, 5, '0', STR_PAD_LEFT),
                    'pet_image' => $b->pets->first()?->photo ?? 'https://i.pravatar.cc/150?img=15',
                    'pet_images' => $b->pets->pluck('photo')->filter()->values()->all(),
                    'pet_type' => $this->resolveBookingPetType($b),
                    'weight' => $this->resolveBookingPetWeight($b),
                    'service_type' => $this->visitTypeBadgeLabel($b),
                    'date' => $b->date->format('d/m/Y'),
                    'time' => $b->time,
                    'price' => (float) $b->amount,
                    'booking_id' => $b->id,
                ],
            )
            ->toArray();
    }

    public function getPendingRequestsCount(): int
    {
        return $this->bookingsQuery()->pending()->whereDate('date', '>=', today())->count();
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
                    'id' => 'FG-' . str_pad($b->id, 5, '0', STR_PAD_LEFT),
                    'status' => ucfirst($b->booking_status),
                    'pet_image' => $b->pets->first()?->photo ?? 'https://i.pravatar.cc/150?img=20',
                    'pet_images' => $b->pets->pluck('photo')->filter()->values()->all(),
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
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $dailyTotals = $this->bookingsQuery()
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->selectRaw('date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $total = $dailyTotals->sum('total');

        $lastWeekTotal = $this->bookingsQuery()
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->whereBetween('date', [$startOfWeek->copy()->subWeek(), $endOfWeek->copy()->subWeek()])
            ->sum('amount');

        $change = $lastWeekTotal > 0 ? round((($total - $lastWeekTotal) / $lastWeekTotal) * 100, 1) : 100.0;

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $weekBuckets = [1 => 0.0, 2 => 0.0, 3 => 0.0, 4 => 0.0];
        $monthlySales = $this->bookingsQuery()
            ->whereIn('booking_status', ['confirmed', 'completed'])
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->selectRaw('CEIL(DAY(date) / 7) as week_bucket, COALESCE(SUM(amount), 0) as total')
            ->groupBy('week_bucket')
            ->pluck('total', 'week_bucket');

        foreach ($monthlySales as $weekIndex => $total) {
            $weekIndex = max(1, min(4, (int) $weekIndex));
            $weekBuckets[$weekIndex] = (float) $total;
        }

        $monthNumber = $startOfMonth->format('m');
        $chartData = collect(range(1, 4))
            ->map(
                fn($week) => [
                    'date' => str_pad((string) $week, 2, '0', STR_PAD_LEFT) . '/' . $monthNumber,
                    'amount' => (float) $weekBuckets[$week],
                ],
            )
            ->toArray();

        return [
            'total' => (float) $total,
            'change' => $change,
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
        $profileVisits = (int) (auth('groomer_spacer')->user()?->profile_visit ?? 0);

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
        $booking = $this->bookingsQuery()->findOrFail($bookingId);
        $booking->update(['booking_status' => 'confirmed']);

        $spacerId = $this->loggedInSpacerId();
        if ($spacerId) {
            Cache::forget("dashboard_sidebar_booking_counts_{$spacerId}");
        }

        $this->dispatch('request-accepted', requestId: $bookingId);
    }

    public function viewDetails($bookingId)
    {
        $this->dispatch('view-details', bookingId: $bookingId);
    }

    #[Computed]
    public function weeklyRevenue(): array
    {
        return $this->getWeeklyRevenue();
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

<div x-data="{ activeTab: @entangle('activeTab') }" class="business-hub-container"
    wire:loading.class="business-hub-container--navigating"
    wire:target="openCardModal,closeCardModal,acceptRequest,viewDetails">
    @php
        $activeColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
        $lightColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFE8E4' : '#FFF8EB';
        $chartColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
        $bookingItemHoverBg = auth()->check() && auth()->user()->user_type === 'space' ? '#FFF7F5' : '#FFFBF4';
    @endphp

    <div class="business-hub-step-loading-bar" wire:loading
        wire:target="openCardModal,closeCardModal,acceptRequest,viewDetails" aria-hidden="true">
        <span class="business-hub-step-loading-bar__sweep"></span>
    </div>

    <style>
        :root {
            --business-hub-active:
                {{ $activeColor }}
            ;
            --business-hub-light:
                {{ $lightColor }}
            ;
            --business-hub-chart:
                {{ $chartColor }}
            ;
            --booking-item-hover-bg:
                {{ $bookingItemHoverBg }}
            ;
            --weekly-revenue-axis: #D9D5CF;
        }

        .business-hub-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 3rem 0;
            flex: 1 1 0;
            min-height: 0;
            width: 100%;
            align-self: stretch;
            position: relative;
        }

        .business-hub-container--navigating {
            cursor: wait;
        }

        .business-hub-step-loading-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            overflow: hidden;
            background: rgba(232, 228, 222, 0.85);
            z-index: 5;
            border-radius: 2px;
            pointer-events: none;
        }

        .business-hub-step-loading-bar__sweep {
            position: absolute;
            top: 0;
            left: -42%;
            height: 100%;
            width: 42%;
            border-radius: 2px;
            background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
            box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
            will-change: left;
            animation: bh-step-load-sweep 1.1s linear infinite;
        }

        @keyframes bh-step-load-sweep {
            0% {
                left: -42%;
            }

            100% {
                left: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .business-hub-step-loading-bar__sweep {
                animation: none;
                left: 0;
                width: 100%;
                opacity: 0.85;
            }
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
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .booking-item:hover {
            background: var(--booking-item-hover-bg);
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
            padding: 2px;
            border: 2px solid #FFC97A;
            border-radius: 50%;
            object-fit: cover;
            background: #fff;
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

        /* Pending Requests — grey panel only on first row */
        .request-item {
            padding: 1rem;
        }

        .request-item.request-item--odd {
            background: #F6F6F6;
            border-radius: 10px;
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

        /* Bottom Row — Figma: 610 / 400 with 20px gap */
        .dashboard-bottom-row {
            margin-top: 1rem;
            display: grid;
            grid-template-columns: minmax(0, 610fr) minmax(0, 400fr);
            gap: 1.25rem;
            align-items: start;
        }

        .upcoming-bookings {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .upcoming-bookings .card-content {
            flex: 1;
            max-height: none;
            overflow-y: auto;
        }

        .upcoming-bookings>.view-all-link {
            flex-shrink: 0;
            margin-top: 0.75rem;
        }

        /* Upcoming Bookings */
        .upcoming-booking-item {
            border: 1px solid #D8E8B7;
            border-radius: 12px;
            margin-bottom: 1.5rem;
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
        .stats-column {
            min-width: 0;
        }

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
            font-size: 10px !important;
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
    </style>

    @php
        $isSpaceAccount = (auth('groomer_spacer')->user()?->user_type ?? '') === 'space';
    @endphp

    <!-- Top Row: Today's Bookings, Weekly Revenue, Pending Requests -->
    <div class="dashboard-top-row">
        <!-- Today's Bookings -->
        <div class="dashboard-card todays-bookings">
            <div class="card-header">
                <h3>Today's Bookings <span class="count">({{ $this->todaysBookingsCount }})</span></h3>
            </div>
            <div class="card-content">
                @foreach (collect($this->todaysBookings)->take(2) as $booking)
                    <div class="booking-item">
                        <div class="booking-header">
                            <div class="pet-avatar-stack">
                                @foreach (!empty($booking['pet_images']) ? $booking['pet_images'] : [$booking['pet_image']] as $petImage)
                                    <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
                                @endforeach
                            </div>
                            <span
                                class="service-badge">{{ $isSpaceAccount ? 'Garden / Shed' : $booking['service_type'] }}</span>
                        </div>
                        <div class="bh-booking-details">
                            @if ($isSpaceAccount)
                                <div class="detail-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                        fill="none">
                                        <path
                                            d="M0.5 6.8658C0.5 4.33891 0.5 3.07513 1.3204 2.29047C2.1408 1.5058 3.4603 1.50513 6.1 1.50513H8.9C11.5397 1.50513 12.8599 1.50513 13.6796 2.29047C14.4993 3.07581 14.5 4.33891 14.5 6.8658V8.20597C14.5 10.7329 14.5 11.9966 13.6796 12.7813C12.8592 13.566 11.5397 13.5666 8.9 13.5666H6.1C3.4603 13.5666 2.1401 13.5666 1.3204 12.7813C0.5007 11.996 0.5 10.7329 0.5 8.20597V6.8658Z"
                                            stroke="#3B3731" />
                                        <path d="M4.0001 1.50513V0.5M11.0001 1.50513V0.5M0.850098 4.85555H14.1501"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M11.7 10.2161C11.7 10.3939 11.6263 10.5643 11.495 10.69C11.3637 10.8156 11.1857 10.8862 11 10.8862C10.8144 10.8862 10.6363 10.8156 10.5051 10.69C10.3738 10.5643 10.3 10.3939 10.3 10.2161C10.3 10.0384 10.3738 9.86799 10.5051 9.74232C10.6363 9.61666 10.8144 9.54606 11 9.54606C11.1857 9.54606 11.3637 9.61666 11.495 9.74232C11.6263 9.86799 11.7 10.0384 11.7 10.2161ZM11.7 7.53581C11.7 7.71352 11.6263 7.88396 11.495 8.00963C11.3637 8.13529 11.1857 8.20589 11 8.20589C10.8144 8.20589 10.6363 8.13529 10.5051 8.00963C10.3738 7.88396 10.3 7.71352 10.3 7.53581C10.3 7.35809 10.3738 7.18765 10.5051 7.06199C10.6363 6.93632 10.8144 6.86572 11 6.86572C11.1857 6.86572 11.3637 6.93632 11.495 7.06199C11.6263 7.18765 11.7 7.35809 11.7 7.53581ZM8.20005 10.2161C8.20005 10.3939 8.1263 10.5643 7.99502 10.69C7.86375 10.8156 7.6857 10.8862 7.50005 10.8862C7.3144 10.8862 7.13635 10.8156 7.00507 10.69C6.8738 10.5643 6.80005 10.3939 6.80005 10.2161C6.80005 10.0384 6.8738 9.86799 7.00507 9.74232C7.13635 9.61666 7.3144 9.54606 7.50005 9.54606C7.6857 9.54606 7.86375 9.61666 7.99502 9.74232C8.1263 9.86799 8.20005 10.0384 8.20005 10.2161ZM8.20005 7.53581C8.20005 7.71352 8.1263 7.88396 7.99502 8.00963C7.86375 8.13529 7.6857 8.20589 7.50005 8.20589C7.3144 8.20589 7.13635 8.13529 7.00507 8.00963C6.8738 7.88396 6.80005 7.71352 6.80005 7.53581C6.80005 7.35809 6.8738 7.18765 7.00507 7.06199C7.13635 6.93632 7.3144 6.86572 7.50005 6.86572C7.6857 6.86572 7.86375 6.93632 7.99502 7.06199C8.1263 7.18765 8.20005 7.35809 8.20005 7.53581ZM4.70005 10.2161C4.70005 10.3939 4.6263 10.5643 4.49502 10.69C4.36375 10.8156 4.1857 10.8862 4.00005 10.8862C3.8144 10.8862 3.63635 10.8156 3.50507 10.69C3.3738 10.5643 3.30005 10.3939 3.30005 10.2161C3.30005 10.0384 3.3738 9.86799 3.50507 9.74232C3.63635 9.61666 3.8144 9.54606 4.00005 9.54606C4.1857 9.54606 4.36375 9.61666 4.49502 9.74232C4.6263 9.86799 4.70005 10.0384 4.70005 10.2161ZM4.70005 7.53581C4.70005 7.71352 4.6263 7.88396 4.49502 8.00963C4.36375 8.13529 4.1857 8.20589 4.00005 8.20589C3.8144 8.20589 3.63635 8.13529 3.50507 8.00963C3.3738 7.88396 3.30005 7.71352 3.30005 7.53581C3.30005 7.35809 3.3738 7.18765 3.50507 7.06199C3.63635 6.93632 3.8144 6.86572 4.00005 6.86572C4.1857 6.86572 4.36375 6.93632 4.49502 7.06199C4.6263 7.18765 4.70005 7.35809 4.70005 7.53581Z"
                                            fill="#3B3731" />
                                    </svg>
                                    <span>{{ $booking['date'] }}</span>
                                </div>
                                <div class="detail-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13"
                                        fill="none">
                                        <path
                                            d="M5.5438 12.2186C2.69175 11.808 0.5 9.35597 0.5 6.39044C0.5 3.13724 3.13744 0.5 6.39088 0.5C9.08249 0.5 11.3519 2.30515 12.0556 4.77069"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M12.5534 9.66857C12.679 9.54095 12.8175 9.43786 12.9687 9.35932C13.1218 9.28079 13.2976 9.24152 13.4959 9.24152C13.6609 9.24152 13.8052 9.269 13.9289 9.32398C14.0546 9.37896 14.1596 9.45652 14.244 9.55665C14.3285 9.65483 14.3923 9.77362 14.4355 9.91303C14.4787 10.0524 14.5003 10.2066 14.5003 10.3754V12.281H13.923V10.3754C13.923 10.1614 13.8739 9.99549 13.7757 9.87768C13.6775 9.75791 13.5273 9.69803 13.3251 9.69803C13.1778 9.69803 13.0394 9.73337 12.9098 9.80405C12.7821 9.87278 12.6633 9.96702 12.5534 10.0868V12.281H11.979V7.93384H12.5534V9.66857Z"
                                            fill="#3B3731" />
                                        <path
                                            d="M11.1689 11.8425V12.2814H8.88325V11.8425H9.77867V9.07109C9.77867 8.98274 9.78161 8.89144 9.7875 8.79719L9.06882 9.40685C9.04133 9.42845 9.01384 9.44219 8.98634 9.44808C8.95885 9.45397 8.93234 9.45496 8.90682 9.45103C8.88325 9.4471 8.86165 9.43925 8.84202 9.42747C8.82435 9.41372 8.80962 9.39998 8.79784 9.38623L8.61816 9.13589L9.8847 8.04321H10.353V11.8425H11.1689Z"
                                            fill="#3B3731" />
                                        <path
                                            d="M6.68523 4.62362C6.68523 4.46096 6.81711 4.3291 6.97978 4.3291C7.14245 4.3291 7.27432 4.46096 7.27432 4.62362V7.27432H5.21251C5.04984 7.27432 4.91797 7.14246 4.91797 6.9798C4.91797 6.81714 5.04984 6.68528 5.21251 6.68528H6.68523V4.62362Z"
                                            fill="#3B3731" />
                                    </svg>
                                    <span>{{ $booking['time'] }}</span>
                                </div>
                                <div class="detail-row">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                        fill="none">
                                        <path
                                            d="M7.5 5.63158C5.38867 5.63158 3.60467 7.52376 3.02867 9.95408C2.77533 11.0228 3.15733 12.1572 4.095 12.6902C4.83833 13.1127 5.944 13.5 7.5 13.5C9.056 13.5 10.162 13.1127 10.9053 12.6902C11.843 12.1572 12.2247 11.0228 11.9713 9.95408C11.3953 7.52342 9.61133 5.63158 7.5 5.63158ZM0.5 5.09926C0.5 6.04416 1.09667 7 1.83333 7C2.57 7 3.16667 6.04416 3.16667 5.09926C3.16667 4.15437 2.57 3.57895 1.83333 3.57895C1.09667 3.57895 0.5 4.15471 0.5 5.09926ZM14.5 5.09926C14.5 6.04416 13.9033 7 13.1667 7C12.43 7 11.8333 6.04416 11.8333 5.09926C11.8333 4.15437 12.43 3.57895 13.1667 3.57895C13.9033 3.57895 14.5 4.15471 14.5 5.09926ZM4 2.02032C4 2.96521 4.59667 3.92105 5.33333 3.92105C6.07 3.92105 6.66667 2.96521 6.66667 2.02032C6.66667 1.07542 6.07 0.5 5.33333 0.5C4.59667 0.5 4 1.07576 4 2.02032ZM11 2.02032C11 2.96521 10.4033 3.92105 9.66667 3.92105C8.93 3.92105 8.33333 2.96521 8.33333 2.02032C8.33333 1.07542 8.93 0.5 9.66667 0.5C10.4033 0.5 11 1.07576 11 2.02032Z"
                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
            </div>
            <a href="#" wire:click.prevent="openCardModal('todays')" class="view-all-link">View All</a>
        </div>

        <!-- Weekly Revenue -->
        <div class="dashboard-card weekly-revenue">
            <div class="card-header">
                <h3>Weekly Revenue</h3>
            </div>
            <div class="card-content">
                <div class="revenue-header">
                    <span class="revenue-amount">£{{ number_format($this->weeklyRevenue['total'], 2) }}</span>
                    <span class="revenue-period">/ week</span>
                    <span class="revenue-change positive"><svg xmlns="http://www.w3.org/2000/svg" width="6" height="9"
                            viewBox="0 0 6 9" fill="none" aria-hidden="true">
                            <path
                                d="M2.91 0L5.8 2.895L5.415 3.265C5.33833 3.34167 5.26167 3.37333 5.185 3.36C5.105 3.34333 5.02833 3.3 4.955 3.23L3.74 2.005C3.65333 1.91833 3.575 1.835 3.505 1.755C3.43167 1.675 3.36667 1.59833 3.31 1.525C3.33333 1.72167 3.35333 1.92833 3.37 2.145C3.38333 2.35833 3.39 2.575 3.39 2.795L3.39 8.93H2.415L2.415 2.795C2.415 2.575 2.42333 2.35667 2.44 2.14C2.45333 1.92333 2.47333 1.71667 2.5 1.52C2.44333 1.59667 2.38 1.675 2.31 1.755C2.23667 1.835 2.15667 1.91833 2.07 2.005L0.845 3.24C0.775 3.31 0.7 3.35333 0.62 3.37C0.54 3.38333 0.461667 3.35167 0.385 3.275L0 2.905L2.91 0Z"
                                fill="currentColor" />
                        </svg> + £{{ number_format($this->weeklyRevenue['change'], 0) }} / last
                        week</span>
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
                            data-labels='@json(array_column($this->weeklyRevenue['chart_data'], 'date'))'
                            data-values='@json(array_column($this->weeklyRevenue['chart_data'], 'amount'))'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="dashboard-card pending-requests">
            <div class="card-header">
                <h3>Pending Requests <span class="count">({{ $this->pendingRequestsCount }})</span></h3>
            </div>
            <div class="card-content">
                @foreach (collect($this->pendingRequests)->take(2) as $request)
                    <div class="request-item {{ $loop->odd ? 'request-item--odd' : '' }}">
                        <div>
                            <div class="pet-avatar-wrap">
                                <div class="pet-avatar-stack">
                                    @foreach (!empty($request['pet_images']) ? $request['pet_images'] : [$request['pet_image']] as $petImage)
                                        <div class="pet-avatar-dot-wrap">
                                            <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                            fill="none">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                            fill="none">
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13"
                                                fill="none">
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
                            <button wire:click="acceptRequest('{{ $request['booking_id'] }}')" class="btn-accept">Accept
                                Request</button>
                            <button wire:click="viewDetails('{{ $request['booking_id'] }}')" class="btn-view">View
                                details</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="#" wire:click.prevent="openCardModal('pending')" class="view-all-link">View All</a>
        </div>
    </div>

    <!-- Bottom Row: Upcoming Bookings & Stats -->
    <div class="dashboard-bottom-row">
        <!-- Upcoming Bookings -->
        <div class="dashboard-card upcoming-bookings">
            <div class="card-header">
                <h3>Upcoming Bookings <span class="count">({{ $this->upcomingBookingsCount }})</span></h3>
            </div>
            <div class="card-content">
                @foreach (collect($this->upcomingBookings)->take(2) as $booking)
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
                                @php
                                    $petImages = !empty($booking['pet_images'])
                                        ? $booking['pet_images']
                                        : [$booking['pet_image']];
                                @endphp
                                <div class="pet-section">
                                    <div class="pet-avatar-stack">
                                        @foreach ($petImages as $petImage)
                                            <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
                                        @endforeach
                                    </div>
                                </div>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                                fill="none">
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
                                    @php
                                        $petImages = !empty($booking['pet_images'])
                                            ? $booking['pet_images']
                                            : [$booking['pet_image']];
                                    @endphp
                                    <div class="pet-avatar-stack">
                                        @foreach ($petImages as $petImage)
                                            <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
                                        @endforeach
                                    </div>

                                    <div class="pet-info">
                                        <div class="pet-count">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                                fill="none">
                                                <path
                                                    d="M7.5 5.63158C5.38867 5.63158 3.60467 7.52376 3.02867 9.95408C2.77533 11.0228 3.15733 12.1572 4.095 12.6902C4.83833 13.1127 5.944 13.5 7.5 13.5C9.056 13.5 10.162 13.1127 10.9053 12.6902C11.843 12.1572 12.2247 11.0228 11.9713 9.95408C11.3953 7.52342 9.61133 5.63158 7.5 5.63158ZM0.5 5.09926C0.5 6.04416 1.09667 7 1.83333 7C2.57 7 3.16667 6.04416 3.16667 5.09926C3.16667 4.15437 2.57 3.57895 1.83333 3.57895C1.09667 3.57895 0.5 4.15471 0.5 5.09926ZM14.5 5.09926C14.5 6.04416 13.9033 7 13.1667 7C12.43 7 11.8333 6.04416 11.8333 5.09926C11.8333 4.15437 12.43 3.57895 13.1667 3.57895C13.9033 3.57895 14.5 4.15471 14.5 5.09926ZM4 2.02032C4 2.96521 4.59667 3.92105 5.33333 3.92105C6.07 3.92105 6.66667 2.96521 6.66667 2.02032C6.66667 1.07542 6.07 0.5 5.33333 0.5C4.59667 0.5 4 1.07576 4 2.02032ZM11 2.02032C11 2.96521 10.4033 3.92105 9.66667 3.92105C8.93 3.92105 8.33333 2.96521 8.33333 2.02032C8.33333 1.07542 8.93 0.5 9.66667 0.5C10.4033 0.5 11 1.07576 11 2.02032Z"
                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
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
                                    <div class="info-label"> <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
                                            <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                            <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>Time</div>
                                    <div class="info-value">{{ $booking['time'] }}</div>
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
                            @endif
                            <div class="booking-actions">
                                <button class="btn-action btn-navigate">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11"
                                        fill="none">
                                        <path
                                            d="M16.1264 4.31767C16.5221 4.70945 16.5215 5.34892 16.1252 5.74001L12.0214 9.78966C11.6966 10.1102 11.1731 10.106 10.8535 9.78024C10.7269 9.6553 10.6762 9.53037 10.7016 9.40543C10.7332 9.28049 10.8029 9.1618 10.9105 9.04935L13.6357 6.3788C13.7876 6.22887 13.9301 6.09457 14.063 5.97587C14.1683 5.8819 14.0872 5.69393 13.9468 5.70816C13.7843 5.72462 13.6173 5.7389 13.4458 5.75099C13.0976 5.77597 12.7368 5.78847 12.3633 5.78847H0.763684C0.341912 5.78847 0 5.44655 0 5.02478C0 4.60301 0.341913 4.2611 0.763685 4.2611H12.3633C12.7368 4.2611 13.1008 4.27359 13.4553 4.29858C13.6242 4.31049 13.7888 4.32452 13.9491 4.34068C14.0915 4.35504 14.1719 4.16651 14.063 4.07369C13.9301 3.955 13.7876 3.82069 13.6357 3.67076L10.8915 0.981471C10.7775 0.869026 10.7079 0.750335 10.6826 0.625397C10.6573 0.500459 10.7047 0.375521 10.825 0.250583C11.1497 -0.0802029 11.6815 -0.0839592 12.0109 0.242206L16.1264 4.31767Z"
                                            fill="white" />
                                    </svg>
                                </button>
                                <button class="btn-action btn-message">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17"
                                        fill="none">
                                        <path
                                            d="M8.77344 0.75C13.2901 0.750138 16.7959 3.98439 16.7959 7.79297C16.7959 11.6016 13.2901 14.8358 8.77344 14.8359H8.77148C7.96957 14.8378 7.17035 14.732 6.39551 14.5215L6.11523 14.4453L5.85645 14.5781C5.3514 14.8374 4.20543 15.3396 2.29883 15.7285L2.30078 15.7188C2.58443 14.8788 2.82006 13.9064 2.90723 12.9844L2.93945 12.6377L2.69531 12.3887C1.47357 11.1454 0.75 9.53663 0.75 7.79297C0.750026 3.98431 4.25668 0.75 8.77344 0.75Z"
                                            stroke="white" stroke-width="1.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="#" wire:click.prevent="openCardModal('upcoming')" class="view-all-link">View All</a>
        </div>

        <!-- Stats Column -->
        <div class="stats-column">
            <div class="this-month-card">
                <div class="card-header">
                    <h3>This Month</h3>
                </div>
                <div class="this-month-rows">
                    <div class="this-month-row">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 48 48"
                                fill="none">
                                <circle cx="24" cy="24" r="24" fill="#D8E8B7" fill-opacity="0.5" />
                                <circle cx="23.9999" cy="24" r="18.3529" fill="#D8E8B7" fill-opacity="0.5" />
                                <path
                                    d="M16 24.003C16 20.8264 16 19.2376 17.0314 18.2512C18.0627 17.2648 19.7215 17.2639 23.04 17.2639H26.56C29.8785 17.2639 31.5382 17.2639 32.5686 18.2512C33.5991 19.2385 33.6 20.8264 33.6 24.003V25.6878C33.6 28.8645 33.6 30.4532 32.5686 31.4396C31.5373 32.4261 29.8785 32.4269 26.56 32.4269H23.04C19.7215 32.4269 18.0618 32.4269 17.0314 31.4396C16.0009 30.4524 16 28.8645 16 25.6878V24.003Z"
                                    fill="white" stroke="#3B3731" stroke-width="0.25" />
                                <path d="M20.2671 17.2576V16M29.3243 17.2576V16M16.1914 21.4494H33.4" stroke="#3B3731"
                                    stroke-width="0.25" stroke-linecap="round" />
                                <path
                                    d="M16.7046 22.1187C16.6687 22.7657 16.6687 23.5355 16.6687 24.4604V25.9232C16.6687 28.6814 16.6695 30.0601 17.6211 30.9174C18.5727 31.7746 20.1053 31.7746 23.1697 31.7746H26.4203C29.4847 31.7746 31.0165 31.7738 31.9689 30.9174C32.9213 30.0609 32.9213 28.6814 32.9213 25.9232V24.4604C32.9213 23.5355 32.9212 22.7657 32.8852 22.1187H16.7046Z"
                                    fill="#EFEFEF" />
                                <path
                                    d="M22.2142 27.0001L23.9608 28.7467L27.3345 25.3731C27.493 25.2145 27.4935 24.9577 27.3355 24.7986C27.1768 24.6387 26.9183 24.6382 26.759 24.7976L23.9608 27.5957L22.7876 26.4258C22.6291 26.2678 22.3725 26.2679 22.2142 26.4262C22.0557 26.5847 22.0557 26.8416 22.2142 27.0001Z"
                                    fill="#3B3731" />
                            </svg>
                        </div>
                        <div class="stat-copy">
                            <h4>Total Bookings</h4>
                            <div class="stat-main">
                                <span class="stat-value">{{ $this->stats['total_bookings']['value'] }}</span>
                                <span class="stat-label">/ {{ $this->stats['total_bookings']['label'] }}</span>
                            </div>
                        </div>
                        <div class="stat-change positive">
                            {{ $this->stats['total_bookings']['change'] }}
                        </div>
                    </div>

                    <div class="this-month-row">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 48 48"
                                fill="none">
                                <circle cx="24" cy="24" r="24" fill="#CBDCE8" fill-opacity="0.5" />
                                <circle cx="23.9999" cy="24" r="18.3529" fill="#CBDCE8" fill-opacity="0.5" />
                                <path
                                    d="M29.8462 17.6H18.1538C16.9643 17.6 16 18.5643 16 19.7538V27.7538C16 28.9434 16.9643 29.9077 18.1538 29.9077H29.8462C31.0357 29.9077 32 28.9434 32 27.7538V19.7538C32 18.5643 31.0357 17.6 29.8462 17.6Z"
                                    fill="white" stroke="#3B3731" stroke-width="0.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M16 21.2922H32" stroke="#3B3731" stroke-width="0.25" stroke-linejoin="round" />
                                <rect x="16.3333" y="21.6" width="15.3333" height="1.33333" fill="#EFEFEF" />
                                <rect x="18.564" y="26.318" width="3.84615" height="1.02564" rx="0.51282"
                                    fill="#D4D4D4" />
                            </svg>
                        </div>
                        <div class="stat-copy">
                            <h4>{{ $isSpaceAccount ? 'Space Profile Views' : 'Profile Views' }}</h4>
                            <div class="stat-main">
                                <span class="stat-value">{{ $this->stats['profile_views']['value'] }}</span>
                                <span class="stat-label">/ {{ $this->stats['profile_views']['label'] }}</span>
                            </div>
                        </div>
                        <div class="stat-change profile-views">
                            {{ $this->stats['profile_views']['sublabel'] }}
                        </div>
                    </div>

                    <div class="this-month-row">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 48 48"
                                fill="none">
                                <circle cx="24" cy="24" r="24" fill="#FFA899" fill-opacity="0.5" />
                                <circle cx="23.9999" cy="24" r="18.3529" fill="#FFA899" fill-opacity="0.5" />
                                <path
                                    d="M16 32.0378V31.0579C16 27.8145 18.6359 25.1786 21.8793 25.1786H25.7988C29.0422 25.1786 31.6781 27.8145 31.6781 31.0579V32.0378"
                                    fill="white" />
                                <path
                                    d="M16 32.0378V31.0579C16 27.8145 18.6359 25.1786 21.8793 25.1786H25.7988C29.0422 25.1786 31.6781 27.8145 31.6781 31.0579V32.0378"
                                    stroke="#3B3731" stroke-width="0.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M16.7349 32.1524C16.7349 28.8512 19.1442 25.9058 22.1089 25.9058H25.6915C28.6562 25.9058 31.0655 28.8512 31.0655 32.1524"
                                    fill="#EFEFEF" />
                                <path
                                    d="M23.8387 22.2391C21.6732 22.2391 19.9192 20.4851 19.9192 18.3196C19.9192 16.154 21.6732 14.4 23.8387 14.4C26.0043 14.4 27.7583 16.154 27.7583 18.3196C27.7583 20.4851 26.0043 22.2391 23.8387 22.2391Z"
                                    fill="white" stroke="#3B3731" stroke-width="0.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="stat-copy">
                            <h4>Repeat Clients</h4>
                            <div class="stat-main">
                                <span class="stat-value">{{ $this->stats['repeat_clients']['value'] }}</span>
                                <span class="stat-label">/ {{ $this->stats['repeat_clients']['label'] }}</span>
                            </div>
                        </div>
                        <div class="stat-change repeat-clients">
                            {{ $this->stats['repeat_clients']['sublabel'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                            @foreach (!empty($booking['pet_images']) ? $booking['pet_images'] : [$booking['pet_image']] as $petImage)
                                                <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
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
                                <div class="request-item {{ $loop->odd ? 'request-item--odd' : '' }}">
                                    <div>
                                        <div class="pet-avatar-wrap">
                                            <div class="pet-avatar-stack">
                                                @foreach (!empty($request['pet_images']) ? $request['pet_images'] : [$request['pet_image']] as $petImage)
                                                    <div class="pet-avatar-dot-wrap">
                                                        <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
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
                                        <button wire:click="acceptRequest('{{ $request['booking_id'] }}')" class="btn-accept">Accept
                                            Request</button>
                                        <button wire:click="viewDetails('{{ $request['booking_id'] }}')" class="btn-view">View
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
                                                    @foreach (!empty($booking['pet_images']) ? $booking['pet_images'] : [$booking['pet_image']] as $petImage)
                                                        <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
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
                                                    @foreach (!empty($booking['pet_images']) ? $booking['pet_images'] : [$booking['pet_image']] as $petImage)
                                                        <img src="{{ $petImage }}" alt="Pet" class="pet-avatar-large">
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
        (function () {
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