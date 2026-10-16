@props(['booking' => null])

@if ($booking)
    @php
        $iconBase = asset('images/business-hub');
        $isSpaceUser = strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? '')) === 'space';
        $owner = $booking->petOwner;
        $ownerName = trim((string) ($owner?->name ?? 'Client')) ?: 'Client';
        $ownerFirst = explode(' ', $ownerName)[0] ?? $ownerName;
        $pets = $booking->relationLoaded('pets') ? $booking->pets : $booking->pets()->get();
        $petNames = $pets->map(fn($p) => trim((string) ($p->name ?? '')))->filter()->values();
        $firstPet = $petNames->first() ?: 'Pet';
        $extraPets = max(0, $petNames->count() - 1);
        $petsLabel = $extraPets > 0 ? $firstPet . ' +' . $extraPets : $firstPet;

        $parts = preg_split('/\s+/', $ownerName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $ownerShort = count($parts) >= 2 ? $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.' : $ownerName;

        $serviceLabel = trim((string) ($booking->service ?? '')) ?: 'Service';
        $timeRaw = trim((string) ($booking->time ?? ''));
        if ($isSpaceUser) {
            $serviceLower = strtolower($serviceLabel);
            $serviceLabel = match (true) {
                (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-Day',
                (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-Day',
                str_contains($serviceLower, 'hour') => 'Hourly',
                default => $serviceLabel,
            };
            if (!in_array($serviceLabel, ['Full-Day', 'Half-Day', 'Hourly'], true) && str_contains($timeRaw, '-')) {
                $rangeParts = preg_split('/\s*-\s*/', $timeRaw, 2);
                preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
                preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
                if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                    $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                    if ($diff < 0) {
                        $diff += 24 * 60;
                    }
                    $serviceLabel = $diff >= 7 * 60 ? 'Full-Day' : ($diff >= 3 * 60 ? 'Half-Day' : 'Hourly');
                }
            }
        }
        $dateLabel = $booking->date?->format('j M') ?? '—';
        if ($timeRaw !== '' && !str_contains($timeRaw, '-')) {
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i', substr($timeRaw, 0, 5));
                $timeLabel = $start->format('H:i') . ' - ' . $start->copy()->addHour()->format('H:i');
            } catch (\Throwable) {
                $timeLabel = $timeRaw;
            }
        } else {
            $timeLabel = $timeRaw !== '' ? $timeRaw : '—';
            if (str_contains($timeRaw, '-')) {
                $partsTime = preg_split('/\s*-\s*/', $timeRaw, 2);
                preg_match('/(\d{1,2}:\d{2})/', (string) ($partsTime[0] ?? ''), $mStart);
                preg_match('/(\d{1,2}:\d{2})/', (string) ($partsTime[1] ?? ''), $mEnd);
                if (!empty($mStart[1]) && !empty($mEnd[1])) {
                    $timeLabel = $mStart[1] . ' - ' . $mEnd[1];
                }
            }
        }

        $visitRaw = str_replace('_', ' ', strtolower(trim((string) ($booking->visit_type ?? ''))));
        $visitBadge = match (true) {
            str_contains($visitRaw, 'home') => 'Home Visits',
            str_contains($visitRaw, 'salon') => 'Salon',
            str_contains($visitRaw, 'garden'), str_contains($visitRaw, 'shed') => 'Garden/Shed',
            str_contains($visitRaw, 'private') => 'Private room',
            str_contains($visitRaw, 'mobile') => 'Mobile Station',
            $visitRaw !== '' => ucwords($visitRaw),
            default => $isSpaceUser ? 'Garden/Shed' : null,
        };

        $avatarSrc = \App\Support\BusinessHubAvatar::mediaUrl($isSpaceUser ? $owner?->profile_image ?? ($pets->first()?->photo ?? null) : $pets->first()?->photo ?? ($owner?->profile_image ?? null));
        $avatarName = $isSpaceUser ? $ownerName : ($firstPet !== 'Pet' ? $firstPet : $ownerName);
        $avatarInitials = \App\Support\BusinessHubAvatar::initials($avatarName);
        $serviceIcon = $isSpaceUser
            ? match (true) {
                str_contains(strtolower($serviceLabel), 'full') => 'icon-space-service-8h.svg',
                str_contains(strtolower($serviceLabel), 'half') => 'icon-space-service-4h.svg',
                default => 'icon-space-service-1h.svg',
            }
            : 'icon-confirmed-service.svg';
        $namesLine = $isSpaceUser ? '<strong>' . e($ownerShort) . '</strong>' : '<strong>' . e($petsLabel) . ' ·</strong><span> ' . e($ownerShort) . '</span>';
    @endphp

    @teleport('body')
        <div class="bh-confirmed-overlay{{ $isSpaceUser ? ' is-space' : '' }}" wire:click.self="closeConfirmedModal"
            wire:keydown.escape.window="closeConfirmedModal"
            x-data="{
                init() {
                        document.documentElement.classList.add('pending-detail-open');
                        document.body.classList.add('pending-detail-open');
                    },
                    destroy() {
                        document.documentElement.classList.remove('pending-detail-open');
                        document.body.classList.remove('pending-detail-open');
                    }
            }">
            <article class="bh-confirmed-modal" role="dialog" aria-modal="true" aria-labelledby="bh-confirmed-title">
                <button type="button" class="bh-confirmed-close" wire:click="closeConfirmedModal" aria-label="Close modal">
                    <img src="{{ $iconBase }}/icon-request-modal-close.svg" alt="" width="20" height="20">
                </button>

                <div class="bh-confirmed-success" aria-hidden="true">
                    <img src="{{ $iconBase }}/icon-confirmed-success.svg" alt="" width="45" height="45">
                </div>

                <header class="bh-confirmed-header">
                    <h2 id="bh-confirmed-title">Booking Confirmed</h2>
                    <p class="bh-confirmed-lead">Your booking with {{ $ownerFirst }} is confirmed and added to your calendar.</p>
                </header>

                <div class="bh-confirmed-card">
                    <div class="bh-confirmed-card-main">
                        @if ($avatarSrc)
                            <span class="bh-confirmed-avatar has-photo" aria-hidden="true">
                                <img src="{{ $avatarSrc }}" alt="" width="34" height="34" loading="lazy" decoding="async"
                                    onerror="this.hidden=true; this.nextElementSibling.hidden=false; this.parentElement.classList.remove('has-photo'); this.parentElement.classList.add('is-fallback');">
                                <span class="bh-confirmed-avatar__initials" hidden>{{ $avatarInitials }}</span>
                            </span>
                        @else
                            <span class="bh-confirmed-avatar is-fallback" aria-hidden="true">
                                <span class="bh-confirmed-avatar__initials">{{ $avatarInitials }}</span>
                            </span>
                        @endif

                        <div class="bh-confirmed-card-body">
                            @if ($visitBadge)
                                <span class="bh-confirmed-badge">{{ $visitBadge }}</span>
                            @endif
                            <p class="bh-confirmed-names{{ $visitBadge ? ' has-badge' : '' }}">
                                {!! $namesLine !!}
                            </p>

                            <div class="bh-confirmed-meta">
                                <span>
                                    <img src="{{ $iconBase }}/{{ $serviceIcon }}" alt="" width="{{ $isSpaceUser ? 18 : 12 }}" height="{{ $isSpaceUser ? 15 : 12 }}">
                                    {{ $serviceLabel }}
                                </span>
                                <span>
                                    <img src="{{ $iconBase }}/icon-confirmed-date.svg" alt="" width="12" height="12">
                                    {{ $dateLabel }}
                                </span>
                                <span>
                                    <img src="{{ $iconBase }}/icon-confirmed-clock.svg" alt="" width="12" height="12">
                                    {{ $timeLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bh-confirmed-divider" aria-hidden="true"></div>

                    <p class="bh-confirmed-notified">{{ $ownerShort }} has been notified.</p>
                </div>

                <footer class="bh-confirmed-footer">
                    <button type="button" class="bh-confirmed-message" disabled>
                        <img src="{{ $iconBase }}/icon-confirmed-message.svg" alt="" width="13.75" height="11.25">
                        Message
                    </button>
                    <button type="button" class="bh-confirmed-done" wire:click="closeConfirmedModal">Done</button>
                </footer>
            </article>
        </div>
    @endteleport
@endif

@once
    <style>
        html.pending-detail-open,
        body.pending-detail-open {
            overflow: hidden;
        }

        .bh-confirmed-overlay {
            position: fixed;
            inset: 0;
            z-index: 2147483015;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(59, 55, 49, 0.10);
            font-family: Lato, sans-serif;
        }

        .bh-confirmed-modal {
            position: relative;
            width: min(505px, calc(100vw - 32px));
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        .bh-confirmed-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 20px;
            height: 20px;
            border: 0;
            padding: 0;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1;
        }

        .bh-confirmed-close img {
            width: 20px;
            height: 20px;
            display: block;
        }

        .bh-confirmed-success {
            display: flex;
            justify-content: center;
            margin: 0 0 20px;
            flex-shrink: 0;
        }

        .bh-confirmed-success img {
            width: 45px;
            height: 45px;
            display: block;
        }

        .bh-confirmed-header {
            text-align: center;
            margin-bottom: 20px;
            flex-shrink: 0;
        }

        .bh-confirmed-header h2 {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 24px;
            font-weight: 600;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-confirmed-lead {
            margin: 20px auto 0;
            max-width: 276px;
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-confirmed-card {
            background: #F9F9F9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            flex-shrink: 0;
        }

        .bh-confirmed-card-main {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .bh-confirmed-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--bh-avatar-bg, #FFC97A);
        }

        .bh-confirmed-avatar.has-photo {
            background: transparent;
        }

        .bh-confirmed-avatar img {
            width: 34px;
            height: 34px;
            object-fit: cover;
            display: block;
        }

        .bh-confirmed-avatar__initials {
            color: #FDFDFD;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
        }

        .bh-confirmed-card-body {
            position: relative;
            min-width: 0;
            flex: 1;
            padding-top: 3px;
        }

        .bh-confirmed-names {
            margin: 0;
            font-size: 14px;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
            min-width: 0;
            padding-right: 0;
        }

        .bh-confirmed-names.has-badge {
            padding-right: 110px;
        }

        .bh-confirmed-names strong {
            color: #3B3731;
            font-weight: 600;
        }

        .bh-confirmed-names span {
            color: #9D9B98;
            font-weight: 400;
        }

        .bh-confirmed-badge {
            position: absolute;
            top: 0;
            right: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 24px;
            padding: 0 10px;
            border-radius: 100px;
            background: rgba(255, 201, 122, 0.1);
            color: #FFC97A;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-confirmed-overlay.is-space .bh-confirmed-badge,
        .business-hub-container.is-space .bh-confirmed-badge,
        body:has(.business-hub-container.is-space) .bh-confirmed-badge {
            background: rgba(255, 168, 153, 0.15);
            color: #FFA899;
        }

        .bh-confirmed-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
            color: #9D9B98;
            font-size: 14px;
            line-height: normal;
        }

        .bh-confirmed-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-confirmed-meta img {
            width: 12px;
            height: 12px;
            display: block;
            flex-shrink: 0;
        }

        .bh-confirmed-divider {
            height: 1px;
            background: #E2E2E2;
            margin: 20px 0;
        }

        .bh-confirmed-notified {
            margin: 0;
            color: #8BAE40;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            display: flex;
            align-items: center;
            gap: 8px;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-confirmed-notified::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #8BAE40;
            flex-shrink: 0;
        }

        .bh-confirmed-footer {
            display: flex;
            gap: 20px;
        }

        .bh-confirmed-message,
        .bh-confirmed-done {
            flex: 1 1 0;
            height: 42px;
            border-radius: 75px;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            line-height: normal;
        }

        .bh-confirmed-message {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid #E2E2E2;
            background: #fff;
            color: #9D9B98;
        }

        .bh-confirmed-message img {
            width: 13.75px;
            height: 11.25px;
            display: block;
            flex-shrink: 0;
        }

        .bh-confirmed-message:disabled {
            opacity: 1;
            cursor: not-allowed;
        }

        .bh-confirmed-done {
            border: 0;
            background: #C9DDA0;
            color: #fff;
        }
    </style>
@endonce
