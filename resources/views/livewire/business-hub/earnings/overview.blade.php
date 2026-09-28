<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Support\BookingReceiptViewData;
use App\Support\BusinessHubNav;
use App\Support\InvoiceNumber;
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

    public string $payoutMonthlyDate = '';

    public string $payoutMonthlyMode = 'day_of_month';

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

    private function invoiceStatusPill(?string $status): array
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'failed' => ['label' => 'Failed', 'key' => 'failed'],
            'refunded' => ['label' => 'Refunded', 'key' => 'refunded'],
            'pending', 'processing', 'unpaid' => ['label' => 'Pending', 'key' => 'pending'],
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
        if ($frequency === 'Fortnightly') {
            $frequency = 'Weekly';
        }

        $this->payoutFrequency = in_array($frequency, ['Daily', 'Weekly', 'Monthly'], true) ? $frequency : 'Weekly';
        $this->payoutMonthlyDate = (string) ($details['payout_monthly_date'] ?? now()->format('Y-m-d'));
        $mode = (string) ($details['payout_monthly_mode'] ?? 'day_of_month');
        $this->payoutMonthlyMode = in_array($mode, ['day_of_month', 'nth_weekday'], true) ? $mode : 'day_of_month';
    }

    public function updatePayoutBankDetails(): void
    {
        $validated = $this->validate([
            'payoutBank' => ['nullable', 'string', 'max:100'],
            'payoutAccountHolderName' => ['required', 'string', 'max:255'],
            'payoutAccountNumber' => ['required', 'string', 'max:50'],
            'payoutSortCode' => ['required', 'string', 'max:20'],
        ]);

        $user = auth('groomer_spacer')->user();

        if (!$user) {
            return;
        }

        $details = $this->payoutDetails();
        $details = array_merge($details, [
            'bank' => trim((string) ($validated['payoutBank'] ?? '')),
            'account_holder_name' => $validated['payoutAccountHolderName'],
            'account_number' => $validated['payoutAccountNumber'],
            'sort_code' => $validated['payoutSortCode'],
        ]);

        $user->update(['payout_details' => $details]);

        $this->dispatch('payout-bank-details-saved');
    }

    public function updatePayoutFrequency(): void
    {
        $validated = $this->validate([
            'payoutFrequency' => ['required', 'string', 'in:Daily,Weekly,Monthly'],
            'payoutMonthlyDate' => ['nullable', 'date'],
            'payoutMonthlyMode' => ['nullable', 'string', 'in:day_of_month,nth_weekday'],
        ]);

        $user = auth('groomer_spacer')->user();

        if (!$user) {
            return;
        }

        $details = $this->payoutDetails();
        $details['payout_frequency'] = $validated['payoutFrequency'];
        $details['payout_monthly_date'] = filled($validated['payoutMonthlyDate'] ?? null) ? $validated['payoutMonthlyDate'] : ((string) ($details['payout_monthly_date'] ?? '') ?: now()->format('Y-m-d'));
        $details['payout_monthly_mode'] = filled($validated['payoutMonthlyMode'] ?? null) ? $validated['payoutMonthlyMode'] : ((string) ($details['payout_monthly_mode'] ?? '') ?: 'day_of_month');

        $user->update(['payout_details' => $details]);
        $this->refreshPayoutFrequency();

        $this->dispatch('payout-frequency-saved');
    }

    private function nextPayoutDateForFrequency(string $frequency): string
    {
        return $this->nextPayoutDate($frequency)->format('d F Y');
    }

    private function nextPayoutDate(string $frequency): \Carbon\Carbon
    {
        return $this->nextPayoutDateAfter($frequency, now()->copy()->subSecond());
    }

    /**
     * Next payout date strictly after $after (exclusive).
     */
    private function nextPayoutDateAfter(string $frequency, \Carbon\Carbon $after): \Carbon\Carbon
    {
        $after = $after->copy();

        if ($frequency === 'Daily') {
            $next = $after->copy()->addDay()->startOfDay();
            while ($next->isWeekend()) {
                $next->addDay();
            }

            return $next;
        }

        if ($frequency === 'Monthly') {
            return $this->nextMonthlyPayoutAfter($after);
        }

        $from = $after->copy()->addDay()->startOfDay();

        return $from->isTuesday() ? $from : $from->next(\Carbon\Carbon::TUESDAY);
    }

    private function nextMonthlyPayoutAfter(\Carbon\Carbon $after): \Carbon\Carbon
    {
        $details = $this->payoutDetails();
        $mode = (string) ($details['payout_monthly_mode'] ?? $this->payoutMonthlyMode ?: 'day_of_month');
        $rawDate = (string) ($details['payout_monthly_date'] ?? '');
        if ($rawDate === '') {
            $rawDate = (string) ($this->payoutMonthlyDate ?: now()->format('Y-m-d'));
        }

        try {
            $anchor = \Carbon\Carbon::parse($rawDate);
        } catch (\Throwable) {
            $anchor = now();
        }

        if ($mode === 'nth_weekday') {
            $month = $after->copy()->startOfMonth();
            $candidate = $month->copy()->firstOfMonth(\Carbon\Carbon::THURSDAY)->addWeeks(2)->startOfDay();
            if ($candidate->lte($after)) {
                $candidate = $after->copy()->addMonthNoOverflow()->startOfMonth()->firstOfMonth(\Carbon\Carbon::THURSDAY)->addWeeks(2)->startOfDay();
            }

            return $candidate;
        }

        $day = max(1, min(28, (int) $anchor->format('j')));
        $candidate = $after->copy()->day($day)->startOfDay();
        if ($candidate->lte($after)) {
            $candidate = $after->copy()->addMonthNoOverflow()->day($day)->startOfDay();
        }

        return $candidate;
    }

    /**
     * Payout date that will include earnings earned on $earningsDate.
     */
    private function payoutDateForEarningsOn(\Carbon\Carbon $earningsDate, string $frequency): \Carbon\Carbon
    {
        $date = $earningsDate->copy()->startOfDay();

        if ($frequency === 'Daily') {
            while ($date->isWeekend()) {
                $date->addDay();
            }

            return $date;
        }

        if ($frequency === 'Monthly') {
            $details = $this->payoutDetails();
            $mode = (string) ($details['payout_monthly_mode'] ?? $this->payoutMonthlyMode ?: 'day_of_month');
            $rawDate = (string) ($details['payout_monthly_date'] ?? '');
            if ($rawDate === '') {
                $rawDate = (string) ($this->payoutMonthlyDate ?: now()->format('Y-m-d'));
            }

            try {
                $anchor = \Carbon\Carbon::parse($rawDate);
            } catch (\Throwable) {
                $anchor = now();
            }

            if ($mode === 'nth_weekday') {
                $month = $date->copy()->startOfMonth();
                $candidate = $month->copy()->firstOfMonth(\Carbon\Carbon::THURSDAY)->addWeeks(2)->startOfDay();
                if ($candidate->lt($date)) {
                    $candidate = $date->copy()->addMonthNoOverflow()->startOfMonth()->firstOfMonth(\Carbon\Carbon::THURSDAY)->addWeeks(2)->startOfDay();
                }

                return $candidate;
            }

            $day = max(1, min(28, (int) $anchor->format('j')));
            $candidate = $date->copy()->day($day)->startOfDay();
            if ($candidate->lt($date)) {
                $candidate = $date->copy()->addMonthNoOverflow()->day($day)->startOfDay();
            }

            return $candidate;
        }

        return $date->isTuesday() ? $date->copy() : $date->copy()->next(\Carbon\Carbon::TUESDAY);
    }

    /**
     * @return list<\Carbon\Carbon>
     */
    private function upcomingPayoutDates(string $frequency, int $count = 12): array
    {
        $dates = [];
        $cursor = now()->copy()->subSecond();

        for ($i = 0; $i < $count; $i++) {
            $next = $this->nextPayoutDateAfter($frequency, $cursor);
            $dates[] = $next->copy();
            $cursor = $next->copy();
        }

        return $dates;
    }

    /**
     * Build future payout rows from the active frequency schedule + live earnings.
     *
     * @return list<array{date: string, amount: float, arrival_date: string, arrival_date_short: string}>
     */
    private function buildFuturePayoutItems(string $frequency, float $pendingAmount): array
    {
        $schedule = $this->upcomingPayoutDates($frequency, 12);
        $buckets = [];

        foreach ($schedule as $payoutDate) {
            $buckets[$payoutDate->format('Y-m-d')] = 0.0;
        }

        $nextKey = $schedule[0]->format('Y-m-d');
        if ($pendingAmount > 0) {
            $buckets[$nextKey] += $pendingAmount;
        }

        $futureBookings = $this->bookingsQuery()
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', now()->startOfDay())
            ->orderBy('date')
            ->get(['id', 'date', 'amount']);

        $nextPayout = $schedule[0]->copy()->startOfDay();

        foreach ($futureBookings as $booking) {
            if (!$booking->date) {
                continue;
            }

            $payoutOn = $this->payoutDateForEarningsOn($booking->date, $frequency)->startOfDay();
            if ($payoutOn->lt($nextPayout)) {
                $payoutOn = $nextPayout->copy();
            }

            $key = $payoutOn->format('Y-m-d');
            if (!array_key_exists($key, $buckets)) {
                $buckets[$key] = 0.0;
            }
            $buckets[$key] += (float) $booking->amount;
        }

        $scheduleKeys = [];
        foreach ($schedule as $payoutDate) {
            $scheduleKeys[$payoutDate->format('Y-m-d')] = true;
        }

        $items = [];
        foreach ($schedule as $index => $payoutDate) {
            $key = $payoutDate->format('Y-m-d');
            $amount = round((float) ($buckets[$key] ?? 0), 2);

            // Always keep the next two schedule dates so frequency changes are visible immediately.
            if ($amount <= 0 && $index >= 2) {
                continue;
            }

            $arrival = $payoutDate->copy()->addWeekdays(1);
            $items[] = [
                'date' => $payoutDate->format('d/m/Y'),
                'amount' => $amount,
                'arrival_date' => $arrival->format('d/m/Y'),
                'arrival_date_short' => $arrival->format('d/m'),
            ];
        }

        foreach ($buckets as $ymd => $amount) {
            if ($amount <= 0 || isset($scheduleKeys[$ymd])) {
                continue;
            }

            $payoutDate = \Carbon\Carbon::parse($ymd)->startOfDay();
            $arrival = $payoutDate->copy()->addWeekdays(1);
            $items[] = [
                'date' => $payoutDate->format('d/m/Y'),
                'amount' => round((float) $amount, 2),
                'arrival_date' => $arrival->format('d/m/Y'),
                'arrival_date_short' => $arrival->format('d/m'),
            ];
        }

        return $items;
    }

    private function nextPayoutShortLabel(string $frequency): string
    {
        $date = $this->nextPayoutDate($frequency);
        $day = match ($date->format('D')) {
            'Tue' => 'Tues',
            'Thu' => 'Thurs',
            default => $date->format('D'),
        };

        return 'Next auto pay out ' . $day . ', ' . $date->format('j M');
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

        $thisMonthStart = now()->copy()->startOfMonth();
        $lastMonthStart = now()->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = now()->copy()->subMonthNoOverflow()->endOfMonth();

        $thisMonth = $this->completedBookingsQuery()->whereDate('date', '>=', $thisMonthStart)->selectRaw('COUNT(*) as booking_count, COALESCE(SUM(amount), 0) as total_revenue')->first();
        $lastMonth = $this->completedBookingsQuery()->whereDate('date', '>=', $lastMonthStart)->whereDate('date', '<=', $lastMonthEnd)->selectRaw('COUNT(*) as booking_count, COALESCE(SUM(amount), 0) as total_revenue')->first();

        $thisMonthTotal = (float) ($thisMonth->total_revenue ?? 0);
        $lastMonthTotal = (float) ($lastMonth->total_revenue ?? 0);
        $bookingDelta = (int) ($thisMonth->booking_count ?? 0) - (int) ($lastMonth->booking_count ?? 0);
        $revenueDelta = $thisMonthTotal - $lastMonthTotal;

        return [
            'total_earnings' => $total,
            'average_revenue' => $average,
            'booking_count' => $count,
            'this_month' => $thisMonthTotal,
            'this_month_label' => now()->format('F Y'),
            'previous_month_short' => now()->copy()->subMonthNoOverflow()->format('M'),
            'booking_delta' => $bookingDelta,
            'booking_delta_positive' => $bookingDelta >= 0,
            'revenue_delta' => $revenueDelta,
            'revenue_delta_positive' => $revenueDelta >= 0,
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

        $bookings = $this->completedBookingsQuery()
            ->with(['pets', 'petOwner:' . implode(',', $ownerColumns)])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->limit(3)
            ->get();

        $ownerIds = $bookings->pluck('pet_owner_id')->filter()->unique()->values();
        $completedCounts = $ownerIds->isEmpty() ? collect() : $this->completedBookingsQuery()->whereIn('pet_owner_id', $ownerIds)->selectRaw('pet_owner_id, COUNT(*) as completed_count')->groupBy('pet_owner_id')->pluck('completed_count', 'pet_owner_id');

        return $bookings
            ->map(function (Booking $booking) use ($isSpaceAccount, $completedCounts) {
                $completedAt = $booking->date?->copy()->endOfDay() ?? now();
                $avatar = $this->recentBookingAvatar($booking, $isSpaceAccount);
                $completedCount = (int) ($completedCounts[$booking->pet_owner_id] ?? 0);
                $clientType = $completedCount <= 1 ? 'new' : ($completedCount <= 4 ? 'regular' : 'repeat');
                $displayName = $isSpaceAccount ? $this->visitTypeLabel($booking->visit_type) : $booking->pets->first()?->name ?? 'Pet';
                $initialSource = $isSpaceAccount ? (string) ($booking->petOwner?->name ?? $displayName) : $displayName;

                return [
                    'display_name' => $displayName,
                    'photo' => $avatar['photo'],
                    'initial' => Str::upper(Str::substr(trim($initialSource), 0, 1) ?: '?'),
                    'client_type' => $clientType,
                    'client_type_label' => match ($clientType) {
                        'repeat' => 'Repeat Client',
                        'regular' => 'Regular Client',
                        default => 'New Client',
                    },
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
            ->with(['booking:id,goormer_spacer_id,time,service,visit_type,amount,discount,extra_add_ons,date,pet_owner_id', 'booking.petOwner:id,name,profile_image', 'petOwner:id,name,profile_image', 'pet:id,name,pet_type'])
            ->orderByDesc('date')
            ->limit(50)
            ->get(['id', 'booking_id', 'pet_owner_id', 'pet_detail_id', 'date', 'amount', 'status', 'payment_method', 'service_type'])
            ->values()
            ->map(function (Payment $payment) use ($isSpaceAccount) {
                $bookingTime = trim((string) ($payment->booking?->time ?? ''));
                $time = $this->normalizedBookingTime($bookingTime);
                $status = $this->transactionStatusPill($payment->status);
                $method = filled($payment->payment_method) ? $payment->payment_method : 'N/A';
                $sortTs = $payment->date?->timestamp ?? 0;
                if ($time !== '' && preg_match('/^(\d{1,2}):(\d{2})$/', $time, $parts)) {
                    $sortTs += ((int) $parts[1] * 60 + (int) $parts[2]) * 60;
                }

                $row = [
                    'date' => $payment->date?->format('d/m/y') ?? '--/--/--',
                    'time' => $time !== '' ? $time : '--:--',
                    'sort_ts' => $sortTs,
                    'amount' => (float) $payment->amount,
                    'payment_method' => $method,
                    'status_label' => $status['label'],
                    'status_key' => $status['key'],
                    'booking_id' => (int) $payment->booking_id,
                    'booking_reference' => 'FG-' . str_pad((string) $payment->booking_id, 5, '0', STR_PAD_LEFT),
                ];

                if ($isSpaceAccount) {
                    $row['client'] = trim((string) ($payment->petOwner?->name ?? 'Unknown Client'));
                    $row['space'] = str_replace(' / ', '/', $this->visitTypeLabel($payment->booking?->visit_type));
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
    public function invoices(): array
    {
        $platformPercent = (float) config('services.fursgo.platform_fee_percent', 5);
        $platformPercent = max(0, min(100, $platformPercent));

        return Payment::query()
            ->whereHas('booking', function ($query) {
                $query->where(['goormer_spacer_id' => $this->loggedInSpacerId() ?? 0])->where(['booking_status' => 'completed']);
            })
            ->with(['booking:id,goormer_spacer_id,pet_owner_id,date,service,amount,discount,extra_add_ons,booking_status', 'petOwner:id,name'])
            ->orderByDesc('date')
            ->limit(40)
            ->get(['id', 'booking_id', 'pet_owner_id', 'date', 'amount', 'status'])
            ->values()
            ->map(function (Payment $payment) use ($platformPercent) {
                $booking = $payment->booking;
                $bookingDate = $booking?->date ?? $payment->date;
                $serviceAmount = (float) ($booking?->amount ?? $payment->amount);
                $extrasAmount = (float) collect(is_array($booking?->extra_add_ons) ? $booking->extra_add_ons : [])->sum(fn($addon) => (float) ($addon['amount'] ?? 0));
                $discount = (float) ($booking?->discount ?? 0);
                $gross = max(0, $serviceAmount + $extrasAmount - $discount);
                $tax = round($gross * ($platformPercent / 100), 2);
                $status = $this->invoiceStatusPill($payment->status);

                return [
                    'date' => $bookingDate?->format('d/m/y') ?? '--/--/--',
                    'time' => $bookingDate?->format('H:i') ?? '',
                    'date_iso' => $bookingDate?->format('Y-m-d') ?? '',
                    'sort_ts' => $bookingDate?->getTimestamp() ?? 0,
                    'invoice_no' => InvoiceNumber::customerBooking((int) $payment->booking_id, $bookingDate),
                    'booking_reference' => 'FG-' . str_pad((string) $payment->booking_id, 5, '0', STR_PAD_LEFT),
                    'client' => trim((string) ($payment->petOwner?->name ?? 'Unknown Client')),
                    'reference' => filled($booking?->service) ? (string) $booking->service : 'Booking',
                    'gross' => $gross,
                    'tax' => $tax,
                    'total' => round($gross + $tax, 2),
                    'status_label' => $status['label'],
                    'status_key' => $status['key'],
                    'invoice_url' => $booking ? route('business-hub.bookings.invoice-pdf', $booking) : null,
                ];
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

        $frequency = (string) ($payoutDetails['payout_frequency'] ?? 'Weekly');
        if ($frequency === 'Fortnightly') {
            $frequency = 'Weekly';
        }
        if (!in_array($frequency, ['Daily', 'Weekly', 'Monthly'], true)) {
            $frequency = 'Weekly';
        }

        $futureBookings = $this->buildFuturePayoutItems($frequency, (float) $pendingAmount);
        $futureAmount = collect($futureBookings)->sum(fn(array $booking) => (float) $booking['amount']);
        $futurePreviewBookings = array_values(array_slice($futureBookings, 0, 2));

        $history = $this->payoutEligiblePaymentsQuery()
            ->with('booking:id,date')
            ->orderByDesc('date')
            ->limit(10)
            ->get(['id', 'booking_id', 'date', 'amount'])
            ->map(
                fn(Payment $payment) => [
                    'date' => $payment->date?->format('d/m/y') ?? '--/--/--',
                    'amount' => (float) $payment->amount,
                    'status' => 'Confirmed',
                    'reference' => 'FG-' . str_pad((string) $payment->booking_id, 5, '0', STR_PAD_LEFT),
                    'invoice_url' => $payment->booking ? route('business-hub.bookings.invoice-pdf', $payment->booking) : null,
                ],
            )
            ->all();

        $nextPayout = $this->nextPayoutDate($frequency);
        $nextWeekly = now()->next('Tuesday');

        return [
            'pending_amount' => (float) $pendingAmount,
            'total_amount' => (float) $totalPayouts,
            'future_amount' => (float) $futureAmount,
            'future_items' => $futurePreviewBookings,
            'future_items_all' => $futureBookings,
            'history' => $history,
            'frequency' => $frequency,
            'next_payout_date' => $nextPayout->format('d F Y'),
            'next_payout_friendly' => $nextPayout->format('l j F'),
            'next_payout_short' => $this->nextPayoutShortLabel($frequency),
            'next_payout_day_month' => $nextPayout->format('j F'),
            'next_weekly_day_month' => $nextWeekly->format('j F'),
            'bank' => [
                'verified' => filled($payoutDetails['bank'] ?? null) && filled($payoutDetails['account_holder_name'] ?? null) && filled($payoutDetails['account_number'] ?? null),
                'name' => filled($payoutDetails['bank'] ?? null) ? $payoutDetails['bank'] : 'Bank details',
                'account_number' => $this->maskedAccountNumber($payoutDetails['account_number'] ?? null),
                'account_holder' => $payoutDetails['account_holder_name'] ?? 'Not added',
                'ending' => (function () use ($payoutDetails) {
                    $digits = preg_replace('/\D+/', '', (string) ($payoutDetails['account_number'] ?? ''));

                    return $digits !== '' ? substr($digits, -4) : 'Not added';
                })(),
            ],
        ];
    }
}; ?>

@php
    $primaryColor = '#FBAC83';
    $lightColor = '#FBAC83';
    $donutColors = ['#CBDCE8', '#D8E8B7', '#FFC97A'];
    $isSpaceAccount = $this->isSpaceAccount();
    $breakdown = $this->revenueBreakdown;
    $breakdownTotal = (float) collect($breakdown['segments'])->sum('amount');
    $breakdownTotalLabel = $breakdownTotal >= 1000 ? '£' . rtrim(rtrim(number_format($breakdownTotal / 1000, 1), '0'), '.') . 'k' : '£' . number_format($breakdownTotal, $breakdownTotal == 0.0 ? 0 : 2);
    $summary = $this->summary;
    $formatStatAmount = function (float $amount, bool $alwaysDecimals = false): string {
        $whole = abs($amount - round($amount)) < 0.005;

        return number_format($amount, $alwaysDecimals || !$whole ? 2 : 0);
    };
    $bookingDeltaLabel = match (true) {
        $summary['booking_delta'] > 0 => '+' . $summary['booking_delta'] . ' vs last month',
        $summary['booking_delta'] < 0 => '−' . abs($summary['booking_delta']) . ' vs last month',
        default => '0 vs last month',
    };
    $revenueDeltaLabel = match (true) {
        $summary['revenue_delta'] > 0 => '+ £' . $formatStatAmount($summary['revenue_delta']) . ' vs ' . $summary['previous_month_short'],
        $summary['revenue_delta'] < 0 => '− £' . $formatStatAmount(abs($summary['revenue_delta'])) . ' vs ' . $summary['previous_month_short'],
        default => '£0 vs ' . $summary['previous_month_short'],
    };
    $chartBookings = $this->chartBookings;
    $transactions = $this->transactions;
    $invoices = $this->invoices;
    $payouts = $this->payouts;
    $now = now();
    $dashboardEarningsMenu = BusinessHubNav::fromSession()['active_earnings_menu'];
@endphp

<div class="earnings-overview" x-data="{ activeEarningsMenu: @js($dashboardEarningsMenu) }"
    x-on:earnings-menu-selected.window="activeEarningsMenu = $event.detail?.menu || 'overview'"
    x-init="$nextTick(() => window.mountEarningsCharts?.($el))">
    <style>
        .earnings-overview {
            --earnings-primary:
                {{ $primaryColor }};
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
            flex-direction: column;
            gap: 20px;
            align-items: stretch;
            width: 100%;
            margin-top: 0;
        }

        .earnings-menu-tabs {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .earnings-menu-tab {
            appearance: none;
            -webkit-appearance: none;
            border: 0;
            background: #F6F5F5;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: 1;
            border-radius: 100px;
            height: 42px;
            padding: 0 20px;
            cursor: pointer;
        }

        .earnings-menu-tab.is-active {
            background: #3B3731;
            color: #fff;
        }

        .earnings-overview-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .earnings-summary-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .earnings-stat-card {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            padding: 21px 20px 16px;
            background: #fff;
            width: 100%;
            min-height: 140px;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
            min-width: 0;
        }

        .earnings-stat-card--payout {
            border-color: #FFD88C;
            background: linear-gradient(316deg, #FFFDF9 16.58%, #FFF8EA 89.9%);
            box-shadow: none;
        }

        .earnings-stat-label {
            margin: 0;
            color: #565149;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-stat-value {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-stat-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            min-width: 0;
        }

        .earnings-stat-note {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-stat-delta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 28px;
            padding: 0 10px;
            border-radius: 74px;
            background: rgba(209, 235, 154, 0.2);
            color: #8BAE40;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            letter-spacing: 0.12px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .earnings-stat-delta img {
            display: block;
            flex-shrink: 0;
        }

        .earnings-stat-delta.is-negative {
            background: rgba(255, 110, 110, 0.12);
            color: #D94848;
        }

        .earnings-stat-delta.is-negative img {
            transform: rotate(180deg);
        }

        .earnings-panels {
            display: grid;
            grid-template-columns: minmax(0, 610fr) minmax(320px, 400fr);
            gap: 20px;
            align-items: stretch;
        }

        .earnings-side {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
            min-height: 472px;
        }

        .earnings-breakdown-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 0 0 auto;
            min-height: 182px;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
            padding: 20px 20px 18px;
        }

        .earnings-breakdown-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .earnings-breakdown-top h3 {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-breakdown-total {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-style: normal;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .earnings-breakdown-total span {
            margin-left: 6px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-breakdown-body {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-top: 8px;
        }

        .earnings-donut-wrap {
            position: relative;
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }

        .earnings-donut {
            display: block;
            width: 100px;
            height: 100px;
        }

        .earnings-breakdown-legend {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
            min-width: 0;
            margin: 0;
        }

        .earnings-legend-item {
            display: grid;
            grid-template-columns: 10px minmax(0, 1fr) auto;
            align-items: center;
            column-gap: 10px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: block;
        }

        .earnings-legend-item strong {
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-chart-card,
        .earnings-recent-card {
            border-radius: 10px;
            border: 1px solid #E2E2E2;
            background: #fff;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
        }

        .earnings-chart-card {
            display: flex;
            flex-direction: column;
            padding: 20px 20px 18px;
            min-height: 472px;
            height: 100%;
        }

        .earnings-chart-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: nowrap;
            margin-bottom: 14px;
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
            background: #F9FAFC;
            border: 0;
            border-radius: 100px;
            padding: 3px;
            height: 42px;
            width: 243px;
            flex-shrink: 0;
        }

        .earnings-period-toggle button {
            appearance: none;
            -webkit-appearance: none;
            flex: 1;
            border: 0;
            background: transparent;
            color: #888;
            text-align: center;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            height: 36px;
            min-width: 0;
            padding: 0;
            border-radius: 100px;
            cursor: pointer;
            transition:
                background-color 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                color 0.28s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.28s ease;
        }

        .earnings-period-toggle button:focus {
            outline: none;
        }

        .earnings-period-toggle button.is-active {
            background: #fff;
            color: #3B3731;
            box-shadow: 0 2px 4px rgba(59, 55, 49, 0.1);
        }

        .earnings-chart-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 18px;
            min-height: 30px;
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

        .earnings-chart-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-chart-total {
            display: inline-flex;
            align-items: baseline;
            gap: 0;
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-chart-total-amount {
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-weight: 600;
            color: #3B3731;
            line-height: normal;
        }

        .earnings-chart-total-period {
            margin-left: 8px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-weight: 400;
            font-size: 14px;
            line-height: normal;
        }

        .earnings-chart-range {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-chart-wrap {
            position: relative;
            flex: 1;
            min-height: 302px;
            height: 302px;
        }

        .earnings-bar-chart {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            grid-template-rows: minmax(0, 1fr) 17px;
            column-gap: 16px;
            row-gap: 16px;
            height: 100%;
            width: 100%;
        }

        .earnings-bar-chart__y-ticks {
            grid-column: 1;
            grid-row: 1;
            display: flex;
            flex-direction: column-reverse;
            justify-content: space-between;
            align-items: flex-end;
            min-height: 0;
            height: 100%;
        }

        .earnings-bar-chart__tick {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
            text-align: right;
        }

        .earnings-bar-chart__tracks {
            grid-column: 2;
            grid-row: 1;
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 0;
            min-height: 0;
            height: 100%;
            padding: 0 4px;
        }

        .earnings-bar-chart__bar-col {
            flex: 1 1 0;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-width: 0;
            max-width: 90px;
            height: 100%;
            animation: earningsBarColFade 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .earnings-bar-chart__bar-col.is-clickable {
            cursor: pointer;
        }

        .earnings-bar-chart__bar-col.is-clickable:hover .earnings-bar-chart__bar {
            filter: brightness(0.96);
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

        .earnings-bar-chart__bar {
            width: 50px;
            max-width: 100%;
            border-radius: 10px 10px 5px 5px;
            background: #FBAC83;
            opacity: 0.5;
            min-height: 0;
            align-self: flex-end;
            transform-origin: bottom center;
            transition:
                height 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                opacity 0.35s ease;
            animation: earningsBarGrow 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .earnings-bar-chart__bar.is-active {
            opacity: 1;
        }

        @keyframes earningsBarGrow {
            from {
                transform: scaleY(0);
            }

            to {
                transform: scaleY(1);
            }
        }

        .earnings-bar-chart__labels {
            grid-column: 2;
            grid-row: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0;
            padding: 0 4px;
            height: 17px;
        }

        .earnings-bar-chart__label {
            flex: 1 1 0;
            max-width: 90px;
            border: 0;
            background: transparent;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: 17px;
            text-align: center;
            white-space: nowrap;
            height: 17px;
            padding: 0;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .earnings-bar-chart__label.is-active {
            color: #3B3731;
        }

        .earnings-bar-chart__label:hover {
            color: #3B3731;
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
            display: flex;
            flex-direction: column;
            padding: 20px 20px 8px;
            flex: 1 1 auto;
            min-height: 270px;
        }

        .earnings-recent-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .earnings-recent-header h3 {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-recent-header a {
            color: #FFC97A;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration: none;
        }

        .earnings-recent-header a:hover {
            text-decoration: underline;
        }

        .earnings-recent-list {
            display: flex;
            flex-direction: column;
        }

        .earnings-recent-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr) auto auto;
            align-items: center;
            column-gap: 12px;
            background: transparent;
            border-radius: 0;
            padding: 15px 0;
            box-shadow: none;
            border-bottom: 1px solid #EEEEEE;
        }

        .earnings-recent-item:last-child {
            border-bottom: 0;
        }

        .earnings-recent-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .earnings-recent-avatar-wrap {
            width: 42px;
            height: 42px;
            box-sizing: border-box;
            border-radius: 999px;
            border: 0;
            background: #FBAC83;
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
            background: #FBAC83;
            color: #FDFDFD;
            text-align: center;
            font-family: Lato, sans-serif;
            font-size: 18px;
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
            min-width: 0;
        }

        .earnings-recent-name {
            display: block;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 0;
        }

        .earnings-recent-time {
            margin: 2px 0 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-client-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            padding: 0 10px;
            border-radius: 100px;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            white-space: nowrap;
            grid-column: 3;
            justify-self: end;
        }

        .earnings-client-badge.is-repeat {
            color: #94BEDB;
            background: rgba(216, 229, 238, 0.2);
        }

        .earnings-client-badge.is-regular {
            color: #F9C45C;
            background: rgba(255, 201, 122, 0.1);
        }

        .earnings-client-badge.is-new {
            color: #AFCD6F;
            background: rgba(186, 207, 142, 0.1);
        }

        .earnings-recent-amount {
            color: #A1BF63;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            white-space: nowrap;
            grid-column: 4;
            justify-self: end;
        }

        @media (max-width: 980px) {
            .earnings-summary-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .earnings-panels {
                grid-template-columns: 1fr;
            }

            .earnings-side,
            .earnings-chart-card {
                min-height: 0;
            }
        }

        @media (max-width: 640px) {
            .earnings-summary-cards {
                grid-template-columns: 1fr;
            }

            .earnings-period-toggle {
                width: 100%;
            }

            .earnings-chart-heading {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        .earnings-empty {
            color: #9D9B98;
            font-family: Lato;
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
            font-family: Lato;
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

        .earnings-receipt-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(59, 55, 49, 0.35);
            opacity: 0;
            pointer-events: none;
            transition: opacity 180ms ease;
        }

        .earnings-receipt-modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .earnings-receipt-modal {
            width: 610px;
            max-width: 100%;
            max-height: calc(100vh - 48px);
            overflow: auto;
            background: #fff;
            border-radius: 10px;
            opacity: 0;
            transform: translateY(12px) scale(0.98);
            transition:
                opacity 180ms ease,
                transform 180ms ease;
        }

        .earnings-receipt-modal-overlay.is-open .earnings-receipt-modal {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        @media (prefers-reduced-motion: reduce) {

            .earnings-receipt-modal-overlay,
            .earnings-receipt-modal {
                transition: none;
            }
        }

        .earnings-receipt-modal__head {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80px;
            background: rgba(203, 220, 232, 0.2);
            border-radius: 10px 10px 0 0;
        }

        .earnings-receipt-modal__head.is-space {
            background: #F5F8FA;
        }

        .earnings-receipt-modal__title {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 28px;
            font-weight: 800;
            line-height: normal;
            text-align: center;
        }

        .earnings-receipt-modal__title span {
            font-weight: 600;
        }

        .earnings-receipt-modal__close {
            position: absolute;
            top: 30px;
            right: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14.5px;
            height: 14.5px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .earnings-receipt-modal__close img {
            width: 14.5px;
            height: 14.5px;
            max-width: 14.5px;
            display: block;
        }

        .earnings-receipt-modal__body {
            padding: 0 25px 32px;
        }

        .earnings-receipt-modal__id,
        .earnings-receipt-modal__line,
        .earnings-receipt-modal__total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .earnings-receipt-modal__id {
            min-height: 56px;
            margin: 0;
            border-bottom: 1px solid #E2E2E2;
            color: #000;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
        }

        .earnings-receipt-modal__id p {
            margin: 0;
        }

        .earnings-receipt-modal__id-end {
            display: inline-flex;
            align-items: center;
            gap: 20px;
            color: #9D9B98;
        }

        .earnings-receipt-modal__download {
            position: relative;
            display: inline-flex;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .earnings-receipt-modal__download img {
            width: 36px;
            height: 36px;
            max-width: 36px;
            display: block;
        }

        .earnings-receipt-modal__download-glyph {
            position: absolute;
            top: 8px;
            left: 10px;
            width: 16px !important;
            height: 19px !important;
            max-width: 16px !important;
        }

        .earnings-receipt-modal__person {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 0 0;
        }

        .earnings-receipt-modal__avatar,
        .earnings-receipt-modal__avatar img,
        .earnings-receipt-modal__avatar span {
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .earnings-receipt-modal__avatar {
            display: inline-flex;
            flex: 0 0 44px;
            overflow: hidden;
            background: #E7EEF3;
        }

        .earnings-receipt-modal__avatar img {
            max-width: 44px;
            object-fit: cover;
            display: block;
        }

        .earnings-receipt-modal__avatar span {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 600;
        }

        .earnings-receipt-modal__name,
        .earnings-receipt-modal__pet {
            display: block;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            line-height: normal;
        }

        .earnings-receipt-modal__name {
            font-weight: 600;
        }

        .earnings-receipt-modal__pet {
            font-weight: 400;
        }

        .earnings-receipt-modal__pet [data-field="pet_type"] {
            color: #9D9B98;
        }

        .earnings-receipt-modal__section,
        .earnings-receipt-modal__summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E2E2E2;
        }

        .earnings-receipt-modal__section-title {
            margin: 0 0 20px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-receipt-modal__line {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
        }

        .earnings-receipt-modal__line+.earnings-receipt-modal__line {
            margin-top: 10px;
        }

        .earnings-receipt-modal__line span:last-child {
            color: #3B3731;
            text-align: right;
            white-space: nowrap;
        }

        .earnings-receipt-modal__summary {
            padding-bottom: 20px;
            border-bottom: 1px solid #E2E2E2;
        }

        .earnings-receipt-modal__total {
            padding-top: 20px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 20px;
            font-weight: 700;
            line-height: normal;
        }

        .earnings-transactions-empty {
            text-align: center !important;
            color: #9D9B98 !important;
            font-size: 15px !important;
        }
    </style>

    <div class="earnings-layout">
        <nav class="earnings-menu-tabs" aria-label="Earnings sections">
            @foreach (['overview' => 'Overview', 'transactions' => 'Transactions', 'pay-outs' => 'Pay-outs', 'invoices' => 'Invoices'] as $menu => $label)
                <button type="button" class="earnings-menu-tab" :class="{ 'is-active': activeEarningsMenu === @js($menu) }"
                    @click="if (activeEarningsMenu === @js($menu)) return; window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeEarningsMenu = @js($menu); window.dispatchEvent(new CustomEvent('earnings-menu-selected', { detail: { menu: @js($menu) } })); window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'earnings', active_earnings_menu: @js($menu) } }))">
                    {{ $label }}
                </button>
            @endforeach
        </nav>

        <div x-show="activeEarningsMenu === 'transactions'" x-cloak>
            <x-business-hub.earnings.transactions :transactions="$transactions" :is-space-user="$isSpaceAccount" />
        </div>

        <div x-show="activeEarningsMenu === 'pay-outs'" x-cloak>
            <x-business-hub.earnings.payouts :payouts="$payouts" />
        </div>

        <div x-show="activeEarningsMenu === 'invoices'" x-cloak>
            <x-business-hub.earnings.invoices :invoices="$invoices" />
        </div>

        <div class="earnings-overview-panel"
            x-show="activeEarningsMenu !== 'transactions' && activeEarningsMenu !== 'pay-outs' && activeEarningsMenu !== 'invoices'"
            x-cloak>
            <div class="earnings-summary-cards">
                <article class="earnings-stat-card earnings-stat-card--payout">
                    <p class="earnings-stat-label">Next pay-out total</p>
                    <p class="earnings-stat-value">£{{ number_format((float) $payouts['pending_amount'], 2) }}</p>
                    <div class="earnings-stat-footer">
                        <p class="earnings-stat-note">{{ $payouts['next_payout_short'] }}</p>
                    </div>
                </article>
                <article class="earnings-stat-card">
                    <p class="earnings-stat-label">Total earnings</p>
                    <p class="earnings-stat-value">£{{ $formatStatAmount((float) $summary['total_earnings']) }}</p>
                    <div class="earnings-stat-footer">
                        <p class="earnings-stat-note">All time</p>
                        <span class="earnings-stat-delta {{ $summary['booking_delta_positive'] ? '' : 'is-negative' }}">
                            @if ($summary['booking_delta'] > 0)
                                <img src="{{ asset('images/business-hub/icon-earnings-trend-up.svg') }}" alt="" width="5.8"
                                    height="8.93">
                            @endif
                            {{ $bookingDeltaLabel }}
                        </span>
                    </div>
                </article>
                <article class="earnings-stat-card">
                    <p class="earnings-stat-label">This month</p>
                    <p class="earnings-stat-value">£{{ $formatStatAmount((float) $summary['this_month']) }}</p>
                    <div class="earnings-stat-footer">
                        <p class="earnings-stat-note">{{ $summary['this_month_label'] }}</p>
                        <span class="earnings-stat-delta {{ $summary['revenue_delta_positive'] ? '' : 'is-negative' }}">
                            {{ $revenueDeltaLabel }}
                        </span>
                    </div>
                </article>
                <article class="earnings-stat-card">
                    <p class="earnings-stat-label">Avg per booking</p>
                    <p class="earnings-stat-value">£{{ $formatStatAmount((float) $summary['average_revenue']) }}</p>
                    <div class="earnings-stat-footer">
                        <p class="earnings-stat-note">Across {{ number_format((int) $summary['booking_count']) }}
                            bookings</p>
                    </div>
                </article>
            </div>

            <div class="earnings-panels">
                <div class="earnings-chart-card" wire:ignore x-data="earningsChartPanel(@js([
    'bookings' => $chartBookings,
    'month' => (int) $now->month,
    'year' => (int) $now->year,
    'period' => 'month',
    'primary' => $primaryColor,
    'light' => $lightColor,
]))">
                    <div class="earnings-chart-toolbar">
                        <h3 class="earnings-chart-title">Earnings</h3>
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
                        <div class="earnings-chart-total earnings-chart-heading__value"
                            :key="'heading-total-' + chartAnimationKey">
                            <span class="earnings-chart-total-amount">£<span x-text="formattedTotal"></span></span><span
                                class="earnings-chart-total-period">/ <span x-text="periodShort"></span></span>
                        </div>
                        <p class="earnings-chart-range" x-text="rangeLabel"></p>
                    </div>

                    <div class="earnings-chart-wrap" role="img" aria-label="Earnings bar chart">
                        <div class="earnings-bar-chart">
                            <div class="earnings-bar-chart__y-ticks" aria-hidden="true">
                                <span class="earnings-bar-chart__tick">£0</span>
                                <span class="earnings-bar-chart__tick">£100</span>
                                <span class="earnings-bar-chart__tick">£250</span>
                                <span class="earnings-bar-chart__tick">£500</span>
                                <span class="earnings-bar-chart__tick">£1K</span>
                            </div>

                            <div class="earnings-bar-chart__tracks">
                                <template x-for="(bar, index) in bars"
                                    :key="chartAnimationKey + '-track-' + index + '-' + bar.label">
                                    <div class="earnings-bar-chart__bar-col is-clickable"
                                        :style="{ animationDelay: barStaggerDelay(index) }" @click="selectBar(index)"
                                        role="button" tabindex="0" @keydown.enter.prevent="selectBar(index)"
                                        @keydown.space.prevent="selectBar(index)"
                                        :aria-label="'View earnings for ' + bar.label">
                                        <div class="earnings-bar-chart__bar" :class="{ 'is-active': isActive(index) }"
                                            :style="{
                                                height: barHeight(bar.value) + '%',
                                                animationDelay: barStaggerDelay(index),
                                            }" :title="formatPound(bar.value)"></div>
                                    </div>
                                </template>
                            </div>

                            <div class="earnings-bar-chart__labels">
                                <template x-for="(bar, index) in bars"
                                    :key="chartAnimationKey + '-label-' + index + '-' + bar.label">
                                    <button type="button" class="earnings-bar-chart__label"
                                        :class="{ 'is-active': isActive(index) }" @click="selectBar(index)"
                                        x-text="bar.label"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="earnings-side">
                    <div class="earnings-breakdown-card">
                        <div class="earnings-breakdown-top">
                            <h3>Revenue Breakdown</h3>
                            <p class="earnings-breakdown-total">{{ $breakdownTotalLabel }} <span>Total</span></p>
                        </div>
                        <div class="earnings-breakdown-body">
                            <div class="earnings-donut-wrap">
                                @php
                                    $donutRadius = 36;
                                    $donutCircumference = 2 * M_PI * $donutRadius;
                                    $donutCursor = 0;
                                    $donutPercentTotal = (float) collect($breakdown['segments'])->sum('percent');
                                    $donutHasValue = $donutPercentTotal > 0;
                                @endphp
                                <svg class="earnings-donut" width="100" height="100" viewBox="0 0 100 100" role="img"
                                    aria-label="Revenue breakdown chart">
                                    <g transform="rotate(-90 50 50)">
                                        @if (!$donutHasValue)
                                            <circle cx="50" cy="50" r="{{ $donutRadius }}" fill="none" stroke="#F3F1EE"
                                                stroke-width="18" />
                                        @else
                                            @foreach ($breakdown['segments'] as $index => $segment)
                                                @php
                                                    $donutPortion = ((float) $segment['percent'] / $donutPercentTotal) * $donutCircumference;
                                                    $donutDraw = $donutPortion + 1.25;
                                                @endphp
                                                @if ($donutPortion > 0)
                                                    <circle cx="50" cy="50" r="{{ $donutRadius }}" fill="none"
                                                        stroke="{{ $donutColors[$index] ?? '#CBDCE8' }}" stroke-width="18"
                                                        stroke-linecap="butt"
                                                        stroke-dasharray="{{ round($donutDraw, 2) }} {{ round($donutCircumference, 2) }}"
                                                        stroke-dashoffset="{{ round(-$donutCursor, 2) }}" />
                                                @endif
                                                @php $donutCursor += $donutPortion; @endphp
                                            @endforeach
                                        @endif
                                    </g>
                                </svg>
                            </div>
                            <div class="earnings-breakdown-legend">
                                @foreach ($breakdown['segments'] as $index => $segment)
                                    <div class="earnings-legend-item">
                                        <span class="earnings-legend-dot"
                                            style="background: {{ $donutColors[$index] ?? '#CBDCE8' }}"></span>
                                        <span>{{ $segment['label'] }}</span>
                                        <strong>{{ $segment['percent'] }}%</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="earnings-recent-card">
                        <div class="earnings-recent-header">
                            <h3>Recent Transactions</h3>
                            <a href="#"
                                @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeEarningsMenu = 'transactions'; window.dispatchEvent(new CustomEvent('earnings-menu-selected', { detail: { menu: 'transactions' } })); window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'earnings', active_earnings_menu: 'transactions' } }))">View
                                All</a>
                        </div>

                        <div class="earnings-recent-list">
                            @forelse ($this->recentBookings as $booking)
                                <div class="earnings-recent-item">
                                    <div class="earnings-recent-avatar-wrap">
                                        @if (filled($booking['photo']))
                                            <img class="earnings-recent-avatar" src="{{ $booking['photo'] }}"
                                                alt="{{ $booking['display_name'] }}"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                            <span
                                                class="earnings-recent-avatar-initials is-hidden">{{ $booking['initial'] }}</span>
                                        @else
                                            <span class="earnings-recent-avatar-initials">{{ $booking['initial'] }}</span>
                                        @endif
                                    </div>
                                    <div class="earnings-recent-meta">
                                        <p class="earnings-recent-name">{{ $booking['display_name'] }}</p>
                                        <p class="earnings-recent-time">{{ $booking['relative_time'] }}</p>
                                    </div>
                                    @unless ($isSpaceAccount)
                                        <span
                                            class="earnings-client-badge is-{{ $booking['client_type'] }}">{{ $booking['client_type_label'] }}</span>
                                    @endunless
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
    </div>

</div>
