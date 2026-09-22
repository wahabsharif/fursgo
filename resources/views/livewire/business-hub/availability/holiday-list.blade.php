<?php

use App\Models\GroomerSpacerProfile;
use App\Models\Staff;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $staffId = null;
    public array $holidays = [];

    public function mount(?int $staffId = null): void
    {
        $this->staffId = $staffId ? (int) $staffId : null;
        $this->loadHolidays();
    }

    #[On('active-staff-changed')]
    public function setActiveStaff($staffId = null): void
    {
        $this->staffId = $staffId ? (int) $staffId : null;
        $this->loadHolidays();
    }

    #[On('staff-holiday-saved')]
    public function onSaved($staffId = null, $saved = false): void
    {
        if (!$saved) {
            return;
        }
        if ((int) $staffId !== (int) $this->staffId) {
            return;
        }
        $this->loadHolidays();
    }

    #[On('staff-holiday-deleted')]
    public function onDeleted($staffId = null): void
    {
        if ((int) $staffId !== (int) $this->staffId) {
            return;
        }
        $this->loadHolidays();
    }

    public function deleteHoliday(int $index): void
    {
        if (!$this->staffId || $index < 0) {
            return;
        }

        $staff = Staff::find($this->staffId);
        if (!$staff) {
            return;
        }

        $profile = auth('groomer_spacer')->user();
        if (!$profile instanceof GroomerSpacerProfile) {
            $email = (string) data_get(auth()->user(), 'email', '');
            $profile = $email !== '' ? GroomerSpacerProfile::where('email', $email)->first() : null;
        }
        if (!$profile || (int) $staff->goormer_spacer_profile_id !== (int) $profile->id) {
            return;
        }

        $entries = is_array($staff->holiday_time_off) ? array_values($staff->holiday_time_off) : [];
        if (!array_key_exists($index, $entries)) {
            return;
        }

        array_splice($entries, $index, 1);
        $staff->holiday_time_off = $entries;
        $staff->save();

        $this->loadHolidays();
    }

    private function loadHolidays(): void
    {
        if (!$this->staffId) {
            $this->holidays = [];
            return;
        }

        $staff = Staff::find($this->staffId);
        if (!$staff) {
            $this->holidays = [];
            return;
        }

        $profile = auth('groomer_spacer')->user();
        if (!$profile instanceof GroomerSpacerProfile) {
            $email = (string) data_get(auth()->user(), 'email', '');
            $profile = $email !== '' ? GroomerSpacerProfile::where('email', $email)->first() : null;
        }
        if (!$profile || (int) $staff->goormer_spacer_profile_id !== (int) $profile->id) {
            $this->holidays = [];
            return;
        }

        $raw = is_array($staff->holiday_time_off) ? array_values($staff->holiday_time_off) : [];
        $this->holidays = array_map(
            fn($entry) => [
                'from' => (string) ($entry['from'] ?? ''),
                'to' => (string) ($entry['to'] ?? ''),
                'reason' => (string) ($entry['reason'] ?? ''),
            ],
            $raw,
        );
    }

    private function formatShort(string $iso): string
    {
        if ($iso === '') {
            return '';
        }
        try {
            $d = \DateTimeImmutable::createFromFormat('Y-m-d', $iso);
            return $d ? $d->format('d M Y') : $iso;
        } catch (\Throwable $e) {
            return $iso;
        }
    }

    private function daysBetween(string $from, string $to): int
    {
        if ($from === '' || $to === '') {
            return 0;
        }
        try {
            $f = new \DateTimeImmutable($from);
            $t = new \DateTimeImmutable($to);
            $diff = (int) $f->diff($t)->days;
            return $diff + 1;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function with(): array
    {
        return [
            'rows' => array_map(function ($h) {
                $days = $this->daysBetween($h['from'], $h['to']);
                $fromText = $this->formatShort($h['from']);
                $toText = $this->formatShort($h['to']);
                $isSingleDay = $days <= 1 || $h['from'] === $h['to'];
                $rangeText = $isSingleDay ? ($fromText ?: $toText) : ($fromText && $toText ? $fromText . ' — ' . $toText : ($fromText ?: $toText));
                return [
                    'from' => $h['from'],
                    'to' => $h['to'],
                    'reason' => $h['reason'],
                    'rangeText' => $rangeText,
                    'days' => $days,
                ];
            }, $this->holidays),
        ];
    }
}; ?>

<div class="ma-holiday-list" data-holiday-list>
    <div class="ma-holiday-list__header">
        <div class="ma-holiday-list__col ma-holiday-list__col--range">Scheduled time off</div>
        <div class="ma-holiday-list__col ma-holiday-list__col--reason">Reason</div>
        <div class="ma-holiday-list__col ma-holiday-list__col--edit">Actions</div>
    </div>
    <div class="ma-holiday-list__body" wire:loading.class="is-loading">
        @forelse ($rows as $index => $row)
            <div class="ma-holiday-list__row"
                wire:key="holiday-{{ $index }}-{{ $row['from'] }}-{{ $row['to'] }}">
                <div class="ma-holiday-list__col ma-holiday-list__range">
                    <span class="ma-holiday-list__dates">{{ $row['rangeText'] }}</span>
                    @if ($row['days'] > 0)
                        <span class="ma-holiday-list__days">({{ $row['days'] }} day{{ $row['days'] === 1 ? '' : 's' }})</span>
                    @endif
                </div>
                <div class="ma-holiday-list__col ma-holiday-list__reason">
                    {{ $row['reason'] ?: '—' }}</div>
                <div class="ma-holiday-list__col ma-holiday-list__actions">
                    <button type="button" class="ma-holiday-list__edit" data-holiday-edit
                        data-from="{{ $row['from'] }}" data-to="{{ $row['to'] }}"
                        data-reason="{{ $row['reason'] }}" aria-label="Edit holiday">
                        <img src="{{ asset('images/business-hub/icon-holiday-edit.svg') }}" alt="">
                    </button>
                    <button type="button" class="ma-holiday-list__delete"
                        wire:click="deleteHoliday({{ $index }})" wire:loading.attr="disabled"
                        wire:target="deleteHoliday({{ $index }})" aria-label="Delete holiday">
                        <span class="ma-holiday-list__delete-icon" wire:loading.remove
                            wire:target="deleteHoliday({{ $index }})">
                            <img src="{{ asset('images/business-hub/icon-holiday-delete.svg') }}" alt="">
                        </span>
                        <span class="ma-holiday-list__delete-spinner" wire:loading
                            wire:target="deleteHoliday({{ $index }})" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        @empty
            <div class="ma-holiday-list__empty">No holiday / time off entries yet.</div>
        @endforelse
    </div>
</div>
