@php
    $gsp = null;
    if (auth()->check()) {
        $gsp = \App\Models\GroomerSpacerProfile::where('email', auth()->user()->email)->first();
    }

    $maAccentColor =
        strtolower((string) ($gsp?->user_type ?? (auth()->user()?->user_type ?? ''))) === 'space'
        ? '#FFA899'
        : '#FFC97A';

    $staffMembers = $gsp
        ? \App\Models\Staff::where('goormer_spacer_profile_id', $gsp->id)->orderBy('id')->get()
        : collect();

    $activeStaff = $staffMembers->first();

    $maDayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    $maDefaultWorkingHours = collect($maDayKeys)
        ->mapWithKeys(
            fn($d) => [
                $d => [
                    'status' => in_array($d, ['saturday', 'sunday'], true) ? false : true,
                    'start' => '10:00',
                    'end' => '18:00',
                ],
            ],
        )
        ->all();

    $maNormalizeWorkingHours = static function ($raw) use ($maDayKeys, $maDefaultWorkingHours): array {
        $raw = is_array($raw) ? $raw : [];
        $out = [];
        foreach ($maDayKeys as $key) {
            $entry = $raw[$key] ?? $maDefaultWorkingHours[$key];
            $out[$key] = [
                'status' => (bool) ($entry['status'] ?? false),
                'start' => (string) ($entry['start'] ?? '10:00'),
                'end' => (string) ($entry['end'] ?? '18:00'),
            ];
        }
        return $out;
    };

    $maFirstHoliday = static function ($raw): array {
        if (!is_array($raw) || empty($raw)) {
            return ['from' => '', 'to' => '', 'reason' => ''];
        }
        $first = $raw[0] ?? $raw;
        return [
            'from' => (string) ($first['from'] ?? ''),
            'to' => (string) ($first['to'] ?? ''),
            'reason' => (string) ($first['reason'] ?? ''),
        ];
    };

    $maStaffPayload = static function ($staff) use ($maNormalizeWorkingHours, $maFirstHoliday): array {
        return [
            'workingHours' => $maNormalizeWorkingHours($staff?->working_hours),
            'holiday' => $maFirstHoliday($staff?->holiday_time_off),
            'pauseBooking' => (bool) ($staff?->pause_booking ?? false),
        ];
    };

    $activePayload = $maStaffPayload($activeStaff);
    $activeWorkingHours = $activePayload['workingHours'];
    $activeHoliday = $activePayload['holiday'];
    $activePauseBooking = $activePayload['pauseBooking'];

    $maTodayIso = now()->toDateString();

    $maFormatTime = static function (string $time): string {
        if ($time === '') {
            return '';
        }
        $parts = explode(':', $time);
        $hour = (int) ($parts[0] ?? 0);
        $minute = $parts[1] ?? '00';
        $suffix = $hour < 12 ? 'AM' : 'PM';
        return sprintf('%02d:%s %s', $hour, $minute, $suffix);
    };

    $maDayLabels = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'dashboard-section-host']) }} x-data="{ extraStaff: [] }" @staff-added.window="(() => {
        const payload = {
            id: $event.detail.id,
            name: $event.detail.name,
            job_title: $event.detail.job_title || '',
            image_url: $event.detail.image_url || null,
            initial: $event.detail.initial || 'N',
            staff_payload: $event.detail.staff_payload || null,
        };
        extraStaff.push(payload);
        $nextTick(() => {
            const newPill = $el.querySelector(`.ma-staff-pill[data-staff-id='${payload.id}']`);
            if (newPill) newPill.click();
        });
    })()">
    <section class="ma-board" aria-label="Manage availability board" style="--ma-accent: {{ $maAccentColor }};"
        data-ma-accent="{{ $maAccentColor }}">
        <div class="ma-staff-strip">
            <div class="ma-staff-list">
                @foreach ($staffMembers as $staff)
                    @php
                        $initial = mb_strtoupper(mb_substr(trim((string) $staff->name), 0, 1)) ?: 'N';
                        $rawImage = trim((string) ($staff->image ?? ''));
                        $imageUrl = null;
                        if ($rawImage !== '') {
                            $imageUrl = preg_match('#^https?://#i', $rawImage)
                                ? $rawImage
                                : asset('storage/' . ltrim($rawImage, '/'));
                        }
                    @endphp
                    <button type="button" class="ma-staff-pill {{ $loop->first ? 'ma-staff-pill--active' : '' }}"
                        data-staff-id="{{ $staff->id }}" data-staff-name="{{ $staff->name }}"
                        data-staff-job-title="{{ $staff->job_title }}"
                        data-staff-payload="{{ json_encode($maStaffPayload($staff)) }}">
                        <span class="ma-staff-avatar">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $staff->name }}">
                            @else
                                {{ $initial }}
                            @endif
                        </span>
                        <span>
                            <strong>{{ $staff->name }}</strong>
                            <small>{{ $staff->job_title }}</small>
                        </span>
                    </button>
                @endforeach

                <template x-for="staff in extraStaff" :key="staff.id">
                    <button type="button" class="ma-staff-pill" :data-staff-id="staff.id" :data-staff-name="staff.name"
                        :data-staff-job-title="staff.job_title"
                        :data-staff-payload="staff.staff_payload ? JSON.stringify(staff.staff_payload) : ''">
                        <span class="ma-staff-avatar">
                            <template x-if="staff.image_url">
                                <img :src="staff.image_url" :alt="staff.name">
                            </template>
                            <template x-if="!staff.image_url">
                                <span x-text="staff.initial"></span>
                            </template>
                        </span>
                        <span>
                            <strong x-text="staff.name"></strong>
                            <small x-text="staff.job_title"></small>
                        </span>
                    </button>
                </template>

                <button type="button" class="ma-staff-add" aria-label="Add staff"
                    @click="$dispatch('open-add-staff-modal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                        <path
                            d="M13 7.12813C13 7.68041 12.5523 8.12813 12 8.12813H7.66288V13C7.66288 13.5523 7.21516 14 6.66288 14H6.31024C5.75795 14 5.31024 13.5523 5.31024 13V8.12813H0.999999C0.447714 8.12813 0 7.68041 0 7.12813V6.85786C0 6.30557 0.447715 5.85786 1 5.85786H5.31024V0.999999C5.31024 0.447714 5.75795 0 6.31024 0H6.66288C7.21516 0 7.66288 0.447715 7.66288 1V5.85786H12C12.5523 5.85786 13 6.30557 13 6.85786V7.12813Z"
                            fill="#3B3731" />
                    </svg>
                </button>
            </div>
        </div>

        <livewire:business-hub.availability.add-staff-modal />
        <livewire:business-hub.availability.staff-actions />


        <div class="ma-section">
            <div class="ma-title-row">
                <h3 style="border: none;width: fit-content; padding: 0;">Work Week Hours</h3>
                <div class="ma-staff-name">
                    <strong data-staff-name-target>{{ $activeStaff?->name }}</strong>
                    <small data-staff-job-title-target>{{ $activeStaff?->job_title }}</small>
                </div>
            </div>

            <div class="ma-week-grid">
                <div class="ma-week-grid__head">Working Week</div>
                <div class="ma-week-grid__head">Start Time — End Time</div>
                <div class="ma-week-grid__head">Edit</div>

                @foreach ($maDayLabels as $dayKey => $dayLabel)
                    @php
                        $entry = $activeWorkingHours[$dayKey];
                        $dayStatus = $entry['status'];
                        $dayStart = $entry['start'];
                        $dayEnd = $entry['end'];
                    @endphp
                    <div class="ma-cell is-disabled" data-day="{{ $dayKey }}" data-day-cell="name"
                        style="padding-left: 0 !important;">
                        <div class="ma-day-name">
                            <span>{{ $dayLabel }}</span>
                            <label class="ma-switch" style="height: 24px;">
                                <input type="checkbox" data-day-status {{ $dayStatus ? 'checked' : '' }}>
                                <span class="ma-switch-slider"></span>
                                <span class="ma-switch-check-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="ma-cell ma-time-range is-disabled" data-day="{{ $dayKey }}" data-day-cell="time">
                        <span class="ma-time-chip" data-time-type="start" data-time-value="{{ $dayStart }}" role="button"
                            tabindex="0">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            <span data-time-start>{{ $maFormatTime($dayStart) }}</span>
                        </span>
                        <span class="ma-time-separator"></span>
                        <span class="ma-time-chip" data-time-type="end" data-time-value="{{ $dayEnd }}" role="button"
                            tabindex="0">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            <span data-time-end>{{ $maFormatTime($dayEnd) }}</span>
                        </span>
                        <div class="ma-time-disabled">
                            <span class="ma-time-disabled-chip">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                <span data-time-start-readonly>{{ $maFormatTime($dayStart) }}</span>
                            </span>
                            <span class="ma-time-disabled-separator"></span>
                            <span class="ma-time-disabled-chip">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                <span data-time-end-readonly>{{ $maFormatTime($dayEnd) }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="ma-cell ma-edit-cell is-disabled" data-day="{{ $dayKey }}" data-day-cell="edit">
                        <button type="button" class="ma-save-mini" data-day-action="save">
                            <span class="ma-save-mini__label" data-save-label>Save</span>
                            <span class="ma-save-spinner" data-save-spinner aria-hidden="true"></span>
                        </button>
                        <button type="button" class="ma-row-action" data-day-action="edit" aria-label="Edit day slot">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                                <path
                                    d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="ma-section">
            <h3>Holiday / Time Off</h3>
            <div class="ma-holiday-grid">
                <div class="ma-holiday-form">
                    <div class="ma-inline-fields">
                        <label class="ma-field" data-date-trigger="from">
                            <span>Date From</span>
                            <span class="ma-field-value">
                                <span class="ma-field-value__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                        fill="none">
                                        <path
                                            d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                            stroke="#3B3731" />
                                        <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M11.643 10.166C11.643 10.3428 11.5696 10.5124 11.439 10.6374C11.3084 10.7624 11.1312 10.8327 10.9465 10.8327C10.7618 10.8327 10.5847 10.7624 10.4541 10.6374C10.3235 10.5124 10.2501 10.3428 10.2501 10.166C10.2501 9.98925 10.3235 9.81969 10.4541 9.69468C10.5847 9.56967 10.7618 9.49944 10.9465 9.49944C11.1312 9.49944 11.3084 9.56967 11.439 9.69468C11.5696 9.81969 11.643 9.98925 11.643 10.166ZM11.643 7.49961C11.643 7.67641 11.5696 7.84596 11.439 7.97098C11.3084 8.09599 11.1312 8.16622 10.9465 8.16622C10.7618 8.16622 10.5847 8.09599 10.4541 7.97098C10.3235 7.84596 10.2501 7.67641 10.2501 7.49961C10.2501 7.32282 10.3235 7.15327 10.4541 7.02825C10.5847 6.90324 10.7618 6.83301 10.9465 6.83301C11.1312 6.83301 11.3084 6.90324 11.439 7.02825C11.5696 7.15327 11.643 7.32282 11.643 7.49961ZM8.1609 10.166C8.1609 10.3428 8.08752 10.5124 7.95692 10.6374C7.82632 10.7624 7.64918 10.8327 7.46448 10.8327C7.27978 10.8327 7.10265 10.7624 6.97205 10.6374C6.84144 10.5124 6.76807 10.3428 6.76807 10.166C6.76807 9.98925 6.84144 9.81969 6.97205 9.69468C7.10265 9.56967 7.27978 9.49944 7.46448 9.49944C7.64918 9.49944 7.82632 9.56967 7.95692 9.69468C8.08752 9.81969 8.1609 9.98925 8.1609 10.166ZM8.1609 7.49961C8.1609 7.67641 8.08752 7.84596 7.95692 7.97098C7.82632 8.09599 7.64918 8.16622 7.46448 8.16622C7.27978 8.16622 7.10265 8.09599 6.97205 7.97098C6.84144 7.84596 6.76807 7.67641 6.76807 7.49961C6.76807 7.32282 6.84144 7.15327 6.97205 7.02825C7.10265 6.90324 7.27978 6.83301 7.46448 6.83301C7.64918 6.83301 7.82632 6.90324 7.95692 7.02825C8.08752 7.15327 8.1609 7.32282 8.1609 7.49961ZM4.67884 10.166C4.67884 10.3428 4.60546 10.5124 4.47486 10.6374C4.34426 10.7624 4.16712 10.8327 3.98242 10.8327C3.79772 10.8327 3.62059 10.7624 3.48999 10.6374C3.35938 10.5124 3.28601 10.3428 3.28601 10.166C3.28601 9.98925 3.35938 9.81969 3.48999 9.69468C3.62059 9.56967 3.79772 9.49944 3.98242 9.49944C4.16712 9.49944 4.34426 9.56967 4.47486 9.69468C4.60546 9.81969 4.67884 9.98925 4.67884 10.166ZM4.67884 7.49961C4.67884 7.67641 4.60546 7.84596 4.47486 7.97098C4.34426 8.09599 4.16712 8.16622 3.98242 8.16622C3.79772 8.16622 3.62059 8.09599 3.48999 7.97098C3.35938 7.84596 3.28601 7.67641 3.28601 7.49961C3.28601 7.32282 3.35938 7.15327 3.48999 7.02825C3.62059 6.90324 3.79772 6.83301 3.98242 6.83301C4.16712 6.83301 4.34426 6.90324 4.47486 7.02825C4.60546 7.15327 4.67884 7.32282 4.67884 7.49961Z"
                                            fill="#3B3731" />
                                    </svg>
                                </span>
                                <span id="manage-availability-date-from"></span>
                            </span>
                        </label>
                        <label class="ma-field" data-date-trigger="to">
                            <span>Date To</span>
                            <span class="ma-field-value" style="background: #F7F7F7;">
                                <span class="ma-field-value__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                                        fill="none">
                                        <path
                                            d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                            stroke="#3B3731" />
                                        <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M11.643 10.166C11.643 10.3428 11.5696 10.5124 11.439 10.6374C11.3084 10.7624 11.1312 10.8327 10.9465 10.8327C10.7618 10.8327 10.5847 10.7624 10.4541 10.6374C10.3235 10.5124 10.2501 10.3428 10.2501 10.166C10.2501 9.98925 10.3235 9.81969 10.4541 9.69468C10.5847 9.56967 10.7618 9.49944 10.9465 9.49944C11.1312 9.49944 11.3084 9.56967 11.439 9.69468C11.5696 9.81969 11.643 9.98925 11.643 10.166ZM11.643 7.49961C11.643 7.67641 11.5696 7.84596 11.439 7.97098C11.3084 8.09599 11.1312 8.16622 10.9465 8.16622C10.7618 8.16622 10.5847 8.09599 10.4541 7.97098C10.3235 7.84596 10.2501 7.67641 10.2501 7.49961C10.2501 7.32282 10.3235 7.15327 10.4541 7.02825C10.5847 6.90324 10.7618 6.83301 10.9465 6.83301C11.1312 6.83301 11.3084 6.90324 11.439 7.02825C11.5696 7.15327 11.643 7.32282 11.643 7.49961ZM8.1609 10.166C8.1609 10.3428 8.08752 10.5124 7.95692 10.6374C7.82632 10.7624 7.64918 10.8327 7.46448 10.8327C7.27978 10.8327 7.10265 10.7624 6.97205 10.6374C6.84144 10.5124 6.76807 10.3428 6.76807 10.166C6.76807 9.98925 6.84144 9.81969 6.97205 9.69468C7.10265 9.56967 7.27978 9.49944 7.46448 9.49944C7.64918 9.49944 7.82632 9.56967 7.95692 9.69468C8.08752 9.81969 8.1609 9.98925 8.1609 10.166ZM8.1609 7.49961C8.1609 7.67641 8.08752 7.84596 7.95692 7.97098C7.82632 8.09599 7.64918 8.16622 7.46448 8.16622C7.27978 8.16622 7.10265 8.09599 6.97205 7.97098C6.84144 7.84596 6.76807 7.67641 6.76807 7.49961C6.76807 7.32282 6.84144 7.15327 6.97205 7.02825C7.10265 6.90324 7.27978 6.83301 7.46448 6.83301C7.64918 6.83301 7.82632 6.90324 7.95692 7.02825C8.08752 7.15327 8.1609 7.32282 8.1609 7.49961ZM4.67884 10.166C4.67884 10.3428 4.60546 10.5124 4.47486 10.6374C4.34426 10.7624 4.16712 10.8327 3.98242 10.8327C3.79772 10.8327 3.62059 10.7624 3.48999 10.6374C3.35938 10.5124 3.28601 10.3428 3.28601 10.166C3.28601 9.98925 3.35938 9.81969 3.48999 9.69468C3.62059 9.56967 3.79772 9.49944 3.98242 9.49944C4.16712 9.49944 4.34426 9.56967 4.47486 9.69468C4.60546 9.81969 4.67884 9.98925 4.67884 10.166ZM4.67884 7.49961C4.67884 7.67641 4.60546 7.84596 4.47486 7.97098C4.34426 8.09599 4.16712 8.16622 3.98242 8.16622C3.79772 8.16622 3.62059 8.09599 3.48999 7.97098C3.35938 7.84596 3.28601 7.67641 3.28601 7.49961C3.28601 7.32282 3.35938 7.15327 3.48999 7.02825C3.62059 6.90324 3.79772 6.83301 3.98242 6.83301C4.16712 6.83301 4.34426 6.90324 4.47486 7.02825C4.60546 7.15327 4.67884 7.32282 4.67884 7.49961Z"
                                            fill="#3B3731" />
                                    </svg>
                                </span>
                                <span id="manage-availability-date-to"></span>
                            </span>
                        </label>
                    </div>
                    <label class="ma-field">
                        <span style="margin-top: 1.5rem;">Reason<span style="font-weight: 400;">(optional)</span></span>
                        <textarea rows="3" data-holiday-reason></textarea>
                    </label>
                    <div class="ma-form-actions">
                        <button type="button" class="ma-save-mini" data-holiday-action="save">
                            <span class="ma-save-mini__label" data-save-label>Save</span>
                            <span class="ma-save-spinner" data-save-spinner aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="ma-holiday-calendar">
                    <x-ui.range-date-calendar id="dashboard-manage-availability-range"
                        start-name="manage_availability_start_date" end-name="manage_availability_end_date"
                        start-value="{{ $maTodayIso }}" end-value="{{ $maTodayIso }}" calendar-width="100%" />
                </div>
            </div>

            <livewire:business-hub.availability.holiday-list :staff-id="$activeStaff?->id" />

        </div>

        <div class="ma-section">
            <h3>Pause Bookings</h3>
            <div class="ma-pause-row">
                <span>Pause New Bookings <small>(effective today)</small></span>
                <label class="ma-switch">
                    <input type="checkbox" data-pause-booking {{ $activePauseBooking ? 'checked' : '' }}>
                    <span class="ma-switch-slider"></span>
                    <span class="ma-switch-check-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                fill="white" />
                        </svg>
                    </span>
                </label>
            </div>
        </div>

        <div class="ma-footer-actions">
            <button type="button" class="ma-btn ma-btn-light">Cancel</button>
            <button type="button" class="ma-btn ma-btn-primary">Save Changes</button>
        </div>
    </section>
</div>

<style>
    .ma-board {
        margin-top: 2rem;
        width: 100%;
        color: #3B3731;
        font-family: Lato;
    }

    .ma-holiday-form {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .ma-staff-strip {
        border-bottom: 1px solid #d8d1c7;
        margin-bottom: 1.25rem;
    }

    .ma-staff-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .ma-staff-pill {
        border: 0;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        padding: 0.25rem 1rem 1rem;
        position: relative;
    }

    .ma-staff-pill:last-child {
        margin-left: 0;
    }

    .ma-staff-pill::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 100%;
        height: 1px;
        background: #3B3731;
        transform: translateX(-50%) scaleX(0);
        transform-origin: center;
        opacity: 0;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .ma-staff-pill--active::after {
        transform: translateX(-50%) scaleX(1);
        opacity: 1;
    }

    .ma-staff-avatar {
        width: 43px;
        height: 43px;
        aspect-ratio: 42.50/42.75;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #d9d9d9;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
        overflow: hidden;
    }

    .ma-staff-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
        display: block;
    }

    .ma-staff-pill strong {
        display: block;
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-pill small {
        display: block;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-add {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 2.5rem;
        border: 0;
        background: transparent;
        color: #3B3731;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        padding: 0.2rem 0.5rem;
    }

    .ma-section {
        margin-bottom: 2rem;
    }

    .ma-section h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        width: 100%;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #D4D4D4;
        margin-bottom: 0.8rem;
    }

    .ma-title-row {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid #d8d1c7;
        margin-bottom: 0.8rem;
    }

    .ma-staff-name {
        text-align: right;
    }

    .ma-staff-name strong {
        display: block;
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-staff-name small {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .ma-week-grid {
        display: grid;
        margin-top: 1.5rem;
        grid-template-columns: 1.2fr 1.9fr 0.8fr;
        border-bottom: 1px solid #dfd8ce;
    }

    .ma-week-grid__head {
        padding: 1rem 1.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border-right: 1px solid #dfd8ce;
    }

    .ma-week-grid__head:first-child {
        padding-left: 0;
    }

    .ma-week-grid>.ma-week-grid__head:nth-child(3) {
        border-right: 0 !important;
        padding-left: 4.5rem;
    }

    .ma-cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #ece7df;
        border-right: 1px solid #dfd8ce;
    }

    .ma-cell:nth-child(3n) {
        justify-content: start;
        border-right: 0 !important;
        padding-left: 4.5rem;
    }

    .ma-day-name {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.5rem 1rem;
        background: #F9FAFC;
        border-radius: 10px;
        background: #F7F7F7;
    }

    .ma-day-name span {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        /* 138.889% */
    }

    .ma-time-range {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .ma-time-chip {
        width: 100%;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.9rem;
        border: 1px solid #d5cfc6;
        border-radius: 999px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        min-width: 135px;
        justify-content: center;
        cursor: pointer;
        user-select: none;
        transition: border-color 0.12s ease, background-color 0.12s ease;
    }

    .ma-time-chip:hover {
        border-color: var(--ma-accent, #ffc97a);
        background: color-mix(in srgb, var(--ma-accent, #ffc97a) 14%, #fff);
    }

    .ma-time-chip.ma-time-chip--open {
        border-color: var(--ma-accent, #ffc97a);
        background: color-mix(in srgb, var(--ma-accent, #ffc97a) 22%, #fff);
    }

    .ma-time-dropdown {
        position: fixed;
        z-index: 9999;
        max-height: 260px;
        overflow-y: auto;
        overscroll-behavior: contain;
        background: #FFFFFF;
        border: 1px solid #E5E1D8;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        padding: 6px;
        box-sizing: border-box;
        opacity: 0;
        transform: translateY(-4px);
        pointer-events: none;
        transition: opacity 0.14s ease, transform 0.14s ease;
    }

    .ma-time-dropdown.is-open {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .ma-time-dropdown__option {
        display: block;
        width: 100%;
        text-align: left;
        background: transparent;
        border: 0;
        padding: 8px 12px;
        border-radius: 8px;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        color: #3B3731;
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease;
    }

    .ma-time-dropdown__option:hover {
        background: color-mix(in srgb, var(--ma-accent, #ffc97a) 18%, #fff);
    }

    .ma-time-dropdown__option.is-selected {
        background: var(--ma-accent, #ffc97a);
        color: #fff;
    }

    .ma-time-separator {
        background: #D4D4D4;
        width: 1px;
        height: 25px;
    }

    .ma-time-disabled {
        height: 42px;
        width: 100%;
        border-radius: 10px;
        background: #F7F7F7;
        display: none;
        align-items: center;
        justify-content: center;
        gap: 7.5rem;
        padding: 0 0.75rem;
    }

    .ma-time-disabled-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        /* 138.889% */
    }

    .ma-time-disabled-separator {
        width: 1px;
        height: 18px;
        background: #D4D4D4;
    }

    .ma-time-range.is-disabled .ma-time-chip,
    .ma-time-range.is-disabled .ma-time-separator {
        display: none;
    }

    .ma-time-range.is-disabled .ma-time-disabled {
        display: flex;
    }

    .ma-cell[data-day-cell="name"].is-disabled .ma-switch {
        cursor: not-allowed;
    }

    .ma-cell[data-day-cell="name"].is-disabled .ma-switch input,
    .ma-cell[data-day-cell="name"].is-disabled .ma-switch-slider {
        pointer-events: none;
        cursor: not-allowed;
    }


    .ma-edit-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
    }

    .ma-edit-cell .ma-row-action {
        display: none;
    }

    .ma-edit-cell.is-disabled .ma-save-mini {
        display: none;
    }

    .ma-edit-cell.is-disabled .ma-row-action {
        display: flex;
    }

    .ma-row-action {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0.2rem;
    }

    .ma-save-mini {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        width: 74px;
        height: 42px;
        border-radius: 100px;
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: background-color 0.18s ease, opacity 0.18s ease;
    }

    .ma-save-mini:hover {
        background: #A8C076;
    }

    .ma-save-mini.is-saving {
        pointer-events: none;
    }

    .ma-save-mini .ma-save-spinner {
        display: none;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 50%;
        animation: ma-save-spin 0.7s linear infinite;
    }

    .ma-save-mini.is-saving .ma-save-mini__label {
        display: none;
    }

    .ma-save-mini.is-saving .ma-save-spinner {
        display: inline-block;
    }

    @keyframes ma-save-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .ma-holiday-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: start;
        margin-top: 2.5rem;
    }

    .ma-inline-fields {
        margin-top: 1.2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem;
    }

    .ma-field {
        display: block;
    }

    .ma-field span {
        display: flex;
        gap: 5px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-field>span:first-child {
        margin-bottom: 1.5rem;
    }

    .ma-field input,
    .ma-field .ma-field-value,
    .ma-field textarea {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        /* 138.889% */
        padding: 0.6rem 0.75rem;
    }

    .ma-field .ma-field-value {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 57px;
    }

    .ma-field .ma-field-value__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ma-field textarea {
        min-height: 96px;
        resize: vertical;
    }

    .ma-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.7rem;
    }

    .ma-holiday-calendar {
        border-radius: 10px;
    }

    .ma-holiday-list {
        margin-top: 1.75rem;
        padding: 20px;
        border-radius: 10px;
        background: #F7F7F7;
        overflow: hidden;
        font-family: Lato;
        color: #3B3731;
    }

    .ma-holiday-list__header,
    .ma-holiday-list__row {
        display: grid;
        grid-template-columns: 2fr 2fr 90px;
        align-items: stretch;
        padding-left: 0;
    }

    .ma-holiday-list__header>div,
    .ma-holiday-list__row>div {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
    }

    .ma-holiday-list__header>div:not(:last-child),
    .ma-holiday-list__row>div:not(:last-child) {
        border-right: 1px solid #D4D4D4;
    }

    .ma-holiday-list__header>div {
        padding-top: 0;
    }

    .ma-holiday-list__header>div:first-child {
        padding-left: 0;
    }

    .ma-holiday-list__row:last-child>div {
        padding-bottom: 0;
    }

    .ma-holiday-list__range {
        padding-left: 0 !important;
    }

    .ma-holiday-list__row:last-child>div:last-child {
        padding-left: 0;
    }

    .ma-holiday-list__header {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-holiday-list__edit-col {
        justify-content: center;
    }

    .ma-holiday-list__row {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border-top: 1px solid #D4D4D4;
    }

    .ma-holiday-list__dates {
        font-weight: 600;
    }

    .ma-holiday-list__days {
        margin-left: 0.35rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .ma-holiday-list__reason {
        color: #3B3731;
    }

    .ma-holiday-list__actions {
        padding-left: 0 !important;
        display: flex;
        justify-content: center;
    }

    .ma-holiday-list__delete {
        border: 0;
        background: transparent;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3B3731;
        transition: background-color 0.12s ease, transform 0.12s ease;
    }

    .ma-holiday-list__delete:hover {
        background: rgba(0, 0, 0, 0.06);
    }

    .ma-holiday-list__delete.is-deleting,
    .ma-holiday-list__delete:disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .ma-holiday-list__delete-spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(59, 55, 49, 0.25);
        border-top-color: #3B3731;
        border-radius: 50%;
        animation: ma-save-spin 0.7s linear infinite;
    }

    .ma-holiday-list__body.is-loading {
        opacity: 0.6;
        transition: opacity 0.18s ease;
    }

    .ma-holiday-list__empty {
        padding: 1.25rem 1.5rem;
        text-align: center;
        color: #9C9790;
        font-size: 14px;
    }

    .ma-field[data-date-trigger] .ma-field-value {
        cursor: pointer;
    }

    .ma-mini-cal {
        position: fixed;
        z-index: 9999;
        background: #fff;
        border: 1px solid #ccc3b7;
        border-radius: 12px;
        padding: 14px 16px;
        box-sizing: border-box;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        color: #3B3731;
        font-family: Lato;
        opacity: 0;
        transform: translateY(-4px);
        pointer-events: none;
        transition: opacity 0.14s ease, transform 0.14s ease;
    }

    .ma-mini-cal.is-open {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    /* When switching Date From ↔ Date To, is-open never toggles off; skip transition to reset enter animation. */
    .ma-mini-cal.ma-mini-cal--instant {
        transition: none;
    }

    .ma-mini-cal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .ma-mini-cal__nav {
        border: 0;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
        width: 28px;
        height: 28px;
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-weight: 600;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 180ms ease, transform 180ms ease;
    }

    .ma-mini-cal__nav:hover {
        color: #FFC97A;
        transform: scale(1.1);
    }

    .ma-mini-cal__title {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: center;
    }

    .ma-mini-cal__weekdays,
    .ma-mini-cal__grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .ma-mini-cal__weekdays {
        margin-bottom: 6px;
    }

    .ma-mini-cal__weekdays span {
        text-align: center;
        color: #9C9790;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-mini-cal__day {
        border: 0;
        background: transparent;
        width: 100%;
        height: 34px;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        border-radius: 0;
        transition: background-color 180ms ease, color 180ms ease;
    }

    .ma-mini-cal__day-label {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 180ms ease, color 180ms ease, transform 180ms ease, box-shadow 180ms ease;
    }

    .ma-mini-cal__day.is-other-month .ma-mini-cal__day-label {
        color: transparent;
    }

    .ma-mini-cal__day.is-disabled {
        cursor: not-allowed;
    }

    .ma-mini-cal__day.is-disabled .ma-mini-cal__day-label {
        color: #DBD8D2;
    }

    .ma-mini-cal__day:not(.is-disabled):not(.is-selected):hover .ma-mini-cal__day-label {
        transform: scale(1.05);
    }

    .ma-mini-cal__day.is-selected .ma-mini-cal__day-label {
        background: #FFC97A;
        color: #fff;
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(255, 201, 122, 0.35);
    }

    .ma-pause-row {
        width: fit-content;
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 1rem;
        border-bottom: 1px solid #dfd8ce;
        padding: 0.75rem 0;
    }

    .ma-pause-row span {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-pause-row small {
        color: #9C9790;
    }

    .ma-switch {
        position: relative;
        display: inline-block;
        width: 42px;
        /* height: 24px; */
    }

    .ma-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .ma-switch-slider {
        width: 44px;
        height: 24px;
        aspect-ratio: 11/6;
        position: absolute;
        cursor: pointer;
        inset: 0;
        border-radius: 999px;
        background: #dfdfdf;
        transition: background-color 0.2s ease;
    }

    .ma-switch-slider::before {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 999px;
        background: #fff;
        top: 4px;
        left: 4px;
        transition: transform 0.2s ease;
    }

    .ma-switch input:checked+.ma-switch-slider {
        background: #d4e5ad;
    }

    .ma-switch input:checked+.ma-switch-slider::before {
        transform: translateX(20px);
        background: transparent;
    }

    .ma-switch-check-icon {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        line-height: 0;
        pointer-events: none;
        opacity: 0;
        transform: translateX(0);
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .ma-switch input:checked~.ma-switch-check-icon {
        opacity: 1;
        transform: translateX(20px);
    }

    .ma-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
    }

    .ma-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 138px;
        height: 42px;
        border-radius: 100px;
        border: none;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }

    .ma-btn-light {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 75px;
        border: 1px solid #3B3731;
        background: transparent;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .ma-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 96px;
        background: #BACF8E;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }
</style>

<script>
    (() => {
        const setupManageAvailability = () => {
            const root = document.querySelector('.ma-board');
            if (!root || root.dataset.rangeSyncBound === '1') {
                return;
            }
            const staffList = root.querySelector('.ma-staff-list');
            const staffNameTarget = root.querySelector('[data-staff-name-target]');
            const staffJobTitleTarget = root.querySelector('[data-staff-job-title-target]');
            const reasonField = root.querySelector('[data-holiday-reason]');
            const pauseField = root.querySelector('[data-pause-booking]');

            const formatTime = (raw) => {
                if (!raw) return '';
                const [hStr, mStr = '00'] = String(raw).split(':');
                const hour = parseInt(hStr, 10);
                if (Number.isNaN(hour)) return raw;
                const suffix = hour < 12 ? 'AM' : 'PM';
                return `${String(hour).padStart(2, '0')}:${mStr} ${suffix}`;
            };

            const applyWorkingHours = (workingHours) => {
                if (!workingHours) return;
                Object.entries(workingHours).forEach(([day, data]) => {
                    const nameRow = root.querySelector(
                        `[data-day="${day}"][data-day-cell="name"]`);
                    const timeRow = root.querySelector(
                        `[data-day="${day}"][data-day-cell="time"]`);
                    const editRow = root.querySelector(
                        `[data-day="${day}"][data-day-cell="edit"]`);
                    if (!nameRow || !timeRow || !editRow) return;
                    const checkbox = nameRow.querySelector('input[data-day-status]');
                    if (checkbox) checkbox.checked = !!data.status;
                    const startChip = timeRow.querySelector('[data-time-type="start"]');
                    const endChip = timeRow.querySelector('[data-time-type="end"]');
                    const startEl = timeRow.querySelector('[data-time-start]');
                    const endEl = timeRow.querySelector('[data-time-end]');
                    const startReadOnlyEl = timeRow.querySelector(
                        '[data-time-start-readonly]');
                    const endReadOnlyEl = timeRow.querySelector(
                        '[data-time-end-readonly]');
                    const startLabel = formatTime(data.start);
                    const endLabel = formatTime(data.end);
                    if (startChip) startChip.dataset.timeValue = data.start || '';
                    if (endChip) endChip.dataset.timeValue = data.end || '';
                    if (startEl) startEl.textContent = startLabel;
                    if (endEl) endEl.textContent = endLabel;
                    if (startReadOnlyEl) startReadOnlyEl.textContent = startLabel;
                    if (endReadOnlyEl) endReadOnlyEl.textContent = endLabel;
                    nameRow.classList.add('is-disabled');
                    timeRow.classList.add('is-disabled');
                    editRow.classList.add('is-disabled');
                    const saveBtn = editRow.querySelector('[data-day-action="save"]');
                    if (saveBtn) saveBtn.classList.remove('is-saving');
                });
            };

            const todayIso = (() => {
                const d = new Date();
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${dd}`;
            })();

            const resetHolidayForm = () => {
                if (reasonField) reasonField.value = '';
                window.dispatchEvent(new CustomEvent('range-calendar-set', {
                    detail: {
                        componentId: 'dashboard-manage-availability-range',
                        start: todayIso,
                        end: todayIso,
                    },
                }));
            };

            const applyHoliday = () => {
                resetHolidayForm();
            };

            const applyPauseBooking = (paused) => {
                if (pauseField) pauseField.checked = !!paused;
            };

            const applyStaffPayload = (pill) => {
                if (!pill) return;
                const raw = pill.dataset.staffPayload || '';
                if (!raw) return;
                let payload;
                try {
                    payload = JSON.parse(raw);
                } catch (err) {
                    return;
                }
                applyWorkingHours(payload.workingHours);
                applyHoliday(payload.holiday);
                applyPauseBooking(payload.pauseBooking);
            };

            const syncActiveStaff = (pill) => {
                if (!pill) return;
                if (staffNameTarget) {
                    staffNameTarget.textContent = pill.dataset.staffName || '';
                }
                if (staffJobTitleTarget) {
                    staffJobTitleTarget.textContent = pill.dataset.staffJobTitle || '';
                }
                applyStaffPayload(pill);
            };

            syncActiveStaff(root.querySelector('.ma-staff-pill.ma-staff-pill--active'));

            const notifyActiveStaff = (pill) => {
                if (!pill || !window.Livewire) return;
                const staffId = parseInt(pill.dataset.staffId || '0', 10) || null;
                window.Livewire.dispatch('active-staff-changed', {
                    staffId
                });
            };

            if (staffList && !staffList.dataset.pillClickBound) {
                staffList.dataset.pillClickBound = '1';
                staffList.addEventListener('click', (event) => {
                    const pill = event.target.closest('.ma-staff-pill');
                    if (!pill || !staffList.contains(pill)) return;
                    staffList.querySelectorAll('.ma-staff-pill').forEach((item) => item
                        .classList.remove('ma-staff-pill--active'));
                    pill.classList.add('ma-staff-pill--active');
                    closeTimeDropdown();
                    if (typeof closeMiniCalendar === 'function') closeMiniCalendar();
                    syncActiveStaff(pill);
                    notifyActiveStaff(pill);
                });
            }

            const setRowEditing = (day, editing) => {
                const nameRow = root.querySelector(
                    `[data-day="${day}"][data-day-cell="name"]`);
                const timeRow = root.querySelector(
                    `[data-day="${day}"][data-day-cell="time"]`);
                const editRow = root.querySelector(
                    `[data-day="${day}"][data-day-cell="edit"]`);
                if (nameRow) nameRow.classList.toggle('is-disabled', !editing);
                if (timeRow) timeRow.classList.toggle('is-disabled', !editing);
                if (editRow) editRow.classList.toggle('is-disabled', !editing);
            };

            const buildTimeOptions = () => {
                const opts = [];
                for (let h = 0; h < 24; h += 1) {
                    for (let m = 0; m < 60; m += 30) {
                        const value =
                            `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
                        opts.push({
                            value,
                            label: formatTime(value),
                        });
                    }
                }
                return opts;
            };

            let timeDropdownEl = null;
            let activeTimeChip = null;

            const closeTimeDropdown = () => {
                if (!timeDropdownEl) return;
                timeDropdownEl.classList.remove('is-open');
                if (activeTimeChip) {
                    activeTimeChip.classList.remove('ma-time-chip--open');
                    activeTimeChip = null;
                }
            };

            const syncTimeDropdownAccent = (dropdown) => {
                const accent =
                    root.dataset.maAccent ||
                    getComputedStyle(root).getPropertyValue('--ma-accent').trim() ||
                    '#FFC97A';
                dropdown.style.setProperty('--ma-accent', accent);
            };

            const ensureTimeDropdown = () => {
                if (timeDropdownEl) return timeDropdownEl;
                const el = document.createElement('div');
                el.className = 'ma-time-dropdown';
                syncTimeDropdownAccent(el);
                buildTimeOptions().forEach(({
                    value,
                    label,
                }) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'ma-time-dropdown__option';
                    btn.dataset.timeOption = value;
                    btn.textContent = label;
                    el.appendChild(btn);
                });
                el.addEventListener('click', (event) => {
                    const option = event.target.closest('[data-time-option]');
                    if (!option || !activeTimeChip) return;
                    const value = option.dataset.timeOption;
                    const label = option.textContent;
                    activeTimeChip.dataset.timeValue = value;
                    const span = activeTimeChip.querySelector(
                        '[data-time-start], [data-time-end]');
                    if (span) span.textContent = label;
                    closeTimeDropdown();
                });
                document.body.appendChild(el);
                timeDropdownEl = el;
                return el;
            };

            const positionTimeDropdown = (chip) => {
                const dropdown = ensureTimeDropdown();
                const rect = chip.getBoundingClientRect();
                dropdown.style.width = `${rect.width}px`;
                dropdown.style.top = `${rect.bottom + 6}px`;
                let left = rect.left;
                if (left + rect.width > window.innerWidth - 8) {
                    left = window.innerWidth - rect.width - 8;
                }
                if (left < 8) left = 8;
                dropdown.style.left = `${left}px`;
            };

            const openTimeDropdown = (chip) => {
                const dropdown = ensureTimeDropdown();
                syncTimeDropdownAccent(dropdown);
                if (activeTimeChip && activeTimeChip !== chip) {
                    activeTimeChip.classList.remove('ma-time-chip--open');
                }
                activeTimeChip = chip;
                chip.classList.add('ma-time-chip--open');
                const current = chip.dataset.timeValue || '';
                dropdown.querySelectorAll('.ma-time-dropdown__option').forEach((opt) => {
                    opt.classList.toggle('is-selected', opt.dataset.timeOption ===
                        current);
                });
                dropdown.classList.add('is-open');
                positionTimeDropdown(chip);
                const selected = dropdown.querySelector('.ma-time-dropdown__option.is-selected');
                if (selected) selected.scrollIntoView({
                    block: 'center'
                });
            };

            if (!root.dataset.dayActionsBound) {
                root.dataset.dayActionsBound = '1';

                root.addEventListener('click', (event) => {
                    const chip = event.target.closest('.ma-time-chip');
                    const chipRow = chip?.closest('.ma-time-range');
                    if (chip && chipRow && !chipRow.classList.contains('is-disabled')) {
                        event.stopPropagation();
                        if (activeTimeChip === chip) {
                            closeTimeDropdown();
                        } else {
                            openTimeDropdown(chip);
                        }
                        return;
                    }

                    const editBtn = event.target.closest('[data-day-action="edit"]');
                    const saveBtn = event.target.closest('[data-day-action="save"]');

                    if (editBtn) {
                        const cell = editBtn.closest('.ma-cell');
                        const day = cell?.dataset.day;
                        if (!day) return;
                        closeTimeDropdown();
                        setRowEditing(day, true);
                        return;
                    }

                    if (saveBtn) {
                        if (saveBtn.classList.contains('is-saving')) return;
                        const cell = saveBtn.closest('.ma-cell');
                        const day = cell?.dataset.day;
                        if (!day) return;
                        closeTimeDropdown();
                        const activePill = root.querySelector(
                            '.ma-staff-pill.ma-staff-pill--active');
                        const staffId = activePill?.dataset.staffId;
                        if (!staffId) return;
                        const checkbox = root.querySelector(
                            `[data-day="${day}"][data-day-cell="name"] input[data-day-status]`);
                        const startChip = root.querySelector(
                            `[data-day="${day}"][data-day-cell="time"] [data-time-type="start"]`);
                        const endChip = root.querySelector(
                            `[data-day="${day}"][data-day-cell="time"] [data-time-type="end"]`);
                        const payload = {
                            staffId: parseInt(staffId, 10),
                            day,
                            status: !!(checkbox && checkbox.checked),
                            start: startChip?.dataset.timeValue || '10:00',
                            end: endChip?.dataset.timeValue || '18:00',
                        };

                        if (activePill && activePill.dataset.staffPayload) {
                            try {
                                const parsed = JSON.parse(activePill.dataset
                                    .staffPayload);
                                parsed.workingHours = parsed.workingHours || {};
                                parsed.workingHours[day] = {
                                    status: payload.status,
                                    start: payload.start,
                                    end: payload.end,
                                };
                                activePill.dataset.staffPayload = JSON.stringify(
                                    parsed);
                            } catch (err) { }
                        }

                        saveBtn.classList.add('is-saving');
                        if (window.Livewire) {
                            window.Livewire.dispatch('save-staff-day', payload);
                        }
                    }
                });

                window.addEventListener('staff-day-saved', (event) => {
                    const detail = event?.detail || {};
                    const day = detail.day;
                    if (!day) return;
                    const editRow = root.querySelector(
                        `[data-day="${day}"][data-day-cell="edit"]`);
                    const saveBtn = editRow?.querySelector('[data-day-action="save"]');
                    if (saveBtn) saveBtn.classList.remove('is-saving');
                    const timeRow = root.querySelector(
                        `[data-day="${day}"][data-day-cell="time"]`);
                    if (timeRow) {
                        const startEl = timeRow.querySelector('[data-time-start]');
                        const endEl = timeRow.querySelector('[data-time-end]');
                        const startReadOnlyEl = timeRow.querySelector(
                            '[data-time-start-readonly]');
                        const endReadOnlyEl = timeRow.querySelector(
                            '[data-time-end-readonly]');
                        if (startEl && startReadOnlyEl) startReadOnlyEl.textContent =
                            startEl.textContent;
                        if (endEl && endReadOnlyEl) endReadOnlyEl.textContent = endEl
                            .textContent;
                    }
                    setRowEditing(day, false);
                });

                document.addEventListener('click', (event) => {
                    if (!timeDropdownEl || !timeDropdownEl.classList.contains(
                        'is-open')) return;
                    if (event.target.closest('.ma-time-dropdown')) return;
                    if (event.target.closest('.ma-time-chip')) return;
                    closeTimeDropdown();
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') closeTimeDropdown();
                });

                window.addEventListener('scroll', (event) => {
                    if (!activeTimeChip) return;
                    const target = event.target;
                    if (target && target.nodeType === 1 && target.closest &&
                        target.closest('.ma-time-dropdown')) return;
                    positionTimeDropdown(activeTimeChip);
                }, true);
                window.addEventListener('resize', () => {
                    if (activeTimeChip) positionTimeDropdown(activeTimeChip);
                });

                const holidaySaveBtn = root.querySelector(
                    '[data-holiday-action="save"]');
                if (holidaySaveBtn) {
                    holidaySaveBtn.addEventListener('click', () => {
                        if (holidaySaveBtn.classList.contains('is-saving')) return;
                        const activePill = root.querySelector(
                            '.ma-staff-pill.ma-staff-pill--active');
                        const staffId = activePill?.dataset.staffId;
                        if (!staffId) return;
                        const range = (typeof getInlineRangeValues === 'function') ?
                            getInlineRangeValues() : {
                                start: '',
                                end: ''
                            };
                        const reason = reasonField?.value || '';
                        const payload = {
                            staffId: parseInt(staffId, 10),
                            from: range.start || '',
                            to: range.end || '',
                            reason,
                        };

                        holidaySaveBtn.classList.add('is-saving');
                        if (window.Livewire) {
                            window.Livewire.dispatch('save-staff-holiday',
                                payload);
                        }
                    });
                }

                window.addEventListener('staff-holiday-saved', () => {
                    const btn = root.querySelector(
                        '[data-holiday-action="save"]');
                    if (btn) btn.classList.remove('is-saving');
                    resetHolidayForm();
                });
            }

            const dateTriggers = root.querySelectorAll('[data-date-trigger]');
            const MINI_CAL_MONTHS = ['January', 'February', 'March', 'April', 'May',
                'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ];
            let miniCalEl = null;
            let miniCalView = {
                year: new Date().getFullYear(),
                month: new Date().getMonth(),
            };
            let activeDateTrigger = null;

            const formatISO = (year, month, day) =>
                `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

            const isoToDate = (iso) => {
                if (!iso) return null;
                const [y, m, d] = String(iso).split('-').map(Number);
                if (!y || !m || !d) return null;
                const dt = new Date(y, m - 1, d);
                return Number.isNaN(dt.getTime()) ? null : dt;
            };

            const getInlineRangeValues = () => {
                const start = root.querySelector(
                    '#dashboard-manage-availability-range input[name="manage_availability_start_date"]'
                );
                const end = root.querySelector(
                    '#dashboard-manage-availability-range input[name="manage_availability_end_date"]'
                );
                return {
                    start: start?.value || '',
                    end: end?.value || '',
                };
            };

            const renderMiniCalendar = () => {
                if (!miniCalEl) return;
                const grid = miniCalEl.querySelector('[data-mini-cal-grid]');
                const titleEl = miniCalEl.querySelector('[data-mini-cal-title]');
                titleEl.textContent =
                    `${MINI_CAL_MONTHS[miniCalView.month]} ${miniCalView.year}`;
                grid.innerHTML = '';
                const type = activeDateTrigger?.dataset.dateTrigger;
                const range = getInlineRangeValues();
                const selectedISO = type === 'from' ? range.start : range.end;
                const otherISO = type === 'from' ? range.end : range.start;
                const otherDate = isoToDate(otherISO);
                const firstDay = new Date(miniCalView.year, miniCalView.month, 1);
                const startWeekday = (firstDay.getDay() + 6) % 7;
                const daysInMonth = new Date(miniCalView.year, miniCalView.month + 1, 0)
                    .getDate();
                const prevMonthDays = new Date(miniCalView.year, miniCalView.month, 0)
                    .getDate();

                const appendDay = (year, month, day, isOtherMonth) => {
                    const cell = document.createElement('button');
                    cell.type = 'button';
                    cell.className = 'ma-mini-cal__day';
                    if (isOtherMonth) cell.classList.add('is-other-month');
                    const label = document.createElement('span');
                    label.className = 'ma-mini-cal__day-label';
                    label.textContent = day;
                    cell.appendChild(label);
                    const iso = formatISO(year, month, day);
                    cell.dataset.date = iso;
                    if (iso === selectedISO) cell.classList.add('is-selected');
                    if (otherDate) {
                        const thisDate = new Date(year, month, day);
                        if (type === 'from' && thisDate > otherDate) {
                            cell.classList.add('is-disabled');
                        } else if (type === 'to' && thisDate < otherDate) {
                            cell.classList.add('is-disabled');
                        }
                    }
                    grid.appendChild(cell);
                };

                for (let i = startWeekday - 1; i >= 0; i -= 1) {
                    const d = prevMonthDays - i;
                    let py = miniCalView.year;
                    let pm = miniCalView.month - 1;
                    if (pm < 0) {
                        pm = 11;
                        py -= 1;
                    }
                    appendDay(py, pm, d, true);
                }
                for (let d = 1; d <= daysInMonth; d += 1) {
                    appendDay(miniCalView.year, miniCalView.month, d, false);
                }
                const totalCells = startWeekday + daysInMonth;
                const remainder = totalCells % 7;
                if (remainder !== 0) {
                    const fill = 7 - remainder;
                    let ny = miniCalView.year;
                    let nm = miniCalView.month + 1;
                    if (nm > 11) {
                        nm = 0;
                        ny += 1;
                    }
                    for (let d = 1; d <= fill; d += 1) appendDay(ny, nm, d, true);
                }
            };

            const buildMiniCalendar = () => {
                const el = document.createElement('div');
                el.className = 'ma-mini-cal';
                el.innerHTML = `
                    <div class="ma-mini-cal__header">
                        <button type="button" class="ma-mini-cal__nav" data-mini-cal-prev aria-label="Previous month">&lsaquo;</button>
                        <span class="ma-mini-cal__title" data-mini-cal-title></span>
                        <button type="button" class="ma-mini-cal__nav" data-mini-cal-next aria-label="Next month">&rsaquo;</button>
                    </div>
                    <div class="ma-mini-cal__weekdays">
                        <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                    </div>
                    <div class="ma-mini-cal__grid" data-mini-cal-grid></div>
                `;
                el.addEventListener('click', (event) => event.stopPropagation());
                el.querySelector('[data-mini-cal-prev]').addEventListener('click', () => {
                    miniCalView.month -= 1;
                    if (miniCalView.month < 0) {
                        miniCalView.month = 11;
                        miniCalView.year -= 1;
                    }
                    renderMiniCalendar();
                });
                el.querySelector('[data-mini-cal-next]').addEventListener('click', () => {
                    miniCalView.month += 1;
                    if (miniCalView.month > 11) {
                        miniCalView.month = 0;
                        miniCalView.year += 1;
                    }
                    renderMiniCalendar();
                });
                el.querySelector('[data-mini-cal-grid]').addEventListener('click', (event) => {
                    const day = event.target.closest('.ma-mini-cal__day');
                    if (!day || day.classList.contains('is-disabled')) return;
                    const iso = day.dataset.date;
                    if (iso) selectMiniCalDate(iso);
                });
                document.body.appendChild(el);
                return el;
            };

            const ensureMiniCalendar = () => {
                if (miniCalEl) return miniCalEl;
                miniCalEl = buildMiniCalendar();
                return miniCalEl;
            };

            const positionMiniCalendar = (anchor) => {
                if (!miniCalEl) return;
                const target = anchor.querySelector('.ma-field-value') || anchor;
                const rect = target.getBoundingClientRect();
                miniCalEl.style.width = `${rect.width}px`;
                miniCalEl.style.top = `${rect.bottom + 6}px`;
                const width = rect.width;
                let left = rect.left;
                if (left + width > window.innerWidth - 8) {
                    left = window.innerWidth - width - 8;
                }
                if (left < 8) left = 8;
                miniCalEl.style.left = `${left}px`;
            };

            const closeMiniCalendar = () => {
                if (!miniCalEl) return;
                miniCalEl.classList.remove('is-open');
                if (activeDateTrigger) {
                    activeDateTrigger.classList.remove('is-active');
                    activeDateTrigger = null;
                }
            };

            const openMiniCalendar = (trigger) => {
                const el = ensureMiniCalendar();
                const switchingAnchorWhileOpen =
                    el.classList.contains('is-open') &&
                    activeDateTrigger !== null &&
                    activeDateTrigger !== trigger;

                if (activeDateTrigger && activeDateTrigger !== trigger) {
                    activeDateTrigger.classList.remove('is-active');
                }
                activeDateTrigger = trigger;
                trigger.classList.add('is-active');
                const type = trigger.dataset.dateTrigger;
                const range = getInlineRangeValues();
                const currentISO = type === 'from' ? range.start : range.end;
                const seed = isoToDate(currentISO) || new Date();
                miniCalView.year = seed.getFullYear();
                miniCalView.month = seed.getMonth();
                renderMiniCalendar();

                if (switchingAnchorWhileOpen) {
                    el.classList.add('ma-mini-cal--instant');
                    el.classList.remove('is-open');
                    void el.offsetWidth;
                    el.classList.remove('ma-mini-cal--instant');
                }

                positionMiniCalendar(trigger);
                void el.offsetWidth;
                el.classList.add('is-open');
            };

            const selectMiniCalDate = (iso) => {
                if (!activeDateTrigger) return;
                const type = activeDateTrigger.dataset.dateTrigger;
                const range = getInlineRangeValues();
                const detail = {
                    componentId: 'dashboard-manage-availability-range',
                    start: type === 'from' ? iso : range.start,
                    end: type === 'to' ? iso : range.end,
                };
                window.dispatchEvent(new CustomEvent('range-calendar-set', {
                    detail,
                }));
                closeMiniCalendar();
            };

            if (dateTriggers.length && !root.dataset.dateTriggersBound) {
                root.dataset.dateTriggersBound = '1';
                dateTriggers.forEach((trigger) => {
                    trigger.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        if (activeDateTrigger === trigger) {
                            closeMiniCalendar();
                        } else {
                            closeTimeDropdown();
                            openMiniCalendar(trigger);
                        }
                    });
                });

                document.addEventListener('click', (event) => {
                    if (!miniCalEl || !miniCalEl.classList.contains('is-open')) return;
                    if (event.target.closest('.ma-mini-cal')) return;
                    if (event.target.closest('[data-date-trigger]')) return;
                    closeMiniCalendar();
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') closeMiniCalendar();
                });

                window.addEventListener('scroll', (event) => {
                    if (!activeDateTrigger) return;
                    const target = event.target;
                    if (target && target.nodeType === 1 && target.closest &&
                        target.closest('.ma-mini-cal')) return;
                    positionMiniCalendar(activeDateTrigger);
                }, true);

                window.addEventListener('resize', () => {
                    if (activeDateTrigger) positionMiniCalendar(activeDateTrigger);
                });
            }

            const calendarRoot = document.getElementById('dashboard-manage-availability-range');
            const dateFromInput = document.getElementById('manage-availability-date-from');
            const dateToInput = document.getElementById('manage-availability-date-to');

            if (!calendarRoot || !dateFromInput || !dateToInput) {
                return;
            }

            const startHiddenInput = calendarRoot.querySelector('input[name="manage_availability_start_date"]');
            const endHiddenInput = calendarRoot.querySelector('input[name="manage_availability_end_date"]');

            if (!startHiddenInput || !endHiddenInput) {
                return;
            }

            root.dataset.rangeSyncBound = '1';

            const formatDate = (isoDate) => {
                if (!isoDate) return '';

                const [year, month, day] = String(isoDate).split('-').map(Number);
                if (!year || !month || !day) return '';

                const date = new Date(year, month - 1, day);
                if (Number.isNaN(date.getTime())) return '';

                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                });
            };

            const syncRangeFields = () => {
                dateFromInput.textContent = formatDate(startHiddenInput.value);
                dateToInput.textContent = formatDate(endHiddenInput.value);
            };

            syncRangeFields();
            setTimeout(syncRangeFields, 0);

            window.addEventListener('range-calendar-changed', (event) => {
                const detail = event?.detail || {};
                if (detail.componentId && detail.componentId !==
                    'dashboard-manage-availability-range') {
                    return;
                }

                dateFromInput.textContent = formatDate(detail.start ?? startHiddenInput.value);
                dateToInput.textContent = formatDate(detail.end ?? endHiddenInput.value);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupManageAvailability);
        } else {
            setupManageAvailability();
        }

        document.addEventListener('livewire:navigated', setupManageAvailability);
    })();
</script>