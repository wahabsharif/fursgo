@php
    use App\Models\GroomerSpacerProfile;
    use App\Models\Staff;

    $gsp = auth('groomer_spacer')->user();
    if (!$gsp instanceof GroomerSpacerProfile && auth()->check()) {
        $gsp = GroomerSpacerProfile::where('email', auth()->user()->email)->first();
    }

    $maAccentColor = strtolower((string) ($gsp?->user_type ?? (auth()->user()?->user_type ?? ''))) === 'space' ? '#FFA899' : '#FFC97A';

    $staffMembers = $gsp instanceof GroomerSpacerProfile ? Staff::listedForProfile($gsp) : collect();

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
    $maTodayDisplay = now()->format('d/m/Y');

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

    $maHoursLabel = static function (string $start, string $end, bool $status): string {
        if (!$status) {
            return '';
        }
        $startParts = explode(':', $start);
        $endParts = explode(':', $end);
        $startMinutes = (int) ($startParts[0] ?? 0) * 60 + (int) ($startParts[1] ?? 0);
        $endMinutes = (int) ($endParts[0] ?? 0) * 60 + (int) ($endParts[1] ?? 0);
        $diff = $endMinutes - $startMinutes;
        if ($diff <= 0) {
            return '0h';
        }
        $hours = $diff / 60;

        return fmod($hours, 1.0) === 0.0 ? ((int) $hours) . 'h' : rtrim(rtrim(number_format($hours, 1, '.', ''), '0'), '.') . 'h';
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

    $maDayShort = [
        'monday' => 'Mon',
        'tuesday' => 'Tue',
        'wednesday' => 'Wed',
        'thursday' => 'Thu',
        'friday' => 'Fri',
        'saturday' => 'Sat',
        'sunday' => 'Sun',
    ];

    $maScheduleSummary = static function (array $hours) use ($maDayKeys, $maDayShort): string {
        $active = [];
        foreach ($maDayKeys as $key) {
            if (!empty($hours[$key]['status'])) {
                $active[] = $key;
            }
        }
        if ($active === []) {
            return 'No working hours';
        }
        $first = $active[0];
        $last = $active[count($active) - 1];
        $start = (string) ($hours[$first]['start'] ?? '08:00');
        $end = (string) ($hours[$first]['end'] ?? '16:00');
        $range = $maDayShort[$first] ?? $first;
        if ($first !== $last) {
            $range .= '–' . ($maDayShort[$last] ?? $last);
        }

        return $range . ' (' . substr($start, 0, 5) . '–' . substr($end, 0, 5) . ')';
    };

    $maBasics = is_array($gsp?->business_basics) ? $gsp->business_basics : [];
    $maBusinessName = trim((string) ($maBasics['business_name'] ?? '')) ?: (trim((string) ($gsp?->full_name ?? '')) ?: 'Business Account');
    $maOwnerStaff = $staffMembers->first();
    $maTeamStaff = $staffMembers->slice(1)->values();
@endphp

<div {{ $attributes->merge(['class' => 'dashboard-section-host']) }} x-data="{
    extraStaff: [],
    maView: 'list',
    scheduleTitle: @js($maBusinessName),
    scheduleSubtitle: 'Editing business schedule',
    openSchedule(staffId, title, isBusiness) {
        this.maView = 'schedule';
        this.scheduleTitle = title || 'Schedule';
        this.scheduleSubtitle = isBusiness ? 'Editing business schedule' : 'Editing staff schedule';
        window.dispatchEvent(new CustomEvent('ma-view-changed', {
            detail: { view: 'schedule', title: this.scheduleTitle, subtitle: this.scheduleSubtitle },
        }));
        this.$nextTick(() => {
            const pill = this.$el.querySelector(`.ma-staff-pill[data-staff-id='${staffId}']`);
            if (pill) pill.click();
        });
    },
    backToList() {
        this.maView = 'list';
        window.dispatchEvent(new CustomEvent('ma-view-changed', { detail: { view: 'list' } }));
    },
}"
    x-init="$nextTick(() => window.dispatchEvent(new CustomEvent('ma-view-changed', { detail: { view: 'list' } })))"
    @ma-back-to-list.window="backToList()"
    @staff-added.window="(() => {
        const payload = {
            id: $event.detail.id,
            name: $event.detail.name,
            job_title: $event.detail.job_title || '',
            email: $event.detail.email || '',
            image_url: $event.detail.image_url || null,
            imageFailed: false,
            initial: (() => {
                const name = String($event.detail.name || '').trim();
                const parts = name.split(/\s+/).filter(Boolean);
                if ($event.detail.initial) return $event.detail.initial;
                if (!parts.length) return '??';
                if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
                return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
            })(),
            schedule_summary: $event.detail.schedule_summary || 'No working hours',
            status: 'Active',
            staff_payload: $event.detail.staff_payload || null,
        };
        extraStaff.push(payload);
        maView = 'list';
        window.dispatchEvent(new CustomEvent('ma-view-changed', { detail: { view: 'list' } }));
    })()">
    <section class="ma-board" aria-label="Manage availability board" style="--ma-accent: {{ $maAccentColor }};"
        data-ma-accent="{{ $maAccentColor }}">

        <div class="ma-list" x-show="maView === 'list'" x-cloak>
            <header class="ma-list-header">
                <div>
                    <h2 class="ma-list-title">Manage Availability</h2>
                    <p class="ma-list-subtitle">Set your working hours, days off, and staff schedules.</p>
                </div>
                <button type="button" class="ma-list-add-btn" @click="$dispatch('open-add-staff-modal')">
                    <img src="{{ asset('images/business-hub/icon-add-member.svg') }}" alt="" width="13" height="12" />
                    Add Member
                </button>
            </header>

            <p class="ma-list-section-label">Core Business</p>
            @if ($maOwnerStaff)
                @php
                    $ownerHours = $maNormalizeWorkingHours($maOwnerStaff->working_hours);
                    $ownerSummary = $maScheduleSummary($ownerHours);
                    $ownerPaused = (bool) ($maOwnerStaff->pause_booking ?? false);
                    $ownerInitial = \App\Support\BusinessHubAvatar::initials($maBusinessName);
                    $ownerRawImage = trim((string) ($maOwnerStaff->image ?? ''));
                    $ownerImageUrl = null;
                    if ($ownerRawImage !== '') {
                        $ownerImageUrl = preg_match('#^https?://#i', $ownerRawImage) ? $ownerRawImage : asset('storage/' . ltrim($ownerRawImage, '/'));
                    }
                @endphp
                <button type="button" class="ma-entity-card is-business"
                    @click="openSchedule({{ $maOwnerStaff->id }}, @js($maBusinessName), true)">
                    <span class="ma-entity-avatar {{ $ownerImageUrl ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                        @if ($ownerImageUrl)
                            <img src="{{ $ownerImageUrl }}" alt="{{ $maBusinessName }}"
                                onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                            <span data-bh-avatar-fallback hidden>{{ $ownerInitial }}</span>
                        @else
                            <span data-bh-avatar-fallback>{{ $ownerInitial }}</span>
                        @endif
                    </span>
                    <span class="ma-entity-meta">
                        <strong>{{ $maBusinessName }}</strong>
                        <small>Business Account · {{ $ownerSummary }}</small>
                    </span>
                    <span class="ma-entity-status {{ $ownerPaused ? 'is-away' : 'is-active' }}">
                        {{ $ownerPaused ? 'Away' : 'Active' }}
                    </span>
                    <span class="ma-entity-chevron" aria-hidden="true">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none">
                            <path d="M1 1l6 6-6 6" stroke="#9D9B98" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            @endif

            <p class="ma-list-section-label">Team members</p>
            <div class="ma-team-list">
                @forelse ($maTeamStaff as $member)
                    @php
                        $memberHours = $maNormalizeWorkingHours($member->working_hours);
                        $memberSummary = $maScheduleSummary($memberHours);
                        $memberPaused = (bool) ($member->pause_booking ?? false);
                        $memberInitial = \App\Support\BusinessHubAvatar::initials($member->name);
                        $memberRawImage = trim((string) ($member->image ?? ''));
                        $memberImageUrl = null;
                        if ($memberRawImage !== '') {
                            $memberImageUrl = preg_match('#^https?://#i', $memberRawImage) ? $memberRawImage : asset('storage/' . ltrim($memberRawImage, '/'));
                        }
                        $memberJob = trim((string) ($member->job_title ?? '')) ?: 'Team Member';
                    @endphp
                    <button type="button" class="ma-entity-card"
                        @click="openSchedule({{ $member->id }}, @js($member->name), false)">
                        <span class="ma-entity-avatar {{ $memberImageUrl ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                            @if ($memberImageUrl)
                                <img src="{{ $memberImageUrl }}" alt="{{ $member->name }}"
                                    onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                                <span data-bh-avatar-fallback hidden>{{ $memberInitial }}</span>
                            @else
                                <span data-bh-avatar-fallback>{{ $memberInitial }}</span>
                            @endif
                        </span>
                        <span class="ma-entity-meta">
                            <strong>{{ $member->name }}</strong>
                            <small>{{ $memberJob }} · {{ $memberSummary }}</small>
                        </span>
                        <span class="ma-entity-status {{ $memberPaused ? 'is-away' : 'is-active' }}">
                            {{ $memberPaused ? 'Away' : 'Active' }}
                        </span>
                        <span class="ma-entity-chevron" aria-hidden="true">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none">
                                <path d="M1 1l6 6-6 6" stroke="#9D9B98" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                @empty
                    <p class="ma-team-empty" x-show="extraStaff.length === 0">No team members yet. Add a member to manage their schedule.</p>
                @endforelse

                <template x-for="staff in extraStaff" :key="'list-' + staff.id">
                    <button type="button" class="ma-entity-card"
                        @click="openSchedule(staff.id, staff.name, false)">
                        <span class="ma-entity-avatar" data-bh-avatar
                            :class="{ 'has-photo': !!staff.image_url && !staff.imageFailed, 'is-fallback': !staff.image_url || staff.imageFailed }">
                            <template x-if="staff.image_url && !staff.imageFailed">
                                <img :src="staff.image_url" :alt="staff.name" x-on:error="staff.imageFailed = true">
                            </template>
                            <template x-if="!staff.image_url || staff.imageFailed">
                                <span x-text="staff.initial"></span>
                            </template>
                        </span>
                        <span class="ma-entity-meta">
                            <strong x-text="staff.name"></strong>
                            <small x-text="(staff.job_title || 'Team Member') + ' · ' + (staff.schedule_summary || 'No working hours')"></small>
                        </span>
                        <span class="ma-entity-status is-active">Active</span>
                        <span class="ma-entity-chevron" aria-hidden="true">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="none">
                                <path d="M1 1l6 6-6 6" stroke="#9D9B98" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </button>
                </template>
            </div>
        </div>

        <div class="ma-schedule" x-show="maView === 'schedule'" x-cloak>
            <div class="ma-schedule-head">
                <button type="button" class="ma-back-link" @click="backToList()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none" aria-hidden="true">
                        <path d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z" fill="#9D9B98" />
                    </svg>
                    Back to All availability
                </button>
                <div class="ma-schedule-titles">
                    <h2 class="ma-schedule-title" data-staff-name-target x-text="scheduleTitle">{{ $maBusinessName }}</h2>
                    <p class="ma-schedule-subtitle" data-schedule-subtitle x-text="scheduleSubtitle">Editing business schedule</p>
                </div>
            </div>

            <div class="ma-staff-strip">
                <div class="ma-staff-list">
                    @foreach ($staffMembers as $staff)
                        @php
                            $initial = \App\Support\BusinessHubAvatar::initials($staff->name);
                            $rawImage = trim((string) ($staff->image ?? ''));
                            $imageUrl = null;
                            if ($rawImage !== '') {
                                $imageUrl = preg_match('#^https?://#i', $rawImage) ? $rawImage : asset('storage/' . ltrim($rawImage, '/'));
                            }
                        @endphp
                        <button type="button" class="ma-staff-pill {{ $loop->first ? 'ma-staff-pill--active' : '' }}"
                            data-staff-id="{{ $staff->id }}" data-staff-name="{{ $staff->name }}"
                            data-display-title="{{ $loop->first ? $maBusinessName : $staff->name }}"
                            data-is-business="{{ $loop->first ? '1' : '0' }}"
                            data-staff-job-title="{{ $staff->job_title }}"
                            data-staff-payload="{{ json_encode($maStaffPayload($staff)) }}">
                            <span class="ma-staff-avatar {{ $imageUrl ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $staff->name }}"
                                        onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                                    <span data-bh-avatar-fallback hidden>{{ $initial }}</span>
                                @else
                                    <span data-bh-avatar-fallback>{{ $initial }}</span>
                                @endif
                            </span>
                            <span class="ma-staff-pill__meta">
                                <strong>{{ $staff->name }}</strong>
                                <small>{{ $staff->job_title }}</small>
                            </span>
                        </button>
                    @endforeach

                    <template x-for="staff in extraStaff" :key="staff.id">
                        <button type="button" class="ma-staff-pill" :data-staff-id="staff.id" :data-staff-name="staff.name"
                            :data-display-title="staff.name" data-is-business="0"
                            :data-staff-job-title="staff.job_title"
                            :data-staff-payload="staff.staff_payload ? JSON.stringify(staff.staff_payload) : ''">
                            <span class="ma-staff-avatar" data-bh-avatar
                                :class="{ 'has-photo': !!staff.image_url && !staff.imageFailed, 'is-fallback': !staff.image_url || staff.imageFailed }">
                                <template x-if="staff.image_url && !staff.imageFailed">
                                    <img :src="staff.image_url" :alt="staff.name"
                                        x-on:error="staff.imageFailed = true">
                                </template>
                                <template x-if="!staff.image_url || staff.imageFailed">
                                    <span x-text="staff.initial"></span>
                                </template>
                            </span>
                            <span class="ma-staff-pill__meta">
                                <strong x-text="staff.name"></strong>
                                <small x-text="staff.job_title"></small>
                            </span>
                        </button>
                    </template>

                    <button type="button" class="ma-staff-add" aria-label="Add staff"
                        @click="$dispatch('open-add-staff-modal')">
                        <img src="{{ asset('images/business-hub/icon-add-member.svg') }}" alt="" width="13" height="12" />
                    </button>
                </div>
            </div>

            <div class="ma-section ma-card--pause">
                <div class="ma-pause-row{{ $activePauseBooking ? ' is-paused' : '' }}" data-pause-row>
                    <div class="ma-pause-copy">
                        <strong data-pause-title>
                            @if ($activePauseBooking)
                                New bookings paused
                            @else
                                Pause new bookings <small>(effective today)</small>
                            @endif
                        </strong>
                        <p data-pause-copy>
                            @if ($activePauseBooking)
                                Clients can still see your profile, but can't request new appointments. Existing bookings are unaffected.
                            @else
                                Clients are currently able to request appointments in your open slots.
                            @endif
                        </p>
                    </div>
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

            <div class="ma-section ma-card ma-card--week">
                <div class="ma-title-row">
                    <h3>Work Week Hours</h3>
                </div>

                <div class="ma-week-grid">
                    <div class="ma-week-row ma-week-row--head">
                        <div class="ma-week-grid__head ma-week-col ma-week-col--day">Working week</div>
                        <div class="ma-week-grid__head ma-week-col ma-week-col--time">Start — end time</div>
                        <div class="ma-week-grid__head ma-week-col ma-week-col--hours">Hours</div>
                        <div class="ma-week-grid__head ma-week-col ma-week-col--edit">Edit</div>
                    </div>

                    @foreach ($maDayLabels as $dayKey => $dayLabel)
                        @php
                            $entry = $activeWorkingHours[$dayKey];
                            $dayStatus = $entry['status'];
                            $dayStart = $entry['start'];
                            $dayEnd = $entry['end'];
                        @endphp
                        <div class="ma-week-row">
                            <div class="ma-cell ma-week-col ma-week-col--day {{ $dayStatus ? '' : 'is-disabled' }}"
                                data-day="{{ $dayKey }}" data-day-cell="name">
                                <div class="ma-day-name">
                                    <span>{{ $dayLabel }}</span>
                                    <label class="ma-switch" style="height: 24px;">
                                        <input type="checkbox" data-day-status {{ $dayStatus ? 'checked' : '' }}>
                                        <span class="ma-switch-slider"></span>
                                        <span class="ma-switch-check-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 20 20" fill="none">
                                                <path
                                                    d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                                    fill="white" />
                                            </svg>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="ma-cell ma-time-range ma-week-col ma-week-col--time {{ $dayStatus ? '' : 'is-unavailable is-disabled' }}"
                                data-day="{{ $dayKey }}" data-day-cell="time">
                                <span class="ma-time-chip" data-time-type="start" data-time-value="{{ $dayStart }}"
                                    role="button" tabindex="0">
                                    <svg width="17" height="17" viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span data-time-start>{{ $maFormatTime($dayStart) }}</span>
                                </span>
                                <span class="ma-time-separator" aria-hidden="true">—</span>
                                <span class="ma-time-chip" data-time-type="end" data-time-value="{{ $dayEnd }}" role="button"
                                    tabindex="0">
                                    <svg width="17" height="17" viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span data-time-end>{{ $maFormatTime($dayEnd) }}</span>
                                </span>
                                <span class="ma-time-unavailable">Unavailable - not bookable</span>
                            </div>
                            <div class="ma-cell ma-hours-cell ma-week-col ma-week-col--hours {{ $dayStatus ? '' : 'is-disabled' }}"
                                data-day="{{ $dayKey }}" data-day-cell="hours">
                                <span data-day-hours>{{ $maHoursLabel($dayStart, $dayEnd, $dayStatus) }}</span>
                            </div>
                            <div class="ma-cell ma-edit-cell ma-week-col ma-week-col--edit {{ $dayStatus ? '' : 'is-disabled' }}"
                                data-day="{{ $dayKey }}" data-day-cell="edit">
                                <button type="button" class="ma-save-mini" data-day-action="save">
                                    <span class="ma-save-mini__label" data-save-label>Save</span>
                                    <span class="ma-save-spinner" data-save-spinner aria-hidden="true"></span>
                                </button>
                                <button type="button" class="ma-row-action" data-day-action="edit" aria-label="Edit day slot">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                        <circle cx="18" cy="18" r="17.5" fill="white" stroke="#E2E2E2" />
                                        <path d="M20.0588 13.7457L22.5294 16.1573M18.4118 25H25M11.8235 21.7845L11 25L14.2941 24.1961L23.8355 14.8824C24.1443 14.5809 24.3178 14.172 24.3178 13.7457C24.3178 13.3194 24.1443 12.9105 23.8355 12.609L23.6939 12.4707C23.385 12.1693 22.9662 12 22.5294 12C22.0927 12 21.6738 12.1693 21.3649 12.4707L11.8235 21.7845Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="ma-section ma-card ma-card--timeoff"
                x-data="{
                    holidayModalOpen: false,
                    holidayMode: 'all-day',
                    openHolidayModal() {
                        this.holidayModalOpen = true;
                        this.$dispatch('ma-open-holiday-modal');
                    },
                    closeHolidayModal() {
                        this.holidayModalOpen = false;
                        this.$dispatch('ma-close-holiday-modal');
                    }
                }"
                @ma-open-holiday-modal.window="holidayModalOpen = true"
                @ma-close-holiday-modal.window="holidayModalOpen = false"
                @keydown.escape.window="if (holidayModalOpen) closeHolidayModal()">
                <h3>Business time off</h3>
                <button type="button" class="ma-add-time-off-dashed" @click="openHolidayModal()">
                    <img src="{{ asset('images/business-hub/icon-add-time-off-plus.svg') }}" alt="" width="11" height="11" />
                    Add Time Off
                </button>

                <livewire:business-hub.availability.holiday-list :staff-id="$activeStaff?->id" />

                {{-- Hidden range calendar (feeds date fields / JS) --}}
                <div class="ma-holiday-calendar-host" aria-hidden="true">
                    <x-ui.range-date-calendar id="dashboard-manage-availability-range"
                        start-name="manage_availability_start_date" end-name="manage_availability_end_date"
                        start-value="{{ $maTodayIso }}" end-value="{{ $maTodayIso }}" calendar-width="100%" />
                </div>

                <template x-teleport="body">
                    <div class="ma-holiday-modal-overlay" :class="{ 'is-open': holidayModalOpen }"
                        :aria-hidden="holidayModalOpen ? 'false' : 'true'"
                        @click.self="closeHolidayModal()">
                        <div class="ma-holiday-modal" role="dialog" aria-modal="true"
                            aria-labelledby="ma-holiday-modal-title">
                            <div class="ma-holiday-modal__head">
                                <h4 id="ma-holiday-modal-title">Add business time off</h4>
                                <button type="button" class="ma-holiday-modal__close" @click="closeHolidayModal()"
                                    aria-label="Close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <circle cx="10" cy="10" r="9.5" fill="#F6F6F6" stroke="#E2E2E2" />
                                        <path d="M6.5 6.5L13.5 13.5M13.5 6.5L6.5 13.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="ma-holiday-modal__body">
                                <div class="ma-holiday-form">
                                    <div class="ma-inline-fields">
                                        <div class="ma-field" data-date-trigger="from" role="button" tabindex="0">
                                            <span>Date from</span>
                                            <span class="ma-field-value">
                                                <span id="manage-availability-date-from">{{ $maTodayDisplay }}</span>
                                                <span class="ma-field-value__icon" aria-hidden="true">
                                                    <img src="{{ asset('images/business-hub/icon-holiday-calendar.svg') }}" alt="">
                                                </span>
                                            </span>
                                        </div>
                                        <div class="ma-field" data-date-trigger="to" role="button" tabindex="0">
                                            <span>Date to</span>
                                            <span class="ma-field-value">
                                                <span id="manage-availability-date-to">{{ $maTodayDisplay }}</span>
                                                <span class="ma-field-value__icon" aria-hidden="true">
                                                    <img src="{{ asset('images/business-hub/icon-holiday-calendar.svg') }}" alt="">
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="ma-field ma-field--duration">
                                        <span>Duration</span>
                                        <div class="ma-holiday-mode" role="tablist" aria-label="Time off duration">
                                            <button type="button" class="ma-holiday-mode__btn" role="tab"
                                                :aria-selected="holidayMode === 'all-day'"
                                                :class="{ 'is-active': holidayMode === 'all-day' }"
                                                @click="holidayMode = 'all-day'">All day</button>
                                            <button type="button" class="ma-holiday-mode__btn" role="tab"
                                                :aria-selected="holidayMode === 'hours'"
                                                :class="{ 'is-active': holidayMode === 'hours' }"
                                                @click="holidayMode = 'hours'">Specific Hours</button>
                                        </div>
                                        <div class="ma-holiday-hours" :class="{ 'is-open': holidayMode === 'hours' }"
                                            :aria-hidden="holidayMode === 'hours' ? 'false' : 'true'">
                                            <div class="ma-holiday-hours__inner">
                                                <input type="text" class="ma-holiday-time ma-time-chip"
                                                    data-holiday-time data-time-type="start" data-time-value="07:00"
                                                    data-time-start value="07:00 AM" inputmode="numeric"
                                                    autocomplete="off" spellcheck="false" aria-label="Start time" />
                                                <span class="ma-time-separator" aria-hidden="true">—</span>
                                                <input type="text" class="ma-holiday-time ma-time-chip"
                                                    data-holiday-time data-time-type="end" data-time-value="14:00"
                                                    data-time-end value="14:00 PM" inputmode="numeric"
                                                    autocomplete="off" spellcheck="false" aria-label="End time" />
                                            </div>
                                        </div>
                                    </div>

                                    <label class="ma-field">
                                        <span>Reason <span class="ma-field__optional">(optional)</span></span>
                                        <textarea rows="3" data-holiday-reason placeholder=""></textarea>
                                    </label>
                                    <div class="ma-form-actions">
                                        <button type="button" class="ma-btn ma-btn-light"
                                            @click="closeHolidayModal()">Cancel</button>
                                        <button type="button" class="ma-add-time-off" data-holiday-action="save">
                                            <span class="ma-save-mini__label" data-save-label>
                                                <img src="{{ asset('images/business-hub/icon-add-time-off-plus.svg') }}" alt="" width="11" height="11" />
                                                Add Time Off
                                            </span>
                                            <span class="ma-save-spinner" data-save-spinner aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="ma-save-bar" data-save-bar>
                <p class="ma-save-bar__status" data-save-status>All changes saved <span>· just now</span></p>
                <div class="ma-footer-actions">
                    <button type="button" class="ma-btn ma-btn-light" data-ma-cancel disabled>Cancel</button>
                    <button type="button" class="ma-btn ma-btn-primary" data-ma-save-changes disabled>Save Changes</button>
                </div>
            </div>
        </div>{{-- /.ma-schedule --}}

        {{-- Outside .ma-schedule so Livewire roots don't inflate staff→pause gap --}}
        <div class="ma-livewire-hosts">
            <livewire:business-hub.availability.add-staff-modal />
            <livewire:business-hub.availability.staff-actions />
        </div>
    </section>
</div>

<style>
    /* Figma 443:899 — list landing */
    .ma-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .ma-list-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .ma-list-title {
        margin: 0;
        color: #3b3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-list-subtitle {
        margin: 8px 0 0;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .ma-list-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 154px;
        height: 48px;
        padding: 0 22px;
        border: 0;
        border-radius: 100px;
        background: #3b3731;
        color: #fdfdfd;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .ma-list-add-btn img {
        display: block;
        width: 13px;
        height: 12px;
        flex-shrink: 0;
    }

    .ma-list-add-btn:hover {
        background: #2a2722;
    }

    .ma-list-section-label {
        margin: 12px 0 0;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .ma-list-section-label+.ma-entity-card,
    .ma-list-section-label+.ma-team-list {
        margin-top: 10px;
    }

    .ma-team-list {
        display: flex;
        flex-direction: column;
        border: 1px solid #e6e6e6;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .ma-entity-card {
        display: grid;
        grid-template-columns: 56px minmax(0, 1fr) auto 12px;
        align-items: center;
        column-gap: 16px;
        width: 100%;
        min-height: 76px;
        padding: 10px 16px 10px 20px;
        border: 0;
        background: #fff;
        text-align: left;
        cursor: pointer;
        font: inherit;
        color: inherit;
        transition: background-color 0.18s ease;
    }

    .ma-entity-card.is-business {
        border: 1px solid #ffe2b8;
        border-radius: 10px;
        background: rgba(255, 201, 122, 0.05);
    }

    .ma-board[data-ma-accent="#FFA899"] .ma-entity-card.is-business {
        border-color: #ffd0c6;
        background: rgba(255, 168, 153, 0.08);
    }

    .ma-team-list .ma-entity-card {
        border-bottom: 1px solid #e6e6e6;
        border-radius: 0;
    }

    .ma-team-list .ma-entity-card:last-child {
        border-bottom: 0;
    }

    .ma-entity-card:hover {
        background: #fffcf8;
    }

    .ma-entity-card.is-business:hover {
        background: rgba(255, 201, 122, 0.1);
    }

    .ma-entity-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        overflow: hidden;
        display: grid;
        place-items: center;
        background: var(--ma-accent, #ffc97a);
        color: #fdfdfd;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .ma-entity-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ma-entity-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ma-entity-meta strong {
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.25;
    }

    .ma-entity-meta small {
        color: #948f88;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.25;
    }

    .ma-entity-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        height: 28px;
        padding: 0 14px;
        border-radius: 100px;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 500;
        line-height: 1;
        justify-self: end;
    }

    .ma-entity-status.is-active {
        background: #f9fbf4;
        color: #afcd6f;
    }

    .ma-entity-status.is-away {
        background: #fffaf2;
        color: #f9c45c;
    }

    .ma-entity-chevron {
        display: inline-flex;
        line-height: 0;
        justify-self: end;
    }

    .ma-team-empty {
        margin: 0;
        padding: 20px;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 15px;
    }

    .ma-schedule {
        display: flex;
        flex-direction: column;
        gap: 40px;
        width: 100%;
    }

    .ma-schedule-head {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .ma-schedule-titles {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ma-back-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 0;
        background: transparent;
        padding: 0;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.16px;
        cursor: pointer;
    }

    .ma-back-link svg path {
        fill: #9d9b98;
    }

    .ma-schedule-title {
        margin: 0;
        color: #3b3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-schedule-subtitle {
        margin: 0;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .ma-add-time-off-dashed {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 42px;
        margin: 0 0 20px;
        border: 1px dashed #dfd4b7;
        border-radius: 10px;
        background: #f7f4ec;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }

    .ma-add-time-off-dashed img {
        display: block;
        width: 11px;
        height: 11px;
        flex-shrink: 0;
    }

    .ma-add-time-off-dashed:hover {
        background: #f3efe4;
        border-color: #d0c4a4;
    }

    .ma-holiday-calendar-host {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }

    .ma-holiday-modal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        height: 100dvh;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        box-sizing: border-box;
        background: rgba(59, 55, 49, 0.1);
        overscroll-behavior: none;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        will-change: opacity;
        transition: opacity 180ms ease, visibility 0s linear 180ms;
    }

    .ma-holiday-modal-overlay.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity 180ms ease, visibility 0s linear 0s;
    }

    .ma-holiday-modal {
        position: relative;
        width: min(505px, 100%);
        max-height: min(90vh, 560px);
        overflow: visible;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 0 0 24px;
        margin: auto;
        flex-shrink: 0;
        font-family: Lato, sans-serif;
        color: #3b3731;
        transform: translateY(10px) scale(0.97);
        will-change: transform;
        transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ma-holiday-modal-overlay.is-open .ma-holiday-modal {
        transform: translateY(0) scale(1);
    }

    .ma-holiday-modal__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 0;
        padding: 20px 20px 0;
    }

    .ma-holiday-modal__head h4 {
        margin: 0;
        color: #3b3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-holiday-modal__close {
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
        line-height: 0;
        border-radius: 50%;
        transition: transform 160ms ease, opacity 160ms ease;
    }

    .ma-holiday-modal__close:hover {
        opacity: 0.85;
        transform: scale(1.04);
    }

    .ma-holiday-modal__body {
        padding: 20px 20px 0;
    }

    .ma-holiday-modal .ma-holiday-form {
        display: flex;
        flex-direction: column;
        width: 100%;
        gap: 20px;
    }

    .ma-holiday-modal .ma-inline-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .ma-holiday-modal .ma-field>span:first-child {
        display: block;
        margin-bottom: 10px;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .ma-holiday-modal .ma-field__optional {
        color: #9c9790;
        font-weight: 400;
    }

    .ma-holiday-modal .ma-field .ma-field-value {
        height: 42px;
        min-height: 42px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 0 12px 0 10px;
        font-size: 18px;
        font-weight: 400;
        line-height: 25px;
        cursor: pointer;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    .ma-holiday-modal .ma-field .ma-field-value:hover {
        border-color: #cfcfcf;
    }

    .ma-holiday-modal .ma-field textarea {
        border: 1px solid #ddd;
        border-radius: 5px;
        min-height: 87px;
        height: 87px;
        padding: 10px 12px;
        font-size: 16px;
        line-height: 22px;
        resize: none;
        transition: border-color 160ms ease;
    }

    .ma-holiday-modal .ma-field textarea:focus {
        outline: none;
        border-color: var(--ma-accent, #ffc97a);
    }

    .ma-holiday-modal .ma-field--duration {
        display: flex;
        flex-direction: column;
    }

    .ma-holiday-mode {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 0;
        width: 100%;
        height: 42px;
        padding: 3px;
        border: 0;
        border-radius: 100px;
        background: #f9fafc;
        box-sizing: border-box;
    }

    .ma-holiday-mode__btn {
        border: 0;
        background: transparent;
        border-radius: 100px;
        height: 36px;
        color: #888;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: background-color 180ms ease, color 180ms ease, box-shadow 180ms ease;
    }

    .ma-holiday-mode__btn.is-active {
        background: #fff;
        color: var(--ma-accent, #ffc97a);
        font-weight: 700;
        box-shadow: 0 2px 4px 0 rgba(59, 55, 49, 0.1);
    }

    .ma-holiday-hours {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        margin-top: 0;
        transition: grid-template-rows 220ms cubic-bezier(0.16, 1, 0.3, 1),
            opacity 180ms ease,
            margin-top 220ms cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .ma-holiday-hours.is-open {
        grid-template-rows: 1fr;
        opacity: 1;
        margin-top: 10px;
        pointer-events: auto;
    }

    .ma-holiday-hours__inner {
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 0;
    }

    .ma-holiday-time {
        flex: 1 1 0;
        display: block;
        width: auto;
        max-width: none;
        height: 42px;
        min-height: 42px;
        padding: 0 14px;
        border: 1px solid #e6e6e6;
        border-radius: 10px;
        background: #fff;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: 42px;
        box-sizing: border-box;
        cursor: text;
        box-shadow: none;
        outline: none;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    .ma-holiday-time:hover,
    .ma-holiday-time:focus,
    .ma-holiday-time.ma-time-chip--open {
        border-color: var(--ma-accent, #ffc97a);
    }

    .ma-holiday-hours .ma-time-separator {
        flex: 0 0 auto;
        color: #c5c5c5;
        font-size: 14px;
        line-height: 1;
    }

    .ma-holiday-modal .ma-form-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 4px;
    }

    .ma-holiday-modal .ma-form-actions .ma-btn-light {
        width: 100%;
        height: 42px;
        border-radius: 75px;
        border: 1px solid #e2e2e2;
        background: #fff;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        box-shadow: none;
        transition: background-color 160ms ease, border-color 160ms ease, transform 160ms ease;
    }

    .ma-holiday-modal .ma-form-actions .ma-btn-light:hover {
        background: #fafafa;
    }

    .ma-holiday-modal .ma-add-time-off {
        width: 100%;
        min-width: 0;
        height: 42px;
        border-radius: 100px;
        border: 0;
        background: #f7f4ec;
        box-shadow: none;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        transition: background-color 160ms ease, transform 160ms ease;
    }

    .ma-holiday-modal .ma-add-time-off .ma-save-mini__label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .ma-holiday-modal .ma-add-time-off .ma-save-mini__label img {
        display: block;
        width: 11px;
        height: 11px;
        flex-shrink: 0;
    }

    .ma-holiday-modal .ma-add-time-off:hover {
        background: #f0ebe0;
    }

    .ma-holiday-modal .ma-form-actions .ma-btn-light:active,
    .ma-holiday-modal .ma-add-time-off:active {
        transform: translateY(1px);
    }

    .ma-board {
        margin-top: 0;
        width: 100%;
        color: #3B3731;
        font-family: Lato;
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .ma-board>[x-cloak] {
        display: none !important;
    }

    .ma-board>div:not([class]),
    .ma-livewire-hosts {
        height: 0;
        margin: 0;
        overflow: visible;
        pointer-events: none;
        flex: 0 0 0;
    }

    .ma-board>div:not([class])>*,
    .ma-livewire-hosts>* {
        pointer-events: auto;
    }

    .ma-card {
        background: #fdfdfd;
        border: 1px solid #e6e6e6;
        border-radius: 10px;
        box-shadow: none;
        padding: 20px;
        margin-bottom: 0;
    }

    .ma-card--week {
        padding-bottom: 8px;
    }

    .ma-card--timeoff {
        padding-bottom: 8px;
    }

    .ma-holiday-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        justify-content: flex-start;
        height: auto;
    }

    .ma-staff-strip {
        background: #fff;
        border: 1px solid #e2e2e2;
        border-radius: 10px;
        box-shadow: none;
        padding: 10px 14px;
        min-height: 68px;
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }

    .ma-staff-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        width: 100%;
    }

    .ma-staff-pill {
        border: 1px solid transparent;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 6px 14px 6px 6px;
        min-width: 162px;
        min-height: 48px;
        border-radius: 100px;
        position: relative;
        transition: background-color 0.12s ease, border-color 0.12s ease;
    }

    .ma-staff-pill:hover {
        background: #f7f7f7;
    }

    .ma-staff-pill--active {
        background: #fdfaf6;
        border-color: var(--ma-accent, #ffc97a);
    }

    .ma-staff-pill:last-child {
        margin-left: 0;
    }

    .ma-staff-pill::after {
        display: none;
    }

    .ma-staff-avatar {
        width: 36px;
        height: 36px;
        border-radius: 100px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--bh-avatar-bg, #FFC97A);
        color: #FDFDFD;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
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
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-pill small {
        display: block;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-add {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 0;
        background: var(--ma-accent, #FFC97A);
        box-shadow: 0 0 10px 0 rgba(255, 216, 140, 0.55);
        color: #fff;
        cursor: pointer;
        padding: 0;
        flex-shrink: 0;
        margin-left: auto;
    }

    .ma-staff-add img {
        display: block;
        width: 13px;
        height: 12px;
    }

    .ma-staff-add:hover {
        filter: brightness(0.97);
    }

    .ma-section {
        margin-bottom: 0;
    }

    .ma-section h3,
    .ma-title-row h3 {
        color: #3b3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        width: fit-content;
        padding-bottom: 0;
        border-bottom: 0;
        margin: 0 0 20px;
        text-transform: none;
    }

    .ma-title-row h3 {
        margin-bottom: 0;
    }

    .ma-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 0;
        border-bottom: 0;
        margin-bottom: 20px;
    }

    .ma-editing-label {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 19px;
        text-align: right;
    }

    .ma-editing-label__name {
        font-weight: 600;
        padding-bottom: 1px;
    }

    .ma-editing-label__job,
    .ma-editing-label__job span {
        color: #9D9B98;
        font-weight: 400;
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
        display: flex;
        flex-direction: column;
        margin-top: 0;
    }

    .ma-week-row {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 2rem;
        border-bottom: 1px solid #e6e6e6;
        box-sizing: border-box;
    }

    .ma-week-row:last-child {
        border-bottom: 0;
    }

    .ma-week-row--head {
        align-items: flex-end;
        padding: 0 0 20px;
        min-height: 0;
    }

    .ma-week-col--day {
        flex: 0 1 275px;
        width: 275px;
        min-width: 0;
    }

    .ma-week-col--time {
        flex: 0 1 400px;
        width: 400px;
        min-width: 0;
        margin-right: auto;
    }

    .ma-week-col--hours {
        flex: 0 1 147px;
        width: 147px;
        min-width: 48px;
    }

    .ma-week-col--edit {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 74px;
        width: 74px;
    }

    .ma-week-grid__head {
        padding: 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 19px;
        text-transform: uppercase;
        border: 0;
        box-sizing: border-box;
    }

    .ma-cell {
        display: flex;
        align-items: center;
        padding: 20px 0;
        border: 0;
        box-sizing: border-box;
        min-width: 0;
    }

    .ma-day-name {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 275px;
        max-width: 100%;
        min-height: 48px;
        padding: 0 20px;
        border-radius: 10px;
        background: #F7F7F7;
        box-sizing: border-box;
    }

    .ma-day-name span {
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-time-range {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 400px;
        max-width: 100%;
        gap: 0;
    }

    .ma-time-chip {
        width: 190px;
        flex: 1 1 190px;
        max-width: 190px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 0 16px;
        border: 1px solid #e6e6e6;
        border-radius: 10px;
        background: #fff;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        min-width: 0;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        box-sizing: border-box;
        transition: border-color 0.12s ease, background-color 0.12s ease;
    }

    .ma-time-chip.ma-time-chip--open {
        border-color: var(--ma-accent, #ffc97a);
        background: color-mix(in srgb, var(--ma-accent, #ffc97a) 22%, #fff);
    }

    .ma-time-dropdown {
        position: fixed;
        z-index: 10060;
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
        color: #9D9B98;
        flex: 0 0 20px;
        width: 20px;
        height: auto;
        background: none;
        font-size: 16px;
        line-height: 1;
        text-align: center;
    }

    .ma-time-unavailable {
        display: none;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: italic;
        font-weight: 400;
        line-height: normal;
    }

    .ma-time-range.is-unavailable .ma-time-chip,
    .ma-time-range.is-unavailable .ma-time-separator {
        display: none;
    }

    .ma-time-range.is-unavailable .ma-time-unavailable {
        display: flex;
    }

    .ma-time-range.is-disabled {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: italic;
        font-weight: 400;
        line-height: normal;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .ma-time-range.is-disabled .ma-time-chip {
        pointer-events: none;
    }

    .ma-hours-cell {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
        justify-content: flex-start;
    }

    .ma-cell[data-day-cell="name"] .ma-switch {
        cursor: pointer;
    }

    .ma-edit-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
    }

    /* Figma 447:123 — idle = pencil; editing = Save; unavailable = none */
    .ma-edit-cell .ma-save-mini {
        display: none;
    }

    .ma-edit-cell .ma-row-action {
        display: flex;
    }

    .ma-edit-cell.is-editing .ma-save-mini {
        display: flex;
    }

    .ma-edit-cell.is-editing .ma-row-action {
        display: none;
    }

    .ma-edit-cell.is-disabled .ma-save-mini,
    .ma-edit-cell.is-disabled .ma-row-action {
        display: none;
    }

    .ma-time-range:not(.is-editing):not(.is-unavailable) .ma-time-chip {
        border-color: transparent;
        background: transparent;
        cursor: default;
        pointer-events: none;
        font-weight: 600;
    }

    .ma-time-range.is-editing .ma-time-chip {
        border-color: #e6e6e6;
        background: #fff;
        cursor: pointer;
        pointer-events: auto;
        font-weight: 400;
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
        background: #bacf8e;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #fff;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: background-color 0.18s ease, opacity 0.18s ease;
    }

    .ma-save-mini:hover {
        background: #a8c076;
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

    .ma-section.ma-card:has(.ma-holiday-grid) {
        padding: 20px;
    }

    .ma-section.ma-card:has(.ma-holiday-grid)>h3 {
        margin-bottom: 40px;
    }

    .ma-holiday-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
        margin-top: 0;
    }

    .ma-inline-fields {
        margin-top: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .ma-field {
        display: block;
    }

    .ma-field>span:first-child {
        display: flex;
        align-items: baseline;
        gap: 5px;
        margin-bottom: 10px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-field__optional {
        color: #9C9790;
        font-weight: 400;
    }

    .ma-field input,
    .ma-field .ma-field-value,
    .ma-field textarea {
        width: 100%;
        border: 1px solid #DDD;
        border-radius: 5px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        padding: 8px 12px;
        box-sizing: border-box;
    }

    .ma-field .ma-field-value {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        height: 42px;
        min-height: 42px;
        padding: 0 20px 0 10px;
        text-align: left;
        font-weight: 400;
    }

    .ma-field .ma-field-value__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 15px;
        height: 14px;
        overflow: hidden;
        margin-left: auto;
    }

    .ma-field .ma-field-value__icon img {
        display: block;
    }

    .ma-field textarea {
        min-height: 87px;
        height: 87px;
        resize: none;
        padding: 10px 12px;
    }

    .ma-form-actions {
        display: flex;
        justify-content: flex-start;
        margin-top: 0;
    }

    .ma-add-time-off {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 148px;
        min-width: 148px;
        height: 42px;
        padding: 0;
        border: 0;
        border-radius: 100px;
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .ma-add-time-off.is-saving .ma-save-mini__label {
        display: none;
    }

    .ma-add-time-off .ma-save-spinner {
        display: none;
    }

    .ma-add-time-off.is-saving .ma-save-spinner {
        display: inline-block;
    }

    .ma-holiday-calendar {
        border-radius: 10px;
        min-width: 0;
    }

    .ma-holiday-calendar .rdc-top {
        margin-bottom: 10px;
    }

    .ma-holiday-calendar .rdc-nav-circle>svg,
    .ma-holiday-calendar .rdc-nav-inline>svg {
        margin-top: 0;
    }

    .ma-holiday-calendar .rdc-panel {
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        padding: 13px;
        gap: 20px;
    }

    .ma-holiday-calendar .rdc-month-header {
        margin-bottom: 12px;
    }

    .ma-holiday-calendar .rdc-day {
        height: 30px;
    }

    .ma-holiday-calendar .rdc-range-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
    }

    .ma-holiday-list {
        margin-top: 40px;
        padding: 0;
        border-radius: 0;
        background: transparent;
        overflow: visible;
        font-family: Lato;
        color: #3B3731;
    }

    .ma-holiday-list__header,
    .ma-holiday-list__row {
        display: grid;
        grid-template-columns: minmax(220px, 380px) minmax(0, 1fr) 82px;
        align-items: center;
        column-gap: 0;
        padding: 0 20px;
        box-sizing: border-box;
    }

    .ma-holiday-list__header {
        height: 50px;
        background: #F6F5F5;
        border-radius: 10px 10px 0 0;
        color: #948F88;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-holiday-list__header>div,
    .ma-holiday-list__row>div {
        padding: 0;
        display: flex;
        align-items: center;
        border-right: 0;
        min-width: 0;
    }

    .ma-holiday-list__col--edit {
        justify-content: center;
    }

    .ma-holiday-list__row {
        min-height: 56px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        border-top: 0;
        border-bottom: 1px solid #D4D4D4;
    }

    .ma-holiday-list__row:last-child {
        border-bottom: 0;
    }

    .ma-holiday-list__range {
        font-weight: 600;
        gap: 0;
        white-space: nowrap;
    }

    .ma-holiday-list__dates {
        font-weight: 600;
        color: #3B3731;
    }

    .ma-holiday-list__days {
        margin-left: 4px;
        color: #9C9790;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .ma-holiday-list__reason {
        color: #9C9790;
        font-weight: 400;
        padding-right: 16px;
    }

    .ma-holiday-list__actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    .ma-holiday-list__edit,
    .ma-holiday-list__delete {
        border: 0;
        background: transparent;
        cursor: pointer;
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3B3731;
        transition: opacity 0.12s ease;
    }

    .ma-holiday-list__edit img,
    .ma-holiday-list__delete img {
        display: block;
    }

    .ma-holiday-list__edit:hover,
    .ma-holiday-list__delete:hover {
        opacity: 0.8;
        background: transparent;
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
        padding: 20px;
        text-align: center;
        color: #9C9790;
        font-size: 14px;
        border-top: 1px solid #D4D4D4;
    }

    .ma-field[data-date-trigger] .ma-field-value {
        cursor: pointer;
    }

    /* Figma 452:2626 — single 240×234 date calendar (fits 6 week rows) */
    .ma-mini-cal {
        position: fixed;
        z-index: 10060;
        width: 240px;
        height: 234px;
        padding: 10px;
        box-sizing: border-box;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        box-shadow: none;
        color: #3b3731;
        font-family: Lato, sans-serif;
        opacity: 0;
        transform: translateY(6px) scale(0.98);
        pointer-events: none;
        transition: opacity 160ms ease, transform 200ms cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ma-mini-cal.is-open {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .ma-mini-cal.ma-mini-cal--instant {
        transition: none;
    }

    .ma-mini-cal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 26px;
        margin-bottom: 10px;
        flex-shrink: 0;
    }

    .ma-mini-cal__nav {
        border: 0;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        box-shadow: 0 4px 4px rgba(0, 0, 0, 0.03);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        transition: transform 160ms ease, box-shadow 160ms ease;
    }

    .ma-mini-cal__nav:hover {
        transform: scale(1.06);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.06);
    }

    .ma-mini-cal__nav img {
        display: block;
        width: 26px;
        height: 26px;
        pointer-events: none;
    }

    .ma-mini-cal__nav--next img {
        transform: scaleX(-1);
    }

    .ma-mini-cal__title {
        flex: 1;
        min-width: 0;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        text-align: center;
    }

    .ma-mini-cal__weekdays,
    .ma-mini-cal__grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        width: 100%;
        max-width: 220px;
        margin: 0 auto;
    }

    .ma-mini-cal__weekdays {
        margin-bottom: 4px;
        flex-shrink: 0;
    }

    .ma-mini-cal__weekdays span {
        height: 17px;
        text-align: center;
        color: #9c9790;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: 17px;
    }

    .ma-mini-cal__grid {
        flex: 1 1 auto;
        min-height: 0;
        grid-auto-rows: 1fr;
        align-content: stretch;
        gap: 0;
    }

    .ma-mini-cal__day {
        position: relative;
        border: 0;
        background: transparent;
        width: 100%;
        min-width: 0;
        height: auto;
        min-height: 0;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b3731;
        font-family: Lato, sans-serif;
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        cursor: pointer;
        z-index: 1;
        overflow: hidden;
    }

    .ma-mini-cal__day.is-in-range {
        background: rgba(255, 201, 122, 0.2);
    }

    .ma-mini-cal__day.is-range-start {
        border-radius: 50% 0 0 50%;
    }

    .ma-mini-cal__day.is-range-end {
        border-radius: 0 50% 50% 0;
    }

    .ma-mini-cal__day.is-range-start.is-range-end {
        border-radius: 50%;
    }

    .ma-mini-cal__day-label {
        width: 26px;
        height: 26px;
        max-width: 100%;
        max-height: 100%;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        box-sizing: border-box;
        transition: background-color 160ms ease, color 160ms ease;
    }

    .ma-mini-cal__day.is-other-month .ma-mini-cal__day-label {
        color: #3b3731;
    }

    .ma-mini-cal__day.is-disabled {
        cursor: not-allowed;
    }

    .ma-mini-cal__day.is-disabled .ma-mini-cal__day-label {
        color: #dbd8d2;
    }

    .ma-mini-cal__day:not(.is-disabled):not(.is-selected):hover .ma-mini-cal__day-label {
        background: rgba(255, 201, 122, 0.25);
    }

    .ma-mini-cal__day.is-selected .ma-mini-cal__day-label {
        background: #ffc97a;
        color: #fff;
    }

    .ma-card--pause {
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .ma-card--pause>h3 {
        display: none;
    }

    .ma-pause-row {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border: 1px solid #ededed;
        border-radius: 10px;
        background: #f5f5f5;
        padding: 18px 20px;
        min-height: 76px;
        box-sizing: border-box;
    }

    .ma-pause-row.is-paused {
        background: #ffecec;
        border-color: #f3cfcf;
    }

    .ma-pause-copy {
        min-width: 0;
        flex: 1;
    }

    .ma-pause-copy strong {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-pause-copy small {
        color: #9D9B98;
        font-weight: 400;
    }

    .ma-pause-copy p {
        margin: 4px 0 0;
        color: #9C9790;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 22px;
    }

    .ma-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
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

    .ma-save-bar {
        position: sticky;
        bottom: 0;
        z-index: 8;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 80px;
        margin: 0;
        padding: 19px 20px;
        background: rgba(186, 207, 142, 0.2);
        border-top: 1px solid #B5DB65;
        backdrop-filter: blur(10px);
        box-sizing: border-box;
    }

    .ma-save-bar.is-dirty {
        background: #FFFCF6;
        border-top-color: #FFAE37;
    }

    .ma-save-bar__status {
        margin: 0;
        color: #AFCD6F;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: 22px;
    }

    .ma-save-bar__status span {
        color: #9C9790;
        font-weight: 600;
    }

    .ma-save-bar.is-dirty .ma-save-bar__status {
        color: #FFAE37;
    }

    .ma-footer-actions {
        display: none;
        justify-content: flex-end;
        gap: 20px;
        margin: 0;
    }

    .ma-save-bar.is-dirty .ma-footer-actions {
        display: flex;
    }

    .ma-btn:disabled {
        opacity: 0.5;
        cursor: default;
        pointer-events: none;
    }

    .ma-btn-light {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100px;
        border: 1px solid #D9D9D9;
        background: #fff;
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        box-shadow: none;
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

    .ma-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
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
    }
</style>

<script>
    (() => {
        const setupManageAvailability = () => {
            const root = document.querySelector('.ma-board');
            if (!root) {
                return;
            }
            if (root.dataset.maSetupBound === '1') {
                return;
            }
            root.dataset.maSetupBound = '1';
            const staffList = root.querySelector('.ma-staff-list');
            const staffNameTarget = root.querySelector('[data-staff-name-target]');
            const staffJobTitleTarget = root.querySelector('[data-staff-job-title-target]');
            const staffJobWrap = root.querySelector('[data-staff-job-wrap]');
            const holidayReasonField = () => document.querySelector(
                '.ma-holiday-modal [data-holiday-reason]');
            const pauseField = root.querySelector('[data-pause-booking]');
            const pauseCopy = root.querySelector('[data-pause-copy]');
            const pauseTitle = root.querySelector('[data-pause-title]');
            const pauseRow = root.querySelector('[data-pause-row]');
            const saveBar = root.querySelector('[data-save-bar]');
            const saveStatus = root.querySelector('[data-save-status]');
            const cancelBtn = root.querySelector('[data-ma-cancel]');
            const saveChangesBtn = root.querySelector('[data-ma-save-changes]');
            const dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            let pauseBaseline = !!pauseField?.checked;
            const dirtySources = new Set();

            const markDirty = (source = 'change') => {
                if (!saveBar) return;
                dirtySources.add(source);
                const count = dirtySources.size;
                saveBar.classList.add('is-dirty');
                if (saveStatus) {
                    saveStatus.textContent = count === 1 ?
                        '1 unsaved changes' :
                        `${count} unsaved changes`;
                }
                if (cancelBtn) cancelBtn.disabled = false;
                if (saveChangesBtn) saveChangesBtn.disabled = false;
            };

            const markSaved = () => {
                if (!saveBar) return;
                dirtySources.clear();
                saveBar.classList.remove('is-dirty');
                if (saveStatus) saveStatus.innerHTML = 'All changes saved <span>· just now</span>';
                if (cancelBtn) cancelBtn.disabled = true;
                if (saveChangesBtn) {
                    saveChangesBtn.disabled = true;
                    saveChangesBtn.classList.remove('is-saving');
                }
                pauseBaseline = !!pauseField?.checked;
            };

            const syncPauseCopy = () => {
                const paused = !!pauseField?.checked;
                pauseRow?.classList.toggle('is-paused', paused);
                if (pauseTitle) {
                    pauseTitle.innerHTML = paused ?
                        'New bookings paused' :
                        'Pause new bookings <small>(effective today)</small>';
                }
                if (pauseCopy) {
                    pauseCopy.textContent = paused ?
                        "Clients can still see your profile, but can't request new appointments. Existing bookings are unaffected." :
                        'Clients are currently able to request appointments in your open slots.';
                }
            };

            const formatTime = (raw) => {
                if (!raw) return '';
                const [hStr, mStr = '00'] = String(raw).split(':');
                const hour = parseInt(hStr, 10);
                if (Number.isNaN(hour)) return raw;
                const suffix = hour < 12 ? 'AM' : 'PM';
                return `${String(hour).padStart(2, '0')}:${mStr} ${suffix}`;
            };

            const hoursLabel = (start, end, status) => {
                if (!status) return '';
                const [sh = 0, sm = 0] = String(start || '0:0').split(':').map((n) => parseInt(n, 10) || 0);
                const [eh = 0, em = 0] = String(end || '0:0').split(':').map((n) => parseInt(n, 10) || 0);
                const diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff <= 0) return '0h';
                const hours = diff / 60;
                return Number.isInteger(hours) ? `${hours}h` : `${hours.toFixed(1).replace(/\.0$/, '')}h`;
            };

            const updateHoursForDay = (day) => {
                const timeRow = root.querySelector(`[data-day="${day}"][data-day-cell="time"]`);
                const hoursEl = root.querySelector(`[data-day="${day}"][data-day-cell="hours"] [data-day-hours]`);
                const checkbox = root.querySelector(`[data-day="${day}"][data-day-cell="name"] input[data-day-status]`);
                if (!hoursEl) return;
                const start = timeRow?.querySelector('[data-time-type="start"]')?.dataset.timeValue || '10:00';
                const end = timeRow?.querySelector('[data-time-type="end"]')?.dataset.timeValue || '18:00';
                hoursEl.textContent = hoursLabel(start, end, !!(checkbox && checkbox.checked));
            };

            const collectWorkingHours = () => {
                const hours = {};
                dayKeys.forEach((day) => {
                    const checkbox = root.querySelector(`[data-day="${day}"][data-day-cell="name"] input[data-day-status]`);
                    const timeRow = root.querySelector(`[data-day="${day}"][data-day-cell="time"]`);
                    hours[day] = {
                        status: !!(checkbox && checkbox.checked),
                        start: timeRow?.querySelector('[data-time-type="start"]')?.dataset.timeValue || '10:00',
                        end: timeRow?.querySelector('[data-time-type="end"]')?.dataset.timeValue || '18:00',
                    };
                });
                return hours;
            };

            const activeStaffId = () => {
                const pill = root.querySelector('.ma-staff-pill.ma-staff-pill--active');
                return parseInt(pill?.dataset.staffId || '0', 10) || null;
            };

            const persistActivePayload = (patch) => {
                const pill = root.querySelector('.ma-staff-pill.ma-staff-pill--active');
                if (!pill?.dataset.staffPayload) return;
                try {
                    const parsed = JSON.parse(pill.dataset.staffPayload);
                    Object.assign(parsed, patch);
                    pill.dataset.staffPayload = JSON.stringify(parsed);
                } catch (err) {}
            };

            const setDayAvailability = (day, isOn) => {
                const nameRow = root.querySelector(`[data-day="${day}"][data-day-cell="name"]`);
                const timeRow = root.querySelector(`[data-day="${day}"][data-day-cell="time"]`);
                const hoursRow = root.querySelector(`[data-day="${day}"][data-day-cell="hours"]`);
                const editRow = root.querySelector(`[data-day="${day}"][data-day-cell="edit"]`);
                if (nameRow) nameRow.classList.toggle('is-disabled', !isOn);
                if (timeRow) {
                    timeRow.classList.toggle('is-unavailable', !isOn);
                    timeRow.classList.toggle('is-disabled', !isOn);
                    if (!isOn) timeRow.classList.remove('is-editing');
                }
                if (hoursRow) hoursRow.classList.toggle('is-disabled', !isOn);
                if (editRow) {
                    editRow.classList.toggle('is-disabled', !isOn);
                    if (!isOn) editRow.classList.remove('is-editing');
                }
                updateHoursForDay(day);
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
                    const startLabel = formatTime(data.start);
                    const endLabel = formatTime(data.end);
                    if (startChip) startChip.dataset.timeValue = data.start || '';
                    if (endChip) endChip.dataset.timeValue = data.end || '';
                    if (startEl) startEl.textContent = startLabel;
                    if (endEl) endEl.textContent = endLabel;
                    const saveBtn = editRow.querySelector('[data-day-action="save"]');
                    if (saveBtn) saveBtn.classList.remove('is-saving');
                    timeRow.classList.remove('is-editing');
                    editRow.classList.remove('is-editing');
                    setDayAvailability(day, !!data.status);
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
                const reasonField = holidayReasonField();
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
                syncPauseCopy();
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
                const name = pill.dataset.displayTitle || pill.dataset.staffName || '';
                const job = pill.dataset.staffJobTitle || '';
                const isBusiness = pill.dataset.isBusiness === '1';
                if (staffNameTarget) {
                    staffNameTarget.textContent = name;
                }
                const subtitleEl = root.querySelector('[data-schedule-subtitle]');
                if (subtitleEl) {
                    subtitleEl.textContent = isBusiness ? 'Editing business schedule' : 'Editing staff schedule';
                }
                if (staffJobTitleTarget) {
                    staffJobTitleTarget.textContent = job;
                }
                if (staffJobWrap) {
                    staffJobWrap.style.display = job ? '' : 'none';
                }
                applyStaffPayload(pill);
                pauseBaseline = !!pauseField?.checked;
                markSaved();
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
                const checkbox = root.querySelector(
                    `[data-day="${day}"][data-day-cell="name"] input[data-day-status]`);
                if (editing && checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                }
                setDayAvailability(day, editing || !!(checkbox && checkbox.checked));
                const timeRow = root.querySelector(`[data-day="${day}"][data-day-cell="time"]`);
                const editRow = root.querySelector(`[data-day="${day}"][data-day-cell="edit"]`);
                if (editing) {
                    if (timeRow) {
                        timeRow.classList.remove('is-disabled', 'is-unavailable');
                        timeRow.classList.add('is-editing');
                    }
                    if (editRow) {
                        editRow.classList.remove('is-disabled');
                        editRow.classList.add('is-editing');
                    }
                } else {
                    if (timeRow) timeRow.classList.remove('is-editing');
                    if (editRow) editRow.classList.remove('is-editing');
                }
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
                    if (activeTimeChip.matches?.('input.ma-holiday-time')) {
                        activeTimeChip.value = label;
                    } else {
                        const span = activeTimeChip.querySelector(
                            '[data-time-start], [data-time-end]');
                        if (span) span.textContent = label;
                    }
                    const day = activeTimeChip.closest('[data-day]')?.dataset.day;
                    if (day) updateHoursForDay(day);
                    markDirty(`day-${day || 'time'}`);
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
                    if (chip && chipRow && chipRow.classList.contains('is-editing') && !chipRow.classList.contains('is-disabled')) {
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
                    const holidayEditBtn = event.target.closest('[data-holiday-edit]');

                    if (holidayEditBtn) {
                        event.preventDefault();
                        const from = holidayEditBtn.dataset.from || todayIso;
                        const to = holidayEditBtn.dataset.to || from;
                        const reasonField = holidayReasonField();
                        if (reasonField) reasonField.value = holidayEditBtn.dataset.reason || '';
                        window.dispatchEvent(new CustomEvent('range-calendar-set', {
                            detail: {
                                componentId: 'dashboard-manage-availability-range',
                                start: from,
                                end: to,
                            },
                        }));
                        window.dispatchEvent(new CustomEvent('ma-open-holiday-modal'));
                        return;
                    }

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
                            } catch (err) {}
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

                window.addEventListener('staff-schedule-saved', () => {
                    markSaved();
                });

                root.addEventListener('change', (event) => {
                    const dayCheckbox = event.target.closest('input[data-day-status]');
                    if (dayCheckbox) {
                        const day = dayCheckbox.closest('[data-day]')?.dataset.day;
                        if (day) setDayAvailability(day, dayCheckbox.checked);
                        markDirty(`day-${day || 'status'}`);
                        return;
                    }
                    if (event.target === pauseField) {
                        syncPauseCopy();
                        markDirty('pause');
                    }
                });

                document.addEventListener('input', (event) => {
                    if (event.target?.matches?.('.ma-holiday-modal [data-holiday-reason]')) {
                        markDirty('reason');
                    }
                });

                cancelBtn?.addEventListener('click', () => {
                    const pill = root.querySelector('.ma-staff-pill.ma-staff-pill--active');
                    if (pill) applyStaffPayload(pill);
                    markSaved();
                });

                saveChangesBtn?.addEventListener('click', () => {
                    if (saveChangesBtn.disabled || saveChangesBtn.classList.contains('is-saving')) return;
                    const staffId = activeStaffId();
                    if (!staffId || !window.Livewire) return;
                    const workingHours = collectWorkingHours();
                    const paused = !!pauseField?.checked;
                    persistActivePayload({
                        workingHours,
                        pauseBooking: paused
                    });
                    saveChangesBtn.classList.add('is-saving');
                    window.Livewire.dispatch('save-staff-schedule', {
                        staffId,
                        workingHours,
                        paused,
                    });
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

                document.addEventListener('click', (event) => {
                    const holidaySaveBtn = event.target.closest(
                        '.ma-holiday-modal [data-holiday-action="save"]');
                    if (!holidaySaveBtn) return;
                    if (holidaySaveBtn.classList.contains('is-saving')) return;
                    event.preventDefault();
                    const activePill = root.querySelector(
                        '.ma-staff-pill.ma-staff-pill--active');
                    const staffId = activePill?.dataset.staffId;
                    if (!staffId) return;
                    const range = getInlineRangeValues();
                    const reason = holidayReasonField()?.value || '';
                    const startChip = document.querySelector(
                        '.ma-holiday-modal [data-holiday-time][data-time-type="start"]');
                    const endChip = document.querySelector(
                        '.ma-holiday-modal [data-holiday-time][data-time-type="end"]');
                    const payload = {
                        staffId: parseInt(staffId, 10),
                        from: range.start || '',
                        to: range.end || '',
                        reason,
                        startTime: startChip?.dataset.timeValue || '',
                        endTime: endChip?.dataset.timeValue || '',
                    };

                    holidaySaveBtn.classList.add('is-saving');
                    if (window.Livewire) {
                        window.Livewire.dispatch('save-staff-holiday', payload);
                    }
                });

                window.addEventListener('staff-holiday-saved', () => {
                    const btn = document.querySelector(
                        '.ma-holiday-modal [data-holiday-action="save"]');
                    if (btn) btn.classList.remove('is-saving');
                    resetHolidayForm();
                    window.dispatchEvent(new CustomEvent('ma-close-holiday-modal'));
                });
            }

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
                const start = document.querySelector(
                    '#dashboard-manage-availability-range input[name="manage_availability_start_date"]'
                );
                const end = document.querySelector(
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

                const rangeStart = isoToDate(range.start);
                const rangeEnd = isoToDate(range.end);
                const rangeFrom = rangeStart && rangeEnd ?
                    (rangeStart <= rangeEnd ? rangeStart : rangeEnd) :
                    rangeStart || rangeEnd;
                const rangeTo = rangeStart && rangeEnd ?
                    (rangeStart <= rangeEnd ? rangeEnd : rangeStart) :
                    null;

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
                    const thisDate = new Date(year, month, day);
                    thisDate.setHours(0, 0, 0, 0);

                    if (iso === selectedISO) cell.classList.add('is-selected');
                    if (iso === range.start || iso === range.end) {
                        if (iso === range.start) cell.classList.add('is-range-start');
                        if (iso === range.end) cell.classList.add('is-range-end');
                    }
                    if (rangeFrom && rangeTo && thisDate >= rangeFrom && thisDate <= rangeTo) {
                        cell.classList.add('is-in-range');
                        if (thisDate.getTime() === rangeFrom.getTime()) {
                            cell.classList.add('is-range-start');
                        }
                        if (thisDate.getTime() === rangeTo.getTime()) {
                            cell.classList.add('is-range-end');
                        }
                    } else if (rangeFrom && !rangeTo && iso === formatISO(
                            rangeFrom.getFullYear(), rangeFrom.getMonth(), rangeFrom.getDate()
                        )) {
                        cell.classList.add('is-in-range', 'is-range-start', 'is-range-end');
                    }

                    if (otherDate) {
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

            const miniCalPrevIcon = @json(asset('images/business-hub/icon-mini-cal-prev.svg'));

            const buildMiniCalendar = () => {
                // Keep exactly one calendar popup in the DOM (Figma 452:2626).
                document.querySelectorAll('.ma-mini-cal').forEach((node) => node.remove());

                const el = document.createElement('div');
                el.className = 'ma-mini-cal';
                el.setAttribute('data-ma-mini-cal', '1');
                el.innerHTML = `
                    <div class="ma-mini-cal__header">
                        <button type="button" class="ma-mini-cal__nav" data-mini-cal-prev aria-label="Previous month">
                            <img src="${miniCalPrevIcon}" alt="" width="26" height="26" />
                        </button>
                        <span class="ma-mini-cal__title" data-mini-cal-title></span>
                        <button type="button" class="ma-mini-cal__nav ma-mini-cal__nav--next" data-mini-cal-next aria-label="Next month">
                            <img src="${miniCalPrevIcon}" alt="" width="26" height="26" />
                        </button>
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
                const existing = document.querySelector('.ma-mini-cal[data-ma-mini-cal="1"]');
                if (existing) {
                    miniCalEl = existing;
                    return miniCalEl;
                }
                miniCalEl = buildMiniCalendar();
                return miniCalEl;
            };

            const positionMiniCalendar = (anchor) => {
                if (!miniCalEl) return;
                const target = anchor.querySelector('.ma-field-value') || anchor;
                const rect = target.getBoundingClientRect();
                const width = 240;
                const height = 234;
                miniCalEl.style.width = `${width}px`;
                miniCalEl.style.height = `${height}px`;

                let top = rect.bottom + 8;
                if (top + height > window.innerHeight - 8) {
                    top = Math.max(8, rect.top - height - 8);
                }
                let left = rect.left;
                if (left + width > window.innerWidth - 8) {
                    left = window.innerWidth - width - 8;
                }
                if (left < 8) left = 8;
                miniCalEl.style.top = `${top}px`;
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
                closeTimeDropdown();
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

            const parseHolidayTimeLabel = (raw) => {
                const text = String(raw || '').trim().toUpperCase().replace(/\s+/g, ' ');
                if (!text) return null;
                const match = text.match(/^(\d{1,2})(?::(\d{2}))?\s*(AM|PM)?$/i);
                if (!match) return null;
                let hour = parseInt(match[1], 10);
                let minute = parseInt(match[2] ?? '0', 10);
                const suffix = (match[3] || '').toUpperCase();
                if (Number.isNaN(hour) || Number.isNaN(minute) || minute > 59) return null;
                // Accept both 12h (7:00 AM) and display-style 24h+suffix (14:00 PM).
                if (suffix === 'AM' || suffix === 'PM') {
                    if (hour <= 12) {
                        if (hour < 1) return null;
                        if (suffix === 'AM') {
                            if (hour === 12) hour = 0;
                        } else if (hour !== 12) {
                            hour += 12;
                        }
                    } else if (hour > 23) {
                        return null;
                    }
                } else if (hour > 23) {
                    return null;
                }
                const value = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                return {
                    value,
                    label: formatTime(value),
                };
            };

            const applyHolidayTimeInput = (input) => {
                const parsed = parseHolidayTimeLabel(input.value);
                if (!parsed) {
                    const fallback = input.dataset.timeValue || '07:00';
                    input.value = formatTime(fallback);
                    return;
                }
                input.dataset.timeValue = parsed.value;
                input.value = parsed.label;
                markDirty('holiday-time');
            };

            const onHolidayDateTriggerClick = (event) => {
                const trigger = event.currentTarget;
                event.preventDefault();
                event.stopPropagation();
                if (activeDateTrigger === trigger) {
                    closeMiniCalendar();
                } else {
                    openMiniCalendar(trigger);
                }
            };

            const onHolidayTimeFocus = (event) => {
                const holidayChip = event.currentTarget;
                closeMiniCalendar();
                openTimeDropdown(holidayChip);
            };

            const bindHolidayModalControls = () => {
                document.querySelectorAll('.ma-holiday-modal [data-date-trigger]').forEach((trigger) => {
                    if (trigger.dataset.maDateBound === '1') return;
                    trigger.dataset.maDateBound = '1';
                    trigger.addEventListener('click', onHolidayDateTriggerClick);
                    trigger.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            onHolidayDateTriggerClick(event);
                        }
                    });
                });

                document.querySelectorAll('.ma-holiday-modal [data-holiday-time]').forEach((chip) => {
                    if (chip.dataset.maTimeBound === '1') return;
                    chip.dataset.maTimeBound = '1';
                    chip.addEventListener('focus', onHolidayTimeFocus);
                    chip.addEventListener('click', (event) => {
                        event.stopPropagation();
                        closeMiniCalendar();
                        openTimeDropdown(chip);
                    });
                    chip.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeTimeDropdown();
                            chip.blur();
                        }
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyHolidayTimeInput(chip);
                            closeTimeDropdown();
                        }
                    });
                    chip.addEventListener('blur', () => {
                        // Allow dropdown option click to land first.
                        setTimeout(() => {
                            if (document.activeElement?.closest?.('.ma-time-dropdown')) return;
                            applyHolidayTimeInput(chip);
                            if (activeTimeChip === chip) closeTimeDropdown();
                        }, 150);
                    });
                });
            };

            window.__maOpenHolidayDateCalendar = (trigger) => {
                if (!trigger) return;
                openMiniCalendar(trigger);
            };

            if (!root.dataset.dateTriggersBound) {
                root.dataset.dateTriggersBound = '1';

                // Outside-click close (capture). Open handlers are bound on the modal controls themselves.
                document.addEventListener('click', (event) => {
                    if (!miniCalEl || !miniCalEl.classList.contains('is-open')) return;
                    if (event.target.closest('.ma-mini-cal')) return;
                    if (event.target.closest('.ma-holiday-modal [data-date-trigger]')) return;
                    closeMiniCalendar();
                }, true);

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

                window.addEventListener('ma-close-holiday-modal', () => {
                    closeMiniCalendar();
                    closeTimeDropdown();
                });

                window.addEventListener('ma-open-holiday-modal', bindHolidayModalControls);

                // Modal may already be in the DOM after Alpine teleport.
                bindHolidayModalControls();
                setTimeout(bindHolidayModalControls, 0);
                setTimeout(bindHolidayModalControls, 250);
                document.addEventListener('alpine:initialized', bindHolidayModalControls);
            }

            const formatDate = (isoDate) => {
                if (!isoDate) return '';

                const [year, month, day] = String(isoDate).split('-').map(Number);
                if (!year || !month || !day) return '';

                const date = new Date(year, month - 1, day);
                if (Number.isNaN(date.getTime())) return '';

                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                });
            };

            const liveDateFrom = () => document.getElementById('manage-availability-date-from');
            const liveDateTo = () => document.getElementById('manage-availability-date-to');
            const liveStartInput = () => document.querySelector(
                '#dashboard-manage-availability-range input[name="manage_availability_start_date"]');
            const liveEndInput = () => document.querySelector(
                '#dashboard-manage-availability-range input[name="manage_availability_end_date"]');

            const syncRangeFields = () => {
                const fromEl = liveDateFrom();
                const toEl = liveDateTo();
                if (fromEl) fromEl.textContent = formatDate(liveStartInput()?.value);
                if (toEl) toEl.textContent = formatDate(liveEndInput()?.value);
            };

            if (!root.dataset.rangeSyncBound) {
                root.dataset.rangeSyncBound = '1';
                syncRangeFields();
                setTimeout(syncRangeFields, 0);
                setTimeout(syncRangeFields, 120);
                setTimeout(syncRangeFields, 400);
                document.addEventListener('alpine:initialized', syncRangeFields);
                window.addEventListener('ma-open-holiday-modal', syncRangeFields);

                window.addEventListener('range-calendar-changed', (event) => {
                    const detail = event?.detail || {};
                    if (detail.componentId && detail.componentId !==
                        'dashboard-manage-availability-range') {
                        return;
                    }

                    const fromEl = liveDateFrom();
                    const toEl = liveDateTo();
                    if (fromEl) fromEl.textContent = formatDate(detail.start ?? liveStartInput()?.value);
                    if (toEl) toEl.textContent = formatDate(detail.end ?? liveEndInput()?.value);
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupManageAvailability);
        } else {
            setupManageAvailability();
        }

        document.addEventListener('livewire:navigated', setupManageAvailability);
        window.addEventListener('manage-availability-mounted', setupManageAvailability);
    })();
</script>
