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

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = $email !== '' ? GroomerSpacerProfile::where('email', $email)->first() : null;
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

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = $email !== '' ? GroomerSpacerProfile::where('email', $email)->first() : null;
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
                $rangeText = $fromText && $toText ? $fromText . ' — ' . $toText : ($fromText ?: $toText);
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
        <div class="ma-holiday-list__col ma-holiday-list__col--range">Holiday / Time Off</div>
        <div class="ma-holiday-list__col ma-holiday-list__col--reason">Reason (Optional)</div>
        <div class="ma-holiday-list__col ma-holiday-list__col--edit">Edit</div>
    </div>
    <div class="ma-holiday-list__body" wire:loading.class="is-loading">
        @forelse ($rows as $index => $row)
            <div class="ma-holiday-list__row"
                wire:key="holiday-{{ $index }}-{{ $row['from'] }}-{{ $row['to'] }}">
                <div class="ma-holiday-list__col ma-holiday-list__range">
                    <span class="ma-holiday-list__dates">{{ $row['rangeText'] }}</span>
                    @if ($row['days'] > 0)
                        <span class="ma-holiday-list__days">({{ $row['days'] }}
                            day{{ $row['days'] === 1 ? '' : 's' }})</span>
                    @endif
                </div>
                <div class="ma-holiday-list__col ma-holiday-list__reason" style="font-weight: 400;">
                    {{ $row['reason'] ?: '—' }}</div>
                <div class="ma-holiday-list__col ma-holiday-list__actions">
                    <button type="button" class="ma-holiday-list__delete"
                        wire:click="deleteHoliday({{ $index }})" wire:loading.attr="disabled"
                        wire:target="deleteHoliday({{ $index }})" aria-label="Delete holiday">
                        <span class="ma-holiday-list__delete-icon" wire:loading.remove
                            wire:target="deleteHoliday({{ $index }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="15" viewBox="0 0 13 15"
                                fill="none">
                                <path
                                    d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                    fill="#3B3731" />
                            </svg>
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
