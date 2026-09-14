<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function load($bookingId = null): ?array
    {
        if (is_array($bookingId)) {
            $bookingId = $bookingId['bookingId'] ?? $bookingId['id'] ?? null;
        }

        $bookingId = $bookingId !== null && $bookingId !== '' ? (int) $bookingId : 0;
        $this->skipRender();

        if ($bookingId < 1) {
            return null;
        }

        $booking = Booking::with([
            'petOwner:id,name,profile_image',
            'pets:id,name,pet_type,breed,sex,weight,notes,photo',
        ])
            ->where('goormer_spacer_id', Auth::guard('groomer_spacer')->id() ?? Auth::id())
            ->where('id', $bookingId)
            ->first();

        return $booking ? $this->toDetails($booking) : null;
    }

    public function close(): void
    {
        $this->skipRender();
    }

    private function toDetails(Booking $booking): array
    {
        $bookingIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $status = strtolower((string) ($booking->booking_status ?? ''));
        $statusLabel = $status !== '' ? ucfirst($status) : 'N/A';

        $owner = $booking->petOwner;
        $ownerName = $owner->name ?? 'N/A';
        $ownerImageRaw = (string) ($owner->profile_image ?? '');
        $ownerImageUrl = $ownerImageRaw !== ''
            ? asset('storage/' . ltrim($ownerImageRaw, '/'))
            : '';

        $pet = $booking->pets->first();
        $petName = $pet->name ?? 'N/A';
        $petType = (string) ($pet->pet_type ?? '');
        $petBreed = (string) ($pet->breed ?? '');
        $petSexRaw = strtolower(trim((string) ($pet->sex ?? '')));
        $petSex = $petSexRaw !== '' ? ucfirst($petSexRaw) : 'N/A';
        $petWeight = $pet && $pet->weight !== null
            ? rtrim(rtrim(number_format((float) $pet->weight, 2, '.', ''), '0'), '.') . ' kg'
            : 'N/A';
        $petNotes = trim((string) ($pet->notes ?? ''));
        $petPhotoRaw = (string) ($pet->photo ?? '');
        $petPhotoUrl = $petPhotoRaw !== ''
            ? asset('storage/' . ltrim($petPhotoRaw, '/'))
            : '';

        $petSummaryParts = array_values(array_filter([$petName, $petType, $petBreed !== '' ? $petBreed : null]));
        $petSummary = $petSummaryParts !== [] ? implode(' · ', $petSummaryParts) : 'N/A';
        $petTypeBreed = trim(implode(' • ', array_filter([$petType, $petBreed])));

        $serviceLabel = $booking->service ?: 'N/A';
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
            }[status] || 'is-default';
            const amount = row.amount || '0.00';

            return {
                id,
                idLabel: row.idLabel || ('FG-' + String(id).padStart(5, '0')),
                ownerName,
                ownerInitial: ownerName.charAt(0).toUpperCase(),
                ownerImageUrl: row.ownerImageUrl || '',
                petSummary: [petName, petType].filter(Boolean).join(' · ') || 'N/A',
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
            }).catch(() => { });
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