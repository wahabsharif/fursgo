<?php

use App\Models\Booking;
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

        $booking = Booking::with(['petOwner:id,name,profile_image,created_at', 'pets:id,name,pet_type,breed,sex,weight,notes,photo'])
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

    private function formatSpaceServiceLabel(?string $service, mixed $time = null): string
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

    private function clientSinceLabel(Booking $booking): string
    {
        $firstBookingAt = Booking::query()
            ->where('goormer_spacer_id', Auth::guard('groomer_spacer')->id() ?? Auth::id())
            ->where('pet_owner_id', $booking->pet_owner_id)
            ->min('created_at');

        if ($firstBookingAt) {
            return Carbon::parse($firstBookingAt)->format('d M Y');
        }

        return (string) optional($booking->petOwner?->created_at)->format('d M Y');
    }

    private function toDetails(Booking $booking): array
    {
        $isSpace = $this->isSpaceUser();
        $bookingIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $status = strtolower((string) ($booking->booking_status ?? ''));
        $statusLabel = $status !== '' ? ucfirst($status) : 'N/A';

        $owner = $booking->petOwner;
        $ownerName = $owner->name ?? 'N/A';
        $ownerImageRaw = (string) ($owner->profile_image ?? '');
        $ownerImageUrl = $ownerImageRaw !== '' ? asset('storage/' . ltrim($ownerImageRaw, '/')) : '';

        $pet = $booking->pets->first();
        $petName = $pet->name ?? 'N/A';
        $petType = (string) ($pet->pet_type ?? '');
        $petBreed = (string) ($pet->breed ?? '');
        $petSexRaw = strtolower(trim((string) ($pet->sex ?? '')));
        $petSex = $petSexRaw !== '' ? ucfirst($petSexRaw) : 'N/A';
        $petWeight = $pet && $pet->weight !== null ? rtrim(rtrim(number_format((float) $pet->weight, 2, '.', ''), '0'), '.') . ' kg' : 'N/A';
        $petNotes = trim((string) ($pet->notes ?? ''));
        $petPhotoRaw = (string) ($pet->photo ?? '');
        $petPhotoUrl = $petPhotoRaw !== '' ? asset('storage/' . ltrim($petPhotoRaw, '/')) : '';

        $petSummaryParts = array_values(array_filter([$petName, $petType, $petBreed !== '' ? $petBreed : null]));
        $petSummary = $petSummaryParts !== [] ? implode(' · ', $petSummaryParts) : 'N/A';
        $petTypeBreed = trim(implode(' • ', array_filter([$petType, $petBreed])));

        $serviceLabel = $isSpace ? $this->formatSpaceServiceLabel($booking->service, $booking->time) : ($booking->service ?: 'N/A');
        $dateLabel = optional($booking->date)->format('l, jS F d/m/Y') ?? 'N/A';
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

        $serviceFee = (float) ($booking->amount ?? 0);
        $addOns = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(fn($item) => (float) data_get($item, 'amount', 0))
            ->sum();
        $discount = (float) ($booking->discount ?? 0);
        $total = $serviceFee + $addOns - $discount;

        $statusClass = match ($status) {
            'pending' => 'is-pending',
            'confirmed' => 'is-confirmed',
            'completed' => 'is-completed',
            'cancelled' => 'is-cancelled',
            default => 'is-default',
        };

        return [
            'id' => (int) $booking->id,
            'idLabel' => $bookingIdLabel,
            'ownerName' => $ownerName,
            'ownerInitial' => strtoupper(substr($ownerName, 0, 1)),
            'ownerImageUrl' => $ownerImageUrl,
            'petSummary' => $petSummary,
            'clientSince' => $isSpace ? $this->clientSinceLabel($booking) : '',
            'isSpace' => $isSpace,
            'status' => $status,
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'serviceLabel' => $serviceLabel,
            'dateLabel' => $dateLabel,
            'timeLabel' => $timeLabel,
            'serviceFee' => number_format($serviceFee, 2),
            'addOns' => number_format($addOns, 2),
            'total' => number_format($total, 2),
            'petName' => $petName,
            'petInitial' => strtoupper(substr($petName, 0, 1)),
            'petPhotoUrl' => $petPhotoUrl,
            'petTypeBreed' => $petTypeBreed,
            'petSex' => $petSex,
            'petWeight' => $petWeight,
            'petNotes' => $petNotes,
        ];
    }
}; ?>

<div x-data="bookingDetailsDrawer" @booking-details-open.window="openWith($event.detail)"
    @keydown.escape.window="if (open) closeDrawer()">
    <x-business-hub.common.booking-details-drawer />
</div>

@script
    <script>
        Alpine.data('bookingDetailsDrawer', () => ({
            open: false,
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

                return {
                    id,
                    idLabel: row.idLabel || ('FG-' + String(id).padStart(5, '0')),
                    ownerName,
                    ownerInitial: ownerName.charAt(0).toUpperCase(),
                    ownerImageUrl: row.ownerImageUrl || '',
                    petSummary: [petName, petType].filter(Boolean).join(' · ') || 'N/A',
                    clientSince: row.clientSince || '',
                    isSpace: this.isSpace,
                    status,
                    statusLabel: row.statusLabel || (status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A'),
                    statusClass,
                    serviceLabel: row.service || 'N/A',
                    dateLabel: row.date || 'N/A',
                    timeLabel: row.time || 'N/A',
                    serviceFee: amount,
                    addOns: '0.00',
                    total: amount,
                    petName,
                    petInitial: petName.charAt(0).toUpperCase(),
                    petPhotoUrl: row.petPhotoUrl || '',
                    petTypeBreed: petType,
                    petSex: 'N/A',
                    petWeight: 'N/A',
                    petNotes: '',
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
                    this.details = full;
                    if (typeof full.isSpace === 'boolean') {
                        this.isSpace = full.isSpace;
                    }
                }).catch(() => {});
            },
            closeDrawer() {
                if (!this.open) {
                    return;
                }
                this.open = false;
                this.requestId += 1;
                const layer = document.querySelector('.booking-details-drawer-layer');
                if (layer) {
                    layer.style.display = 'none';
                    layer.classList.remove('is-open');
                }
                if (window.__unlockBookingDetailsDrawer) window.__unlockBookingDetailsDrawer();
            },
        }));
    </script>
@endscript
