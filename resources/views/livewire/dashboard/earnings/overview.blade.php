<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Support\BookingReceiptViewData;
use App\Support\DashboardNav;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $payoutBank = '';

    public string $payoutAccountHolderName = '';

    public string $payoutAccountNumber = '';

    public string $payoutSortCode = '';

    public string $payoutIban = '';

    public string $payoutFrequency = 'Weekly';

    public function mount(): void
    {
        $this->refreshPayoutBankDetails();
        $this->refreshPayoutFrequency();
    }

    private function loggedInSpacerId(): ?int
    {
        return auth('groomer_spacer')->id();
    }

    private function bookingsQuery()
    {
        return Booking::query()->where(['goormer_spacer_id' => $this->loggedInSpacerId() ?? 0]);
    }

    private function completedBookingsQuery()
    {
        return $this->bookingsQuery()->where(['booking_status' => 'completed']);
    }

    public function isSpaceAccount(): bool
    {
        $userType = auth('groomer_spacer')->user()?->user_type ?? (auth()->user()?->user_type ?? '');

        return strtolower((string) $userType) === 'space';
    }

    private function normalizedBookingTime($time): string
    {
        if ($time === null || $time === '') {
            return '';
        }

        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }

        $value = trim(preg_replace('/\s+/', ' ', (string) $time));
        if ($value === '') {
            return '';
        }

        if (preg_match('/^(\d{1,2}:\d{2})(?::\d{2})?\s*-\s*(\d{1,2}:\d{2})(?::\d{2})?$/', $value, $range)) {
            return $range[1] . ' - ' . $range[2];
        }

        if (preg_match('/^(\d{1,2}:\d{2})(?::\d{2})?/', $value, $match)) {
            return $match[1];
        }

        return $value;
    }

    private function spaceDurationCategory(?string $service, $time): string
    {
        $serviceLower = strtolower(trim((string) $service));

        if (preg_match('/full[\s_-]*day|fullday/', $serviceLower)) {
            return 'Full-Day';
        }

        if (preg_match('/half[\s_-]*day/', $serviceLower)) {
            return 'Half-Day';
        }

        if (str_contains($serviceLower, 'hour')) {
            return 'Hourly';
        }

        $timeRange = $this->normalizedBookingTime($time);

        if ($timeRange !== '' && preg_match('/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/', $timeRange, $range)) {
            $startMinutes = ((int) $range[1]) * 60 + (int) $range[2];
            $endMinutes = ((int) $range[3]) * 60 + (int) $range[4];
            $diff = $endMinutes - $startMinutes;

            if ($diff < 0) {
                $diff += 24 * 60;
            }

            if ($diff >= 7 * 60) {
                return 'Full-Day';
            }

            if ($diff >= 3 * 60) {
                return 'Half-Day';
            }

            return 'Hourly';
        }

        return $timeRange !== '' ? 'Hourly' : 'Hourly';
    }

    private function clientInitialsFromName(?string $name): string
    {
        if (!filled($name)) {
            return '??';
        }

        return Str::upper(Str::substr(Str::of($name)->explode(' ')->map(fn(string $part) => Str::substr($part, 0, 1))->join(''), 0, 2));
    }

    private function usersHaveProfileImage(): bool
    {
        return Schema::hasColumn('users', 'profile_image');
    }

    private function resolveProfileImageUrl(?string $profileImage): ?string
    {
        $value = trim((string) $profileImage);

        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//') || str_starts_with($value, 'data:')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }

    private function visitTypeLabel(?string $visitType): string
    {
        $raw = trim((string) $visitType);

        if ($raw === '') {
            return 'Garden / Shed';
        }

        if (str_contains($raw, '/') || str_contains($raw, ' ')) {
            return $raw;
        }

        $normalized = str_replace('_', ' ', strtolower($raw));

        return match ($normalized) {
            'garden shed', 'garden/shed' => 'Garden / Shed',
            'home visit', 'home' => 'Home Visit',
            'salon', 'salon visit' => 'Salon',
            'mobile station', 'mobile_station' => 'Mobile Station',
            default => ucwords($normalized),
        };
    }

    private function transactionStatusPill(?string $status): array
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'failed' => ['label' => 'Failed', 'key' => 'failed'],
            'refunded' => ['label' => 'Refunded', 'key' => 'refunded'],
            default => ['label' => 'Paid', 'key' => 'paid'],
        };
    }

    private function payoutEligiblePaymentsQuery()
    {
        return Payment::query()
            ->whereHas('booking', function ($query) {
                $query->where(['goormer_spacer_id' => $this->loggedInSpacerId() ?? 0])->where(['booking_status' => 'completed']);
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhereNotIn('status', ['failed', 'refunded']);
            });
    }

    private function payoutDetails(): array
    {
        $details = auth('groomer_spacer')->user()?->payout_details ?? [];

        if (!is_array($details)) {
            $details = is_string($details) ? (json_decode($details, true) ?: []) : [];
        }

        return $details;
    }

    public function refreshPayoutBankDetails(): void
    {
        $details = $this->payoutDetails();

        $this->payoutBank = (string) ($details['bank'] ?? '');
        $this->payoutAccountHolderName = (string) ($details['account_holder_name'] ?? '');
        $this->payoutAccountNumber = (string) ($details['account_number'] ?? '');
        $this->payoutSortCode = (string) ($details['sort_code'] ?? '');
        $this->payoutIban = (string) ($details['iban'] ?? '');
    }

    public function refreshPayoutFrequency(): void
    {
        $details = $this->payoutDetails();
        $frequency = (string) ($details['payout_frequency'] ?? 'Weekly');

        $this->payoutFrequency = in_array($frequency, ['Weekly', 'Fortnightly', 'Monthly'], true) ? $frequency : 'Weekly';
    }

    public function updatePayoutBankDetails(): void
    {
        $validated = $this->validate([
            'payoutBank' => ['required', 'string', 'max:100'],
            'payoutAccountHolderName' => ['required', 'string', 'max:255'],
            'payoutAccountNumber' => ['required', 'string', 'max:50'],
            'payoutSortCode' => ['required', 'string', 'max:20'],
            'payoutIban' => ['required', 'string', 'max:50'],
        ]);

        $user = auth('groomer_spacer')->user();

        if (!$user) {
            return;
        }

        $details = $this->payoutDetails();
        $details = array_merge($details, [
            'bank' => $validated['payoutBank'],
            'account_holder_name' => $validated['payoutAccountHolderName'],
            'account_number' => $validated['payoutAccountNumber'],
            'sort_code' => $validated['payoutSortCode'],
            'iban' => $validated['payoutIban'],
        ]);

        $user->update(['payout_details' => $details]);

        $this->dispatch('payout-bank-details-saved');
    }

    public function updatePayoutFrequency(): void
    {
        $validated = $this->validate([
            'payoutFrequency' => ['required', 'string', 'in:Weekly,Fortnightly,Monthly'],
        ]);

        $user = auth('groomer_spacer')->user();

        if (!$user) {
            return;
        }

        $details = $this->payoutDetails();
        $details['payout_frequency'] = $validated['payoutFrequency'];

        $user->update(['payout_details' => $details]);

        $this->dispatch('payout-frequency-saved');
    }

    private function nextPayoutDateForFrequency(string $frequency): string
    {
        $nextTuesday = now()->next('Tuesday');

        return match ($frequency) {
            'Fortnightly' => $nextTuesday->addWeek()->format('d F Y'),
            'Monthly' => now()->addMonthNoOverflow()->next('Tuesday')->format('d F Y'),
            default => $nextTuesday->format('d F Y'),
        };
    }

    private function maskedAccountNumber(?string $accountNumber): string
    {
        $digits = preg_replace('/\D+/', '', (string) $accountNumber);

        if ($digits === '') {
            return 'Not added';
        }

        return '**** **** **** ' . substr($digits, -4);
    }

    private function recentBookingAvatar(Booking $booking, bool $preferOwnerPhoto = false): array
    {
        $pet = $booking->pets->first();
        $ownerPhoto = null;

        if ($this->usersHaveProfileImage()) {
            $profileImage = $booking->petOwner?->profile_image ?? null;
            $ownerPhoto = $this->resolveProfileImageUrl($profileImage);
        }

        $petPhoto = filled($pet?->photo) ? (string) $pet->photo : null;
        $photo = $preferOwnerPhoto ? $ownerPhoto : $petPhoto ?? $ownerPhoto;

        return [
            'photo' => $photo,
            'initials' => $this->clientInitialsFromName($booking->petOwner?->name ?? $pet?->name),
            'alt' => $booking->petOwner?->name ?? ($pet?->name ?? 'Client'),
        ];
    }

    private function revenueCategory(string $service, ?array $extraAddOns = null): string
    {
        $serviceLower = strtolower(trim($service));

        if (str_contains($serviceLower, 'addon') || str_contains($serviceLower, 'add-on') || str_contains($serviceLower, 'nail') || str_contains($serviceLower, 'teeth') || str_contains($serviceLower, 'trim')) {
            return 'Add-ons';
        }

        if (str_contains($serviceLower, 'bath') || str_contains($serviceLower, 'brush') || str_contains($serviceLower, 'wash') || str_contains($serviceLower, 'tidy')) {
            return 'Bath & Tidy';
        }

        if (is_array($extraAddOns) && count($extraAddOns) > 0) {
            return 'Add-ons';
        }

        return 'Full Groom';
    }

    private function addOnRevenue(?array $extraAddOns): float
    {
        if (!is_array($extraAddOns)) {
            return 0.0;
        }

        return collect($extraAddOns)->sum(fn($addon) => (float) ($addon['amount'] ?? 0));
    }

    private function twelveWeekPeriodBounds(): array
    {
        $currentStart = now()->subWeeks(12)->startOfDay();
        $previousStart = now()->subWeeks(24)->startOfDay();
        $previousEnd = $currentStart->copy()->subDay()->endOfDay();

        return [
            'current_start' => $currentStart,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    private function sumCompletedRevenueFrom($start, ?\Carbon\Carbon $end = null): float
    {
        $query = $this->completedBookingsQuery()->whereDate('date', '>=', $start);

        if ($end !== null) {
            $query->whereDate('date', '<=', $end);
        }

        return (float) $query->sum('amount');
    }

    /**
     * @param  iterable<int, Booking>  $bookings
     * @return array<int, array{label: string, amount: float, percent: int}>
     */
    private function buildSpaceRevenueSegments(iterable $bookings): array
    {
        $totals = [
            'Hourly' => 0.0,
            'Half-Day' => 0.0,
            'Full-Day' => 0.0,
        ];

        foreach ($bookings as $booking) {
            $category = $this->spaceDurationCategory((string) $booking->service, $booking->time);
            $totals[$category] += (float) $booking->amount;
        }

        $grandTotal = array_sum($totals);

        return collect($totals)
            ->map(function (float $amount, string $label) use ($grandTotal) {
                $percent = $grandTotal > 0 ? (int) round(($amount / $grandTotal) * 100) : 0;

                return [
                    'label' => $label,
                    'amount' => $amount,
                    'percent' => $percent,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  iterable<int, Booking>  $bookings
     * @return array<int, array{label: string, amount: float, percent: int}>
     */
    private function buildRevenueSegments(iterable $bookings): array
    {
        $totals = [
            'Full Groom' => 0.0,
            'Bath & Tidy' => 0.0,
            'Add-ons' => 0.0,
        ];

        foreach ($bookings as $booking) {
            $addOnAmount = $this->addOnRevenue($booking->extra_add_ons);
            $baseAmount = max((float) $booking->amount - $addOnAmount, 0);
            $category = $this->revenueCategory((string) $booking->service, $booking->extra_add_ons);

            $totals[$category] += $baseAmount;

            if ($addOnAmount > 0) {
                $totals['Add-ons'] += $addOnAmount;
            }
        }

        $grandTotal = array_sum($totals);

        return collect($totals)
            ->map(function (float $amount, string $label) use ($grandTotal) {
                $percent = $grandTotal > 0 ? (int) round(($amount / $grandTotal) * 100) : 0;

                return [
                    'label' => $label,
                    'amount' => $amount,
                    'percent' => $percent,
                ];
            })
            ->values()
            ->all();
    }

    private function calculateTwelveWeekGrowth(float $recentTotal, float $previousTotal): int
    {
        if ($previousTotal <= 0) {
            return $recentTotal > 0 ? 100 : 0;
        }

        return (int) round((($recentTotal - $previousTotal) / $previousTotal) * 100);
    }

    #[Computed]
    public function summary(): array
    {
        $stats = $this->completedBookingsQuery()->selectRaw('COUNT(*) as booking_count, COALESCE(SUM(amount), 0) as total_revenue')->first();

        $count = (int) ($stats->booking_count ?? 0);
        $total = (float) ($stats->total_revenue ?? 0);
        $average = $count > 0 ? round($total / $count, 2) : 0.0;

        return [
            'total_earnings' => $total,
            'average_revenue' => $average,
            'booking_count' => $count,
        ];
    }

    #[Computed]
    public function revenueBreakdown(): array
    {
        $periods = $this->twelveWeekPeriodBounds();
        $isSpaceAccount = $this->isSpaceAccount();
        $bookingColumns = $isSpaceAccount ? ['service', 'amount', 'time'] : ['service', 'amount', 'extra_add_ons'];

        $recentBookings = $this->completedBookingsQuery()->whereDate('date', '>=', $periods['current_start'])->get($bookingColumns);

        $segments = $isSpaceAccount ? $this->buildSpaceRevenueSegments($recentBookings) : $this->buildRevenueSegments($recentBookings);

        $recentTotal = $this->sumCompletedRevenueFrom($periods['current_start']);
        $previousTotal = $this->sumCompletedRevenueFrom($periods['previous_start'], $periods['previous_end']);

        $growth = $this->calculateTwelveWeekGrowth($recentTotal, $previousTotal);

        return [
            'segments' => $segments,
            'growth' => $growth,
            'growth_positive' => $growth >= 0,
        ];
    }

    #[Computed]
    public function chartBookings(): array
    {
        $start = now()->subMonths(35)->startOfMonth();
        $end = now()->endOfMonth();

        return $this->completedBookingsQuery()
            ->whereBetween('date', [$start, $end])
            ->get(['date', 'amount'])
            ->map(
                fn(Booking $booking) => [
                    'date' => $booking->date?->format('Y-m-d'),
                    'amount' => (float) $booking->amount,
                ],
            )
            ->filter(fn(array $booking) => filled($booking['date']))
            ->values()
            ->all();
    }

    #[Computed]
    public function recentBookings(): array
    {
        $isSpaceAccount = $this->isSpaceAccount();
        $ownerColumns = ['id', 'name'];

        if ($this->usersHaveProfileImage()) {
            $ownerColumns[] = 'profile_image';
        }

        return $this->completedBookingsQuery()
            ->with(['pets', 'petOwner:' . implode(',', $ownerColumns)])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(4)
            ->get()
            ->map(function (Booking $booking) use ($isSpaceAccount) {
                $completedAt = $booking->date?->copy()->endOfDay() ?? now();
                $avatar = $this->recentBookingAvatar($booking, $isSpaceAccount);

                if ($isSpaceAccount) {
                    return [
                        'visit_type_label' => $this->visitTypeLabel($booking->visit_type),
                        'client_photo' => $avatar['photo'],
                        'client_initials' => $avatar['initials'],
                        'client_name' => $avatar['alt'],
                        'relative_time' => $completedAt->diffForHumans(),
                        'amount' => (float) $booking->amount,
                    ];
                }

                return [
                    'pet_name' => $booking->pets->first()?->name ?? 'Pet',
                    'pet_photo' => $avatar['photo'] ?? 'https://i.pravatar.cc/150?img=12',
                    'relative_time' => $completedAt->diffForHumans(),
                    'amount' => (float) $booking->amount,
                ];
            })
            ->all();
    }

    #[Computed]
    public function transactions(): array
    {
        $isSpaceAccount = $this->isSpaceAccount();

        return Payment::query()
            ->whereHas('booking', fn($query) => $query->where(['goormer_spacer_id' => $this->loggedInSpacerId() ?? 0]))
            ->with(['booking:id,goormer_spacer_id,time,service,visit_type,amount,discount,extra_add_ons,date,pet_owner_id', 'petOwner:id,name', 'pet:id,name,pet_type'])
            ->orderByDesc('date')
            ->limit(8)
            ->get(['id', 'booking_id', 'pet_owner_id', 'pet_detail_id', 'date', 'amount', 'status', 'payment_method', 'service_type'])
            ->values()
            ->map(function (Payment $payment) use ($isSpaceAccount) {
                $bookingTime = trim((string) ($payment->booking?->time ?? ''));
                $time = $this->normalizedBookingTime($bookingTime);
                $status = $this->transactionStatusPill($payment->status);
                $method = filled($payment->payment_method) ? $payment->payment_method : 'N/A';

                $row = [
                    'date' => $payment->date?->format('d/m/y') ?? '--/--/--',
                    'time' => $time !== '' ? $time : '--:--',
                    'amount' => (float) $payment->amount,
                    'payment_method' => $method,
                    'status_label' => $status['label'],
                    'status_key' => $status['key'],
                    'booking_id' => (int) $payment->booking_id,
                    'booking_reference' => 'FG-' . str_pad((string) $payment->booking_id, 5, '0', STR_PAD_LEFT),
                ];

                if ($isSpaceAccount) {
                    $row['service_type'] = filled($payment->service_type) ? $payment->service_type : $this->spaceDurationCategory($payment->booking?->service, $payment->booking?->time);
                    $row['space'] = $this->visitTypeLabel($payment->booking?->visit_type);
                } else {
                    $row['client'] = trim((string) ($payment->petOwner?->name ?? 'Unknown Client'));
                    $row['pet'] = trim((string) ($payment->pet?->name ?? 'Unknown Pet'));
                    $row['pet_type'] = trim((string) ($payment->pet?->pet_type ?? 'Pet'));
                }

                if ($payment->booking) {
                    $row['receipt'] = BookingReceiptViewData::fromBooking($payment->booking, $isSpaceAccount, trim((string) ($payment->petOwner?->name ?? '')) ?: null, trim((string) ($payment->pet?->name ?? '')) ?: null, trim((string) ($payment->pet?->pet_type ?? '')) ?: null);
                }

                return $row;
            })
            ->all();
    }

    #[Computed]
    public function payouts(): array
    {
        $pendingStart = now()->subDays(7)->startOfDay();
        $payoutDetails = $this->payoutDetails();

        $pendingAmount = (clone $this->payoutEligiblePaymentsQuery())->whereDate('date', '>=', $pendingStart)->sum('amount');

        $totalPayouts = (clone $this->payoutEligiblePaymentsQuery())->sum('amount');

        $futureBookings = $this->bookingsQuery()
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', now()->startOfDay())
            ->orderBy('date')
            ->get(['id', 'date', 'amount'])
            ->map(
                fn(Booking $booking) => [
                    'date' => $booking->date?->format('d/m/Y') ?? '--/--/----',
                    'amount' => (float) $booking->amount,
                    'arrival_date' => $booking->date?->copy()->addWeekdays(2)->format('d/m/Y') ?? '--/--/----',
                ],
            )
            ->all();

        $futureAmount = collect($futureBookings)->sum(fn(array $booking) => (float) $booking['amount']);
        $futurePreviewBookings = array_slice($futureBookings, 0, 2);

        $history = $this->payoutEligiblePaymentsQuery()
            ->with('booking:id,date')
            ->orderByDesc('date')
            ->limit(7)
            ->get(['id', 'booking_id', 'date', 'amount'])
            ->map(
                fn(Payment $payment) => [
                    'date' => $payment->date?->format('d/m/y') ?? '--/--/--',
                    'amount' => (float) $payment->amount,
                    'status' => 'Completed',
                    'reference' => 'FG-' . str_pad((string) $payment->booking_id, 5, '0', STR_PAD_LEFT),
                    'invoice_url' => $payment->booking ? route('dashboard.bookings.invoice-pdf', $payment->booking) : null,
                ],
            )
            ->all();

        $frequency = (string) ($payoutDetails['payout_frequency'] ?? 'Weekly');
        if (!in_array($frequency, ['Weekly', 'Fortnightly', 'Monthly'], true)) {
            $frequency = 'Weekly';
        }

        return [
            'pending_amount' => (float) $pendingAmount,
            'total_amount' => (float) $totalPayouts,
            'future_amount' => (float) $futureAmount,
            'future_items' => $futurePreviewBookings,
            'future_items_all' => $futureBookings,
            'history' => $history,
            'frequency' => $frequency,
            'next_payout_date' => $this->nextPayoutDateForFrequency($frequency),
            'bank' => [
                'verified' => filled($payoutDetails['bank'] ?? null) && filled($payoutDetails['account_holder_name'] ?? null) && filled($payoutDetails['account_number'] ?? null),
                'name' => filled($payoutDetails['bank'] ?? null) ? $payoutDetails['bank'] : 'Bank details',
                'account_number' => $this->maskedAccountNumber($payoutDetails['account_number'] ?? null),
                'account_holder' => $payoutDetails['account_holder_name'] ?? 'Not added',
            ],
        ];
    }
}; ?>

@php
    $primaryColor = '#F4A47C';
    $lightColor = '#FDEBD0';
    $donutColors = ['#E88B5C', '#F4A47C', '#FDE8D4'];
    $isSpaceAccount = $this->isSpaceAccount();
    $breakdown = $this->revenueBreakdown;
    $summary = $this->summary;
    $chartBookings = $this->chartBookings;
    $transactions = $this->transactions;
    $payouts = $this->payouts;
    $now = now();
    $dashboardEarningsMenu = DashboardNav::fromSession()['active_earnings_menu'];
@endphp

<div class="earnings-overview" x-data="{ activeEarningsMenu: @js($dashboardEarningsMenu) }"
    x-on:earnings-menu-selected.window="activeEarningsMenu = $event.detail?.menu || 'overview'" x-init="$nextTick(() => window.mountEarningsCharts?.($el))">
    <style>
        .earnings-overview {
            --earnings-primary: {{ $primaryColor }};
            --earnings-light: rgba(255, 216, 140, 0.20);
            ;
            --earnings-text: #333333;
            --earnings-muted: #777777;
            --earnings-border: #FFC97A;
            --earnings-panel: #F4F7F9;
            --earnings-growth: #82C91E;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 0.5rem 0 2rem;
            width: 100%;
        }

        .earnings-layout {
            display: flex;
            justify-content: space-between;
            gap: 4rem;
            align-items: start;
            width: 100%;
            margin-top: 4rem;
        }

        .earnings-layout-left,
        .earnings-layout-right {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-width: 0;
        }

        .earnings-layout-left {
            gap: 3rem !important;
        }

        .earnings-summary-cards {
            display: flex;
            align-items: center;
            /* justify-content: space-between; */
            gap: 1rem;
        }

        .earnings-stat-card {
            display: flex;
            flex-direction: column;
            align-items: start;
            justify-content: center;
            border: 1px solid var(--earnings-border);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            background: #fff;
            width: 100%;
            height: 160px;
        }

        .earnings-stat-card div {
            margin: 0 auto;
        }

        .earnings-stat-card--accent {
            background: var(--earnings-light);
        }

        .earnings-stat-label {
            margin: 0 0 0.5rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-stat-value {
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .earnings-stat-suffix {
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-left: 15px;
        }

        .earnings-breakdown-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .earnings-breakdown-body {
            display: flex;
            align-items: center;
            gap: 3rem;
            margin-top: 0.25rem;
            height: 160px;
        }

        .earnings-breakdown-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: start;
            gap: 0.75rem;
        }

        .earnings-breakdown-header h3 {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-breakdown-growth {
            margin: 0.35rem 0 0;
            color: #AFCD6F;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .earnings-breakdown-growth.is-negative {
            color: #D94848;
        }

        .earnings-donut-wrap {
            position: relative;
            width: 108px;
            height: 108px;
            flex-shrink: 0;
            align-self: center;
        }

        .earnings-breakdown-legend {
            display: flex;
            align-items: start;
            justify-content: start;
            gap: 1.5rem;
            margin-top: 0;
        }

        .earnings-legend-item {
            display: flex;
            flex-direction: column;
            align-items: start;
            gap: 0.5rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-legend-item strong {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .earnings-chart-card,
        .earnings-recent-card {
            border-radius: 10px;
        }

        .earnings-chart-card {
            padding: 1.25rem 1.5rem 1rem;
        }

        .earnings-chart-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .earnings-period-nav {
            display: flex;
            align-items: center;
            justify-content: start;
            gap: 0.75rem;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-period-nav button {
            border: 0;
            background: transparent;
            color: #9D9B98;
            cursor: pointer;
            padding: 0.15rem;
            line-height: 1;
            font-size: 18px;
            transition: transform 0.22s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.22s ease;
        }

        .earnings-period-nav button:hover {
            color: var(--earnings-text);
            transform: scale(1.06);
        }

        .earnings-period-nav button:active {
            transform: scale(0.94);
            opacity: 0.85;
        }

        .earnings-period-nav__label {
            margin-bottom: 12px;
            display: inline-block;
            min-width: 9rem;
            animation: earningsLabelFade 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .earnings-period-nav:focus {
            outline: none;
        }

        @keyframes earningsLabelFade {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .earnings-period-toggle {
            display: inline-flex;
            align-items: center;
            background: #F8F8F8;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            padding: 0;
            overflow: hidden;
        }

        .earnings-period-toggle button {
            border: 0;
            border-right: 1px solid #D4D4D4;
            background: transparent;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            padding: 0.75rem 1.75rem;
            min-width: 6rem;
            cursor: pointer;
            transition:
                background-color 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                color 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.18s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.28s ease;
        }

        .earnings-period-toggle button:focus {
            outline: none;
        }

        .earnings-period-toggle button:active {
            transform: scale(0.96);
        }

        .earnings-period-toggle button:last-child {
            border-right: 0;
        }

        .earnings-period-toggle button.is-active {
            background: #F1AA7D;
            color: #fff;
            border-right-color: transparent;
            position: relative;
            z-index: 1;
            font-weight: 600;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        .earnings-chart-heading {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            min-height: 2rem;
        }

        .earnings-chart-heading__title,
        .earnings-chart-heading__value {
            animation: earningsHeadingIn 0.48s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .earnings-chart-heading__value {
            animation-delay: 0.1s;
        }

        @keyframes earningsHeadingIn {
            0% {
                opacity: 0;
                transform: translateY(12px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .earnings-chart-heading h3 {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .earnings-chart-total {
            display: flex;
            justify-content: center;
            align-items: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .earnings-chart-total span:nth-child(2) {
            color: #9D9B98;
            font-weight: 400 !important;
            font-size: 14px !important;
            margin-left: 15px;
        }

        .earnings-chart-wrap {
            position: relative;
            height: 22.5rem;
        }

        .earnings-bar-chart {
            display: flex;
            align-items: stretch;
            gap: 0.75rem;
            height: 100%;
        }

        .earnings-bar-chart__y-axis {
            display: flex;
            flex-direction: column-reverse;
            justify-content: space-between;
            flex-shrink: 0;
            height: 100%;
            padding-bottom: 2.35rem;
        }

        .earnings-bar-chart__tick {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-bar-chart__plot {
            flex: 1;
            min-width: 0;
            height: 100%;
        }

        .earnings-bar-chart__bars {
            display: flex;
            align-items: stretch;
            justify-content: space-around;
            gap: 0.5rem;
            height: 100%;
        }

        .earnings-bar-chart__bar-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            min-width: 0;
            max-width: 50px;
            height: 100%;
            animation: earningsBarColFade 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .earnings-bar-chart__bar-col.is-clickable {
            cursor: pointer;
        }

        .earnings-bar-chart__bar-col.is-clickable:hover .earnings-bar-chart__bar {
            filter: brightness(0.96);
        }

        .earnings-bar-chart__bar-col.is-clickable:hover .earnings-bar-chart__label {
            color: #3B3731;
        }

        @keyframes earningsBarColFade {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .earnings-bar-chart__bar-track {
            flex: 1;
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-height: 0;
        }

        .earnings-bar-chart__bar {
            width: 100%;
            max-width: 50px;
            border-radius: 10px;
            min-height: 0;
            transform-origin: bottom center;
            transition:
                height 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                background-color 0.35s ease,
                opacity 0.35s ease;
            animation: earningsBarGrow 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes earningsBarGrow {
            from {
                transform: scaleY(0);
                opacity: 0.45;
            }

            to {
                transform: scaleY(1);
                opacity: 1;
            }
        }

        .earnings-bar-chart__label {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-align: center;
            white-space: nowrap;
            transition: color 0.3s ease, font-weight 0.3s ease, transform 0.3s ease;
        }

        .earnings-bar-chart__label.is-active {
            color: #3B3731;
            font-weight: 700;
            transform: translateY(-1px);
        }

        @media (prefers-reduced-motion: reduce) {

            .earnings-period-nav button,
            .earnings-period-toggle button,
            .earnings-bar-chart__bar,
            .earnings-bar-chart__bar-col,
            .earnings-bar-chart__label,
            .earnings-period-nav__label,
            .earnings-chart-heading__title,
            .earnings-chart-heading__value,
            .earnings-chart-total {
                animation: none !important;
                transition: none !important;
            }
        }

        .earnings-recent-card {
            background: var(--earnings-panel);
            border-color: transparent;
            padding: 4rem;
        }

        .earnings-recent-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .earnings-recent-header h3 {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .earnings-recent-header a {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-recent-header a:hover {
            text-decoration: underline;
        }

        .earnings-recent-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .earnings-recent-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            background: #fff;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .earnings-recent-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .earnings-recent-avatar-wrap {
            width: 50px;
            height: 50px;
            box-sizing: border-box;
            padding: 2px;
            border-radius: 999px;
            border: 1px solid #FFC97A;
            background: #fff;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .earnings-recent-avatar-wrap .earnings-recent-avatar {
            width: 100%;
            height: 100%;
        }

        .earnings-recent-avatar-initials {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            background: #FFF1DE;
            color: #FFC97A;
            text-align: center;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .earnings-recent-avatar-initials.is-hidden {
            display: none;
        }

        .earnings-recent-name.is-space {
            font-weight: 700;
        }

        .earnings-recent-meta {
            flex: 1;
            min-width: 0;
        }

        .earnings-recent-name {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 0;
        }

        .earnings-recent-time {
            margin: 0.15rem 0 0;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-recent-amount {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-empty {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            text-align: center;
            padding: 1.5rem 1rem;
        }

        .earnings-transactions-card {
            margin-top: 1.6rem;
            padding-top: 0.6rem;
        }

        .earnings-transactions-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .earnings-transactions-table {
            width: 100%;
            min-width: 980px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .earnings-transactions-table th,
        .earnings-transactions-table td {
            padding: 1.2rem 0.95rem;
            text-align: left;
            border-bottom: 1px solid #E3E3E3;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            vertical-align: middle;
        }

        .earnings-transactions-table th {
            font-weight: 600;
            color: #111;
            white-space: nowrap;
        }

        .earnings-transactions-date,
        .earnings-transactions-time {
            display: block;
            line-height: 1.3;
        }

        .earnings-transactions-client {
            font-weight: 600;
        }

        .earnings-transactions-pet-cell {
            display: flex;
            gap: 4px;
            align-items: baseline;
        }

        .earnings-transactions-pet {
            font-weight: 600;
        }

        .earnings-transactions-pet-type {
            color: #9D9B98;
        }

        .earnings-transactions-status {
            min-width: 96px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.45rem 1rem;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .earnings-transactions-status.is-paid {
            background: rgba(186, 207, 142, 0.10);
            color: #AFCD6F;
        }

        .earnings-transactions-status.is-failed {
            background: #FFE2E2;
            color: #FF6E6E;
        }

        .earnings-transactions-status.is-refunded {
            background: #FFF4E4;
            color: #FFAE37;
        }

        .earnings-transactions-table__receipt {
            text-align: center !important;
            border-left: 1px solid #DCDCDC;
        }

        .earnings-transactions-receipt-btn {
            border: 0;
            background: transparent;
            cursor: pointer;
            padding: 0.2rem;
            line-height: 0;
        }

        .completed-booking-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.22);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 100100;
            opacity: 0;
            pointer-events: none;
            transition: opacity 180ms ease;
        }

        .completed-booking-modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .completed-booking-modal-card {
            width: min(680px, 100%);
            border-radius: 10px;
            border: 1px solid #CBDCE8;
            background: #F8F8F8;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            opacity: 0;
            transform: translateY(12px) scale(0.98);
            transition:
                opacity 180ms ease,
                transform 180ms ease;
        }

        .completed-booking-modal-overlay.is-open .completed-booking-modal-card {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        @media (prefers-reduced-motion: reduce) {

            .completed-booking-modal-overlay,
            .completed-booking-modal-card {
                transition: none;
            }
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
            font-family: Lato, sans-serif;
            font-size: 18px;
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
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .completed-booking-modal-pet {
            margin: 0;
            color: #3B3731;
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
            height: 1px;
            background: #DCDCDC;
        }

        .completed-booking-modal-section-label {
            margin: 0 0 1rem;
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

        .completed-booking-modal-section-label-inner {
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
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .completed-booking-modal-space-name {
            margin: 0;
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
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
            margin-bottom: 1.7rem;
        }

        .completed-booking-modal-total-row>span:last-child {
            color: #3B3731;
            text-align: right;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
        }

        .completed-booking-modal-total-row.is-grand {
            border-top: 1px solid #DCDCDC;
            padding-top: 1rem;
            margin-top: 0.8rem;
            margin-bottom: 0;
        }

        .completed-booking-modal-total-row.is-grand>span {
            color: #3B3731 !important;
            font-family: Lato !important;
            font-style: normal !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            line-height: normal !important;
        }

        .completed-booking-addon-line {
            margin-bottom: 0.35rem;
        }

        .completed-booking-addon-line .completed-booking-modal-line-sub,
        .completed-booking-addon-line>span {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 23px;
        }

        .completed-booking-addon-line:last-child {
            margin-bottom: 0;
        }

        .earnings-transactions-empty {
            text-align: center !important;
            color: #9D9B98 !important;
            font-size: 15px !important;
        }
    </style>

    <div x-show="activeEarningsMenu === 'transactions'" x-cloak>
        <x-dashboard.earnings.transactions :transactions="$transactions" :is-space-user="$isSpaceAccount" />
    </div>

    <div x-show="activeEarningsMenu === 'pay-outs'" x-cloak>
        <x-dashboard.earnings.payouts :payouts="$payouts" />
    </div>

    <div class="earnings-layout" x-show="activeEarningsMenu !== 'transactions' && activeEarningsMenu !== 'pay-outs'"
        x-cloak>
        <div class="earnings-layout-left" style="width: 60%;">
            <div class="earnings-summary-cards">
                <div class="earnings-stat-card">
                    <div>
                        <p class="earnings-stat-label">Total Earnings</p>
                        <div>
                            <span class="earnings-stat-value">£{{ number_format($summary['total_earnings'], 2) }}</span>
                            <span class="earnings-stat-suffix"> / All Time</span>
                        </div>
                    </div>
                </div>
                <div class="earnings-stat-card earnings-stat-card--accent">
                    <div>
                        <p class="earnings-stat-label">Average Revenue</p>
                        <div>
                            <span
                                class="earnings-stat-value">£{{ number_format($summary['average_revenue'], 2) }}</span>
                            <span class="earnings-stat-suffix"> / Per Booking</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="earnings-chart-card" wire:ignore x-data="earningsChartPanel(@js([
    'bookings' => $chartBookings,
    'month' => (int) $now->month,
    'year' => (int) $now->year,
    'period' => 'month',
    'primary' => $primaryColor,
    'light' => $lightColor,
]))">
                <div class="earnings-chart-toolbar">
                    <div class="earnings-period-nav">
                        <button type="button" @click="previousPeriod()" aria-label="Previous month"><svg
                                xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34"
                                fill="none">
                                <g filter="url(#filter0_d_48_564)">
                                    <circle cx="17" cy="13" r="13" fill="white" />
                                    <circle cx="17" cy="13" r="12.5" stroke="#F5F5F5" />
                                </g>
                                <path d="M18.625 17.0625L14.5347 12.9722L18.5563 8.9505" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <defs>
                                    <filter id="filter0_d_48_564" x="0" y="0" width="34" height="34"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="4" />
                                        <feGaussianBlur stdDeviation="2" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_48_564" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_48_564"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg></button>
                        <span class="earnings-period-nav__label" :key="periodLabel" x-text="periodLabel"></span>
                        <button type="button" @click="nextPeriod()" aria-label="Next month"><svg
                                xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34"
                                fill="none">
                                <g filter="url(#filter0_d_48_567)">
                                    <circle cx="13" cy="13" r="13" transform="matrix(-1 0 0 1 30 0)"
                                        fill="white" />
                                    <circle cx="13" cy="13" r="12.5" transform="matrix(-1 0 0 1 30 0)"
                                        stroke="#F5F5F5" />
                                </g>
                                <path d="M15.375 17.0625L19.4653 12.9722L15.4437 8.9505" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <defs>
                                    <filter id="filter0_d_48_567" x="0" y="0" width="34" height="34"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="4" />
                                        <feGaussianBlur stdDeviation="2" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_48_567" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_48_567"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg></button>
                    </div>

                    <div class="earnings-period-toggle" role="group" aria-label="Earnings period">
                        <button type="button" @click="setPeriod('day')"
                            :class="{ 'is-active': period === 'day' }">Day</button>
                        <button type="button" @click="setPeriod('week')"
                            :class="{ 'is-active': period === 'week' }">Week</button>
                        <button type="button" @click="setPeriod('month')"
                            :class="{ 'is-active': period === 'month' }">Month</button>
                    </div>
                </div>

                <div class="earnings-chart-heading">
                    <h3 class="earnings-chart-heading__title" :key="'heading-title-' + chartAnimationKey"
                        x-text="chartTitle"></h3>
                    <div class="earnings-chart-total earnings-chart-heading__value"
                        :key="'heading-total-' + chartAnimationKey">
                        £<span x-text="formattedTotal"></span>
                        <span>/ <span x-text="periodShort"></span></span>
                    </div>
                </div>

                <div class="earnings-chart-wrap" role="img" aria-label="Earnings bar chart">
                    <div class="earnings-bar-chart">
                        <div class="earnings-bar-chart__y-axis" aria-hidden="true">
                            <template x-for="tick in yTicks" :key="chartAnimationKey + '-tick-' + tick">
                                <span class="earnings-bar-chart__tick" x-text="formatPound(tick)"></span>
                            </template>
                        </div>

                        <div class="earnings-bar-chart__plot">
                            <div class="earnings-bar-chart__bars">
                                <template x-for="(bar, index) in bars"
                                    :key="chartAnimationKey + '-bar-' + index + '-' + bar.label">
                                    <div class="earnings-bar-chart__bar-col is-clickable"
                                        :style="{ animationDelay: barStaggerDelay(index) }" @click="selectBar(index)"
                                        role="button" tabindex="0" @keydown.enter.prevent="selectBar(index)"
                                        @keydown.space.prevent="selectBar(index)"
                                        :aria-label="'View earnings for ' + bar.label">
                                        <div class="earnings-bar-chart__bar-track">
                                            <div class="earnings-bar-chart__bar"
                                                :style="{
                                                    height: barHeight(bar.value) + '%',
                                                    backgroundColor: barColor(index),
                                                    animationDelay: barStaggerDelay(index),
                                                }"
                                                :title="formatPound(bar.value)"></div>
                                        </div>
                                        <span class="earnings-bar-chart__label"
                                            :class="{ 'is-active': isActive(index) }" x-text="bar.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="earnings-layout-right" style="width: 40%;">
            <div class="earnings-breakdown-card">
                <div class="earnings-breakdown-body">
                    <div class="earnings-donut-wrap earnings-chart-wrap" wire:ignore>
                        <canvas id="earningsDonutChart" role="img" aria-label="Revenue breakdown chart"
                            data-labels='@json(array_column($breakdown['segments'], 'label'))' data-values='@json(array_column($breakdown['segments'], 'amount'))'
                            data-colors='@json($donutColors)'></canvas>
                    </div>

                    <div class="earnings-breakdown-content">
                        <div class="earnings-breakdown-header">
                            <h3>Revenue Breakdown</h3>
                            <p
                                class="earnings-breakdown-growth {{ $breakdown['growth_positive'] ? '' : 'is-negative' }}">
                                {{ $breakdown['growth_positive'] ? '+' : '' }}{{ $breakdown['growth'] }}% growth over
                                last
                                12 weeks
                            </p>
                        </div>

                        <div class="earnings-breakdown-legend">
                            @foreach ($breakdown['segments'] as $index => $segment)
                                <div class="earnings-legend-item">
                                    <span>{{ $segment['label'] }}</span>
                                    <strong>{{ $segment['percent'] }}%</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="earnings-recent-card">
                <div class="earnings-recent-header">
                    <h3>Recent Bookings</h3>
                    <a href="#"
                        @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'bookings'; window.dispatchEvent(new CustomEvent('booking-status-changed', { detail: { status: 'completed' } }))">View
                        All</a>
                </div>

                <div class="earnings-recent-list">
                    @forelse ($this->recentBookings as $booking)
                        <div class="earnings-recent-item">
                            @if ($isSpaceAccount)
                                <div class="earnings-recent-avatar-wrap">
                                    @if (filled($booking['client_photo']))
                                        <img class="earnings-recent-avatar" src="{{ $booking['client_photo'] }}"
                                            alt="{{ $booking['client_name'] }}"
                                            onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                                        <span
                                            class="earnings-recent-avatar-initials is-hidden">{{ $booking['client_initials'] }}</span>
                                    @else
                                        <span
                                            class="earnings-recent-avatar-initials">{{ $booking['client_initials'] }}</span>
                                    @endif
                                </div>
                                <div class="earnings-recent-meta">
                                    <p class="earnings-recent-name is-space">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13"
                                            viewBox="0 0 15 13" fill="none" aria-hidden="true">
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
                                        </svg>
                                        {{ $booking['visit_type_label'] }}
                                    </p>
                                    <p class="earnings-recent-time">{{ $booking['relative_time'] }}</p>
                                </div>
                            @else
                                <div class="earnings-recent-avatar-wrap">
                                    <img class="earnings-recent-avatar" src="{{ $booking['pet_photo'] }}"
                                        alt="{{ $booking['pet_name'] }}">
                                </div>
                                <div class="earnings-recent-meta">
                                    <p class="earnings-recent-name">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                            viewBox="0 0 16 15" fill="none">
                                            <path
                                                d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        {{ $booking['pet_name'] }}
                                    </p>
                                    <p class="earnings-recent-time">{{ $booking['relative_time'] }}</p>
                                </div>
                            @endif
                            <div class="earnings-recent-amount">+ £{{ number_format($booking['amount'], 2) }}</div>
                        </div>
                    @empty
                        <p class="earnings-empty">No completed bookings yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
