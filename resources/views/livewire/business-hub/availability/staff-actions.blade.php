<?php

use App\Models\GroomerSpacerProfile;
use App\Models\Staff;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    private const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    #[On('save-staff-day')]
    public function saveDay($staffId = null, $day = null, $status = null, $start = null, $end = null): void
    {
        $staffId = (int) $staffId;
        $day = (string) $day;
        $start = $start ? (string) $start : '10:00';
        $end = $end ? (string) $end : '18:00';
        $status = (bool) $status;

        if (!$staffId || !in_array($day, self::DAYS, true)) {
            return;
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            return;
        }

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();
        if (!$profile || (int) $staff->goormer_spacer_profile_id !== (int) $profile->id) {
            return;
        }

        $workingHours = is_array($staff->working_hours) ? $staff->working_hours : [];
        $workingHours[$day] = [
            'status' => $status,
            'start' => $start,
            'end' => $end,
        ];

        $staff->working_hours = $workingHours;
        $staff->save();

        $this->dispatch('staff-day-saved', staffId: $staffId, day: $day, status: $status, start: $start, end: $end);
    }

    #[On('save-staff-holiday')]
    public function saveHoliday($staffId = null, $from = null, $to = null, $reason = null): void
    {
        $staffId = (int) $staffId;
        if (!$staffId) {
            return;
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            return;
        }

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();
        if (!$profile || (int) $staff->goormer_spacer_profile_id !== (int) $profile->id) {
            return;
        }

        $from = $from ? (string) $from : '';
        $to = $to ? (string) $to : '';
        $reason = trim((string) ($reason ?? ''));

        if ($from === '' && $to === '' && $reason === '') {
            $this->dispatch('staff-holiday-saved', staffId: $staffId, saved: false);
            return;
        }

        $entries = is_array($staff->holiday_time_off) ? array_values($staff->holiday_time_off) : [];
        $entries[] = [
            'from' => $from,
            'to' => $to,
            'reason' => $reason,
        ];

        $staff->holiday_time_off = $entries;
        $staff->save();

        $this->dispatch('staff-holiday-saved', staffId: $staffId, saved: true);
    }

    #[On('delete-staff-holiday')]
    public function deleteHoliday($staffId = null, $index = null): void
    {
        $staffId = (int) $staffId;
        $index = is_numeric($index) ? (int) $index : -1;
        if (!$staffId || $index < 0) {
            return;
        }

        $staff = Staff::find($staffId);
        if (!$staff) {
            return;
        }

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();
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

        $this->dispatch('staff-holiday-deleted', staffId: $staffId, index: $index);
    }
}; ?>

<div></div>
