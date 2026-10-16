<?php

use App\Models\Booking;
use App\Support\BusinessHubAvatar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function load($bookingId = null): ?array
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? ($bookingId['id'] ?? null);
        }

        $bookingId = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : 0;
        $this->skipRender();

        if ($bookingId < 1) {
            return null;
        }

        $booking = Booking::with(['petOwner:id,name,profile_image,created_at', 'pets:id,name,pet_type,breed,sex,weight,notes,photo,address'])
            ->where('goormer_spacer_id', Auth::guard('groomer_spacer')->id() ?? Auth::id())
            ->where('id', $bookingId)
            ->first();

        return $booking ? $this->toDetails($booking) : null;
    }

    public function close(): void
    {
        $this->skipRender();
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

    private function spacerId(): int
    {
        return (int) (Auth::guard('groomer_spacer')->id() ?? (Auth::id() ?? 0));
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

    private function cancelBannerCopy(Booking $booking, string $ownerName): array
    {
        $cancelledByRaw = strtolower(trim((string) ($booking->cancelled_by ?? '')));
        $cancelledByProvider = str_contains($cancelledByRaw, 'groomer') || str_contains($cancelledByRaw, 'space') || str_contains($cancelledByRaw, 'host') || $cancelledByRaw === 'you';

        $cancelDate = optional($booking->updated_at)->format('j M Y') ?? '';
        $title = ($cancelledByProvider ? 'You' : $ownerName) . ' cancelled this booking' . ($cancelDate !== '' ? ' · ' . $cancelDate : '');

        $reason = trim((string) ($booking->cancellation_reason ?? ''));

        if ($cancelledByProvider) {
            $subtitle = $reason !== '' ? $reason : 'Customer did not show up — you will be paid in full.';

            return ['title' => $title, 'subtitle' => $subtitle];
        }

        if ($reason !== '' && preg_match('/(free-cancellation|24h window|fee applies|days before|hours before)/i', $reason)) {
            return ['title' => $title, 'subtitle' => $reason];
        }

        $subtitle = $this->ownerCancelSubtitle($booking);

        return ['title' => $title, 'subtitle' => $subtitle];
    }

    private function ownerCancelSubtitle(Booking $booking): string
    {
        $cancelledAt = $booking->updated_at?->copy() ?? now();
        $appointmentAt = $booking->date ? Carbon::parse($booking->date)->startOfDay() : null;

        if ($appointmentAt) {
            $timeRaw = trim((string) ($booking->time ?? ''));
            if (preg_match('/(\d{1,2}):(\d{2})/', $timeRaw, $m)) {
                $appointmentAt->setTime((int) $m[1], (int) $m[2]);
            }
        }

        if (!$appointmentAt) {
            return 'Cancelled within the free-cancellation window.';
        }

        $hoursBefore = (int) $cancelledAt->diffInHours($appointmentAt, false);

        if ($hoursBefore >= 24) {
            $days = (int) max(1, (int) floor($hoursBefore / 24));
            $dayLabel = $days === 1 ? '1 day' : $days . ' days';

            return 'Cancelled ' . $dayLabel . ' before — within the free-cancellation window.';
        }

        if ($hoursBefore > 0) {
            $hourLabel = $hoursBefore === 1 ? '1 hour' : $hoursBefore . ' hours';

            return 'Cancelled ' . $hourLabel . ' before — inside the 24h window, so a fee applies.';
        }

        return 'Cancelled within the free-cancellation window.';
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

    private function spaceServiceMinutes(mixed $time = null): ?int
    {
        $timeRaw = is_object($time) && method_exists($time, 'format') ? $time->format('H:i') : trim((string) ($time ?? ''));
        if (!str_contains($timeRaw, '-')) {
            return null;
        }

        $rangeParts = preg_split('/\s*-\s*/', $timeRaw, 2);
        preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
        preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
        if (empty($startMatch[1]) || empty($endMatch[1])) {
            return null;
        }

        $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
        if ($diff < 0) {
            $diff += 24 * 60;
        }

        return $diff;
    }

    private function spaceServiceIconUrl(string $serviceKind): string
    {
        $kind = strtolower(trim($serviceKind));
        $file = match (true) {
            str_contains($kind, 'full') => 'icon-space-service-8h.svg',
            str_contains($kind, 'half') => 'icon-space-service-4h.svg',
            default => 'icon-space-service-1h.svg',
        };

        return asset('images/business-hub/' . $file);
    }

    private function formatSpaceServiceLabel(?string $service, mixed $time = null, bool $withDuration = false): string
    {
        $serviceLower = strtolower(trim((string) $service));
        $minutes = $this->spaceServiceMinutes($time);
        $label = match (true) {
            (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-Day',
            (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-Day',
            str_contains($serviceLower, 'hour') => 'Hourly',
            default => null,
        };

        if ($label === null && $minutes !== null) {
            $label = $minutes >= 7 * 60 ? 'Full-Day' : ($minutes >= 3 * 60 ? 'Half-Day' : 'Hourly');
        }

        if ($label === null) {
            $plain = trim((string) $service);

            return $plain !== '' ? $plain : 'N/A';
        }

        if (!$withDuration) {
            return $label;
        }

        $durationMinutes = $minutes;
        if ($durationMinutes === null) {
            $durationMinutes = match ($label) {
                'Full-Day' => 8 * 60,
                'Half-Day' => 4 * 60,
                default => null,
            };
        }

        if ($durationMinutes === null || $label === 'Hourly') {
            return $label;
        }

        $hours = max(1, (int) round($durationMinutes / 60));
        $hourLabel = $hours === 1 ? '1 hour' : $hours . ' hours';

        return $label . ' (' . $hourLabel . ')';
    }

    private function locationLabel(?string $visitType): string
    {
        $visitRaw = str_replace('_', ' ', strtolower(trim((string) $visitType)));

        return match (true) {
            str_contains($visitRaw, 'home') => "At customer's home",
            str_contains($visitRaw, 'salon'), str_contains($visitRaw, 'studio') => 'At studio',
            str_contains($visitRaw, 'mobile') => 'Mobile station',
            str_contains($visitRaw, 'garden'), str_contains($visitRaw, 'shed') => 'Garden / Shed',
            str_contains($visitRaw, 'private') => 'Private room',
            $visitRaw !== '' => ucwords($visitRaw),
            default => 'N/A',
        };
    }

    private function directionsAddress(Booking $booking): string
    {
        $visitRaw = str_replace('_', ' ', strtolower(trim((string) ($booking->visit_type ?? ''))));
        $petAddress = trim((string) ($booking->pets->first()?->address ?? ''));
        $isHome = str_contains($visitRaw, 'home');

        if ($isHome && $petAddress !== '') {
            return $petAddress;
        }

        $issuer = Auth::guard('groomer_spacer')->user() ?? Auth::user();
        $providerAddress = trim((string) data_get($issuer?->spacer_business_profile ?? [], 'legacy.location', ''));
        if ($providerAddress === '') {
            $providerAddress = trim((string) data_get($issuer?->freelance_details ?? [], 'service_home_address_line1', ''));
        }
        if ($providerAddress === '') {
            $providerAddress = trim((string) data_get($issuer?->freelance_details ?? [], 'service_home_address_line2', ''));
        }
        if ($providerAddress === '' && $issuer) {
            $area = \App\Models\ServiceArea::query()->where('groomer_spacer_id', $issuer->id)->first();
            $providerAddress = trim((string) ($area?->address ?? ($area?->name ?? '')));
        }

        if ($providerAddress !== '') {
            return $providerAddress;
        }

        if ($petAddress !== '') {
            return $petAddress;
        }

        return $this->locationLabel($booking->visit_type ?? null);
    }

    private function expiresLabel(Booking $booking): string
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

    private function toDetails(Booking $booking): array
    {
        $isSpace = $this->isSpaceUser();
        $spacerId = $this->spacerId();
        $bookingIdLabel = ($isSpace ? 'SH-' : 'FG-') . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $status = strtolower((string) ($booking->booking_status ?? ''));
        $statusLabel = $status !== '' ? ucfirst($status) : 'N/A';
        $statusClass = match ($status) {
            'pending' => 'is-pending',
            'confirmed' => 'is-confirmed',
            'completed' => 'is-completed',
            'cancelled' => 'is-cancelled',
            default => 'is-default',
        };

        $owner = $booking->petOwner;
        $ownerFullName = trim((string) ($owner?->name ?? 'N/A')) ?: 'N/A';
        $ownerName = $this->ownerShortName($owner);
        $ownerImageUrl = (string) (BusinessHubAvatar::mediaUrl($owner?->profile_image ?? null) ?? '');

        $completedCount = (int) Booking::query()->where('goormer_spacer_id', $spacerId)->where('pet_owner_id', $booking->pet_owner_id)->where('booking_status', 'completed')->count();
        $previousCompleted = max(0, $completedCount - ($status === 'completed' ? 1 : 0));
        $tier = $this->clientTierLabel($previousCompleted);

        $lastVisit = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->where('pet_owner_id', $booking->pet_owner_id)
            ->where('booking_status', 'completed')
            ->when($status === 'completed', fn($q) => $q->where('id', '!=', $booking->id))
            ->orderByDesc('date')
            ->value('date');
        $lastVisitLabel = $lastVisit ? Carbon::parse($lastVisit)->format('j M') : null;
        $bookingsCountLabel = $previousCompleted === 1 ? '1 booking' : $previousCompleted . ' bookings';
        $ownerMeta = $lastVisitLabel ? $bookingsCountLabel . ' · last visit ' . $lastVisitLabel : $bookingsCountLabel;

        $serviceKind = $isSpace ? $this->formatSpaceServiceLabel($booking->service, $booking->time) : ((string) ($booking->service ?: 'N/A'));
        $serviceLabel = $isSpace ? $this->formatSpaceServiceLabel($booking->service, $booking->time, true) : $serviceKind;
        $serviceIconUrl = $isSpace ? $this->spaceServiceIconUrl($serviceKind) : asset('images/business-hub/icon-confirmed-service.svg');
        $dateLabel = optional($booking->date)->format('D, j M Y') ?? 'N/A';
        $timeRaw = trim((string) ($booking->time ?? ''));
        $timeLabel = $timeRaw !== '' ? $timeRaw : 'N/A';
        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[0] ?? ''), $mStart);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[1] ?? ''), $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                $timeLabel = $mStart[1] . ' - ' . $mEnd[1];
            }
        }

        $pets = $booking->pets
            ->map(function ($pet) {
                $name = trim((string) ($pet->name ?? 'N/A')) ?: 'N/A';
                $breed = trim((string) ($pet->breed ?? ''));
                $type = trim((string) ($pet->pet_type ?? ''));
                $breedOrType = $breed !== '' ? $breed : ($type !== '' ? $type : '');
                $sexRaw = strtolower(trim((string) ($pet->sex ?? '')));
                $weight = $pet->weight !== null ? rtrim(rtrim(number_format((float) $pet->weight, 2, '.', ''), '0'), '.') . ' kg' : '';

                return [
                    'name' => $name,
                    'breed' => $breedOrType,
                    'title' => $breedOrType !== '' ? $name . ' · ' . $breedOrType : $name,
                    'photoUrl' => (string) (BusinessHubAvatar::mediaUrl($pet->photo ?? null) ?? ''),
                    'initial' => BusinessHubAvatar::initials($name),
                    'imageFailed' => false,
                    'sex' => $sexRaw !== '' ? ucfirst($sexRaw) : '',
                    'weight' => $weight,
                    'notes' => trim((string) ($pet->notes ?? '')),
                ];
            })
            ->values()
            ->all();

        $serviceFee = (float) ($booking->amount ?? 0);
        $addOnRows = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(function ($item) {
                $label = trim((string) data_get($item, 'label', data_get($item, 'name', '')));
                $amount = (float) data_get($item, 'amount', 0);

                return $label !== '' ? ['label' => $label, 'amount' => number_format($amount, 2), 'raw' => $amount] : null;
            })
            ->filter()
            ->values();
        $addOnsTotal = (float) $addOnRows->sum('raw');
        $discount = (float) ($booking->discount ?? 0);
        $subtotal = max(0, $serviceFee + $addOnsTotal - $discount);
        $platformPercent = (float) config('services.fursgo.platform_fee_percent', 5);
        $earnRaw = round($subtotal * (1 - $platformPercent / 100), 2);

        $lineItems = [['label' => $serviceLabel, 'amount' => number_format($serviceFee, 2)], ...$addOnRows->map(fn($row) => ['label' => $row['label'], 'amount' => $row['amount']])->all()];
        if ($discount > 0) {
            $lineItems[] = ['label' => 'Discount', 'amount' => '-' . number_format($discount, 2)];
        }

        $statusDate = optional($booking->updated_at ?? $booking->date)->format('j M') ?? '';
        $requestedDate = optional($booking->created_at)->format('j M') ?? $statusDate;
        $expires = $status === 'pending' ? $this->expiresLabel($booking) : '';

        $headerMeta = match ($status) {
            'pending' => 'Requested ' . $requestedDate,
            'confirmed' => 'Booking ' . $bookingIdLabel . ' · Confirmed ' . $statusDate,
            'completed' => 'Booking ' . $bookingIdLabel . ' · Completed ' . $statusDate,
            'cancelled' => 'Booking ' . $bookingIdLabel . ' · Cancelled ' . $statusDate,
            default => 'Booking ' . $bookingIdLabel,
        };

        $earnLabel = in_array($status, ['completed', 'cancelled'], true) ? 'Your payout' : "You'll earn";
        $earnAmount = $status === 'cancelled' ? '0.00' : number_format($earnRaw, 2);
        $refundedAmount = $status === 'cancelled' ? number_format((float) ($booking->refund_amount ?? $subtotal), 2) : null;

        $cancelBanner = $status === 'cancelled' ? $this->cancelBannerCopy($booking, $ownerName) : null;

        return [
            'id' => (int) $booking->id,
            'idLabel' => $bookingIdLabel,
            'isSpace' => $isSpace,
            'status' => $status,
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'autoAccepted' => $status === 'confirmed' && (bool) ($booking->auto_accepted ?? false),
            'headerMeta' => $headerMeta,
            'expiresLabel' => $expires,
            'ownerName' => $ownerName,
            'ownerFullName' => $ownerFullName,
            'ownerInitial' => BusinessHubAvatar::initials($ownerFullName),
            'ownerImageUrl' => $ownerImageUrl,
            'ownerImageFailed' => false,
            'clientTier' => $tier['label'],
            'clientTierTone' => $tier['tone'],
            'ownerMeta' => $ownerMeta,
            'serviceLabel' => $serviceLabel,
            'serviceIconUrl' => $serviceIconUrl,
            'dateLabel' => $dateLabel,
            'timeLabel' => $timeLabel,
            'locationLabel' => $this->locationLabel($booking->visit_type ?? null),
            'directionsAddress' => $this->directionsAddress($booking),
            'directionsMeta' => '',
            'pets' => $pets,
            'lineItems' => $lineItems,
            'subtotalLabel' => $serviceKind . ' + add-ons',
            'subtotal' => number_format($subtotal, 2),
            'earnLabel' => $earnLabel,
            'earnAmount' => $earnAmount,
            'refundedAmount' => $refundedAmount,
            'cancelBanner' => $cancelBanner,
            'invoiceUrl' => url('/business-hub/bookings/' . $booking->id . '/invoice.pdf'),
        ];
    }
}; ?>

<div x-data="bookingDetailsDrawer" @booking-details-open.window="openWith($event.detail)"
    @keydown.escape.window="if (directionsOpen) { closeDirections(); } else if (open) { closeDrawer(); }">
    <x-business-hub.common.booking-details-drawer />
</div>

@script
    <script>
        Alpine.data('bookingDetailsDrawer', () => ({
            open: false,
            directionsOpen: false,
            topOffset: 0,
            requestId: 0,
            isSpace: {{ $this->isSpaceUser() ? 'true' : 'false' }},
            details: {},
            init() {
                this._onResize = () => this.syncTopOffset();
                window.addEventListener('resize', this._onResize);
            },
            destroy() {
                window.removeEventListener('resize', this._onResize);
            },
            emptyDetails() {
                return {
                    id: 0,
                    idLabel: '',
                    isSpace: this.isSpace,
                    status: '',
                    statusLabel: '',
                    statusClass: 'is-default',
                    autoAccepted: false,
                    headerMeta: '',
                    expiresLabel: '',
                    ownerName: '',
                    ownerFullName: '',
                    ownerInitial: '??',
                    ownerImageUrl: '',
                    ownerImageFailed: false,
                    clientTier: '',
                    clientTierTone: 'new',
                    ownerMeta: '',
                    serviceLabel: '',
                    serviceIconUrl: this.isSpace ? @js(asset('images/business-hub/icon-space-service-1h.svg')) : @js(asset('images/business-hub/icon-confirmed-service.svg')),
                    dateLabel: '',
                    timeLabel: '',
                    locationLabel: '',
                    directionsAddress: '',
                    directionsMeta: '',
                    pets: [],
                    lineItems: [],
                    subtotalLabel: '',
                    subtotal: '0.00',
                    earnLabel: "You'll earn",
                    earnAmount: '0.00',
                    refundedAmount: null,
                    cancelBanner: null,
                    invoiceUrl: '',
                };
            },
            fromRow(row) {
                row = row || {};
                const id = Number(row.id || 0);
                const ownerName = row.owner || row.ownerName || 'N/A';
                const petName = row.petName || 'N/A';
                const petType = row.petType || '';
                const status = String(row.status || '').toLowerCase();
                const statusClass = {
                    pending: 'is-pending',
                    confirmed: 'is-confirmed',
                    completed: 'is-completed',
                    cancelled: 'is-cancelled',
                } [status] || 'is-default';
                const amount = row.amount || '0.00';
                const serviceLabel = row.service || 'N/A';
                const serviceLower = String(serviceLabel).toLowerCase();
                const spaceServiceIcon = serviceLower.includes('full') ?
                    @js(asset('images/business-hub/icon-space-service-8h.svg')) :
                    (serviceLower.includes('half') ?
                        @js(asset('images/business-hub/icon-space-service-4h.svg')) :
                        @js(asset('images/business-hub/icon-space-service-1h.svg')));
                const initialsFrom = (value) => {
                    const parts = String(value || '').trim().split(/\s+/).filter(Boolean);
                    if (!parts.length) return '??';
                    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
                    return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
                };

                return {
                    ...this.emptyDetails(),
                    id,
                    idLabel: row.idLabel || ((this.isSpace ? 'SH-' : 'FG-') + String(id).padStart(5, '0')),
                    ownerName,
                    ownerFullName: row.ownerFull || ownerName,
                    ownerInitial: initialsFrom(ownerName),
                    ownerImageUrl: row.ownerImageUrl || '',
                    clientTier: row.clientTier || '',
                    clientTierTone: row.clientTierTone || 'new',
                    status,
                    statusLabel: row.statusLabel || (status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A'),
                    statusClass,
                    autoAccepted: status === 'confirmed' && !!row.autoAccepted,
                    headerMeta: status === 'pending' ?
                        ('Requested ' + (row.date || '')) : ('Booking ' + (row.idLabel || '') + (status ? ' · ' + status.charAt(0).toUpperCase() + status.slice(1) : '')),
                    expiresLabel: status === 'pending' && row.timerLabel ? ('Expires in ' + row.timerLabel) : '',
                    serviceLabel,
                    serviceIconUrl: this.isSpace ? spaceServiceIcon : @js(asset('images/business-hub/icon-confirmed-service.svg')),
                    dateLabel: row.date || 'N/A',
                    timeLabel: row.time || row.startTime || 'N/A',
                    locationLabel: row.visitType || 'N/A',
                    pets: [{
                        name: petName,
                        breed: petType,
                        title: [petName, petType].filter(Boolean).join(' · ') || 'N/A',
                        photoUrl: row.petPhoto || row.petPhotoUrl || '',
                        initial: initialsFrom(petName),
                        imageFailed: false,
                        sex: '',
                        weight: '',
                        notes: '',
                    }],
                    lineItems: [{
                        label: row.service || 'Service',
                        amount
                    }],
                    subtotalLabel: (row.service || 'Service') + ' + add-ons',
                    subtotal: amount,
                    earnLabel: ['completed', 'cancelled'].includes(status) ? 'Your payout' : "You'll earn",
                    earnAmount: status === 'cancelled' ? '0.00' : amount,
                    invoiceUrl: id ? ('/business-hub/bookings/' + id + '/invoice.pdf') : '',
                };
            },
            syncTopOffset() {
                const curve = document.querySelector('.dashboard-header .curve-shape-container');
                this.topOffset = curve ? Math.max(0, Math.round(curve.getBoundingClientRect().bottom)) : 0;
            },
            openWith(row) {
                this.open = true;
                this.details = this.fromRow(row);
                this.syncTopOffset();
                const layer = document.querySelector('.booking-details-drawer-layer');
                if (layer) {
                    layer.style.display = 'block';
                    layer.classList.add('is-open');
                    layer.classList.toggle('is-space', this.isSpace);
                    layer.classList.remove('is-pending', 'is-confirmed', 'is-completed', 'is-cancelled');
                    if (this.details.statusClass) {
                        layer.classList.add(this.details.statusClass);
                    }
                }
                if (window.__lockBookingDetailsDrawer) window.__lockBookingDetailsDrawer();
                const req = ++this.requestId;
                const id = Number(row?.id || 0);
                if (!id || !this.$wire?.load) {
                    return;
                }
                this.$wire.load(id).then((full) => {
                    if (req !== this.requestId || !this.open || !full) {
                        return;
                    }
                    this.details = {
                        ...this.emptyDetails(),
                        ...full,
                        ownerImageFailed: false,
                        pets: Array.isArray(full.pets) ?
                            full.pets.map((pet) => ({
                                ...pet,
                                imageFailed: false
                            })) : [],
                    };
                    if (typeof full.isSpace === 'boolean') {
                        this.isSpace = full.isSpace;
                    }
                    if (layer) {
                        layer.classList.toggle('is-space', this.isSpace);
                        layer.classList.remove('is-pending', 'is-confirmed', 'is-completed', 'is-cancelled');
                        if (full.statusClass) {
                            layer.classList.add(full.statusClass);
                        }
                    }
                }).catch(() => {});
            },
            closeDrawer() {
                if (!this.open) {
                    return;
                }
                this.directionsOpen = false;
                this.open = false;
                this.requestId += 1;
                const layer = document.querySelector('.booking-details-drawer-layer');
                if (layer) {
                    layer.style.display = 'none';
                    layer.classList.remove('is-open');
                }
                if (window.__unlockBookingDetailsDrawer) window.__unlockBookingDetailsDrawer();
            },
            openDirections() {
                this.directionsOpen = true;
            },
            closeDirections() {
                this.directionsOpen = false;
            },
            openInMaps() {
                const q = encodeURIComponent(this.details.directionsAddress || this.details.locationLabel || 'pet grooming');
                window.open('https://www.google.com/maps/search/?api=1&query=' + q, '_blank', 'noopener');
            },
            runAction(action) {
                const id = Number(this.details?.id || 0);
                if (!id) {
                    return;
                }
                if (action === 'download') {
                    if (typeof window.downloadBookingInvoicePdf === 'function' && this.details.invoiceUrl) {
                        window.downloadBookingInvoicePdf(this.details.invoiceUrl);
                    }
                    return;
                }
                if (action === 'directions') {
                    this.openDirections();
                    return;
                }
                this.closeDrawer();
                if (action === 'accept' && window.Livewire) {
                    window.Livewire.dispatch('booking-drawer-accept', {
                        bookingId: id
                    });
                }
                if (action === 'decline' && window.Livewire) {
                    window.Livewire.dispatch('booking-drawer-decline', {
                        bookingId: id
                    });
                }
                if (action === 'reschedule' && window.Livewire) {
                    window.Livewire.dispatch('booking-drawer-reschedule', {
                        bookingId: id
                    });
                }
                if (action === 'cancel' && window.Livewire) {
                    window.Livewire.dispatch('booking-drawer-decline', {
                        bookingId: id
                    });
                }
            },
        }));
    </script>
@endscript
