@props(['booking' => null])

@if ($booking)
    @php
        $bookingIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $owner = $booking->petOwner;
        $ownerName = trim((string) ($owner?->name ?? 'N/A')) ?: 'N/A';
        $pet = $booking->pets->first();

        $resolvePhotoUrl = function (?string $raw): ?string {
            $raw = trim((string) $raw);
            if ($raw === '') {
                return null;
            }

            return str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:') || str_starts_with($raw, '/') ? $raw : asset('storage/' . ltrim($raw, '/'));
        };

        $ownerPhoto = $resolvePhotoUrl($owner?->profile_image);
        $ownerInitials = \App\Support\BusinessHubAvatar::initials($ownerName);
        $petName = trim((string) ($pet?->name ?? 'N/A')) ?: 'N/A';
        $petPhoto = $resolvePhotoUrl($pet?->photo);
        $petInitials = \App\Support\BusinessHubAvatar::initials($petName);
        $petType = trim((string) ($pet?->pet_type ?? ''));
        $petBreed = trim((string) ($pet?->breed ?? ''));
        $petSex = trim((string) ($pet?->sex ?? ''));
        $petWeight = trim((string) ($pet?->weight ?? ''));
        $petMeta = trim(($petType !== '' ? $petType : 'Pet') . ($petBreed !== '' ? ' (' . $petBreed . ')' : ''));
        $petMeta .= $petSex !== '' ? ' · ' . ucfirst($petSex) : '';
        $petMeta .= $petWeight !== '' ? ' · ' . rtrim(rtrim(number_format((float) $petWeight, 2, '.', ''), '0'), '.') . 'kg' : '';

        $previousVisits = \App\Models\Booking::query()->where('goormer_spacer_id', $booking->goormer_spacer_id)->where('pet_owner_id', $booking->pet_owner_id)->where('id', '!=', $booking->id)->where('booking_status', 'completed')->count();

        $dateLabel = $booking->date?->format('D, j M Y') ?? 'N/A';
        $timeLabel = trim((string) ($booking->time ?? '')) ?: 'N/A';
        $visitRaw = str_replace('_', ' ', strtolower(trim((string) ($booking->visit_type ?? ''))));
        $locationLabel = match (true) {
            str_contains($visitRaw, 'home') => 'At your home',
            str_contains($visitRaw, 'salon') => 'Salon',
            str_contains($visitRaw, 'mobile') => 'Mobile station',
            str_contains($visitRaw, 'garden'), str_contains($visitRaw, 'shed') => 'Garden / Shed',
            $visitRaw !== '' => ucwords($visitRaw),
            default => 'N/A',
        };

        $extraAddOns = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(fn($item) => trim((string) data_get($item, 'label', data_get($item, 'name', ''))))
            ->filter()
            ->values();
        $addOnsLabel = $extraAddOns->isNotEmpty() ? $extraAddOns->implode(', ') . '.' : 'No add-ons selected.';
        $extraAmount = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])->sum(fn($item) => (float) data_get($item, 'amount', 0));
        $total = (float) ($booking->amount ?? 0) + $extraAmount - (float) ($booking->discount ?? 0);
        $serviceLabel = trim((string) ($booking->service ?? '')) ?: 'N/A';
        $petNotes = trim((string) ($pet?->notes ?? ''));
        $iconBase = asset('images/business-hub');
    @endphp

    @teleport('body')
        <div class="pending-detail-overlay" wire:click.self="closeDetailsModal" wire:keydown.escape.window="closeDetailsModal"
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
            <article class="pending-detail-modal" role="dialog" aria-modal="true" aria-labelledby="pending-detail-title">
                <header class="pending-detail-header">
                    <div class="pending-detail-heading">
                        <h2 id="pending-detail-title">Pending Request</h2>
                        <div class="pending-detail-id-row">
                            <span class="pending-detail-id">{{ $bookingIdLabel }}</span>
                            <span class="pending-detail-status">Pending</span>
                        </div>
                    </div>
                    <button type="button" class="pending-detail-close" wire:click="closeDetailsModal" aria-label="Close modal">
                        <img src="{{ $iconBase }}/icon-decline-close.svg" alt="" width="14" height="14">
                    </button>
                </header>

                <div class="pending-detail-scroll">
                    <section class="pending-detail-client">
                        <div class="pending-detail-client-main">
                            <span class="pending-detail-avatar {{ $ownerPhoto ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                                @if ($ownerPhoto)
                                    <svg class="bh-avatar__ring" data-bh-avatar-ring viewBox="0 0 60 60" fill="none" aria-hidden="true">
                                        <circle cx="30" cy="30" r="29.5" fill="white" stroke="currentColor" />
                                    </svg>
                                    <img class="bh-avatar__photo" src="{{ $ownerPhoto }}" alt="{{ $ownerName }}"
                                        onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                                    <span data-bh-avatar-fallback hidden>{{ $ownerInitials }}</span>
                                @else
                                    <span data-bh-avatar-fallback>{{ $ownerInitials }}</span>
                                @endif
                            </span>
                            <div class="pending-detail-client-copy">
                                <div class="pending-detail-client-name">
                                    <strong>{{ $ownerName }}</strong>
                                    @if ($previousVisits === 0)
                                        <span class="pending-detail-new-client">New Client</span>
                                    @endif
                                </div>
                                <p>{{ $previousVisits === 0 ? 'No last visits recorded' : $previousVisits . ' previous ' . \Illuminate\Support\Str::plural('visit', $previousVisits) }}</p>
                            </div>
                        </div>
                        <button type="button" class="pending-detail-outline-btn pending-detail-message-sm">
                            <img src="{{ $iconBase }}/icon-message.svg" alt="" width="16" height="15" class="pending-detail-icon-message">
                            Message
                        </button>
                    </section>

                    <section class="pending-detail-section">
                        <h3>Booking details</h3>
                        <div class="pending-detail-grid">
                            <div class="pending-detail-field">
                                <span>Service</span>
                                <strong>
                                    <img src="{{ $iconBase }}/icon-reschedule-service.svg" alt="" width="15" height="16" class="pending-detail-icon-service">
                                    {{ $serviceLabel }}
                                </strong>
                            </div>
                            <div class="pending-detail-field">
                                <span>Date</span>
                                <strong>
                                    <img src="{{ $iconBase }}/icon-reschedule-date.svg" alt="" width="18" height="17" class="pending-detail-icon-date">
                                    {{ $dateLabel }}
                                </strong>
                            </div>
                            <div class="pending-detail-field">
                                <span>Time</span>
                                <strong>
                                    <img src="{{ $iconBase }}/icon-space-booking-clock.svg" alt="" width="16" height="16" class="pending-detail-icon-time">
                                    {{ $timeLabel }}
                                </strong>
                            </div>
                            <div class="pending-detail-field">
                                <span>Location</span>
                                <strong>
                                    <img src="{{ $iconBase }}/icon-location.svg" alt="" width="12" height="16" class="pending-detail-icon-location">
                                    {{ $locationLabel }}
                                </strong>
                            </div>
                        </div>
                    </section>

                    <section class="pending-detail-section pending-detail-addons">
                        <h3>Add-ons</h3>
                        <p>{{ $addOnsLabel }}</p>
                    </section>

                    <section class="pending-detail-section">
                        <h3>Pet</h3>
                        <div class="pending-detail-pet-card">
                            <div class="pending-detail-pet-main">
                                <span class="pending-detail-pet-avatar {{ $petPhoto ? 'has-photo' : 'is-fallback' }}" data-bh-avatar>
                                    @if ($petPhoto)
                                        <svg class="bh-avatar__ring" data-bh-avatar-ring viewBox="0 0 60 60" fill="none" aria-hidden="true">
                                            <circle cx="30" cy="30" r="29.5" fill="white" stroke="currentColor" />
                                        </svg>
                                        <img class="bh-avatar__photo" src="{{ $petPhoto }}" alt="{{ $petName }}"
                                            onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
                                        <span data-bh-avatar-fallback hidden>{{ $petInitials }}</span>
                                    @else
                                        <span data-bh-avatar-fallback>{{ $petInitials }}</span>
                                    @endif
                                </span>
                                <div>
                                    <strong>{{ $petName }}</strong>
                                    <p>{{ $petMeta }}</p>
                                </div>
                            </div>
                            @if ($petNotes !== '')
                                <div class="pending-detail-note">
                                    <img src="{{ $iconBase }}/icon-pet-notes.svg" alt="" width="15" height="15" class="pending-detail-icon-note">
                                    <p>{{ $petNotes }}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <div class="pending-detail-total">
                        <span>Total Paid</span>
                        <strong>£{{ number_format($total, 2) }}</strong>
                    </div>
                </div>

                <footer class="pending-detail-footer">
                    <button type="button" class="pending-detail-cancel">Cancel Booking</button>
                    <div class="pending-detail-footer-actions">
                        <button type="button" class="pending-detail-outline-btn pending-detail-message-lg">
                            <img src="{{ $iconBase }}/icon-message.svg" alt="" width="16" height="15" class="pending-detail-icon-message">
                            Message groomer
                        </button>
                        <button type="button" class="pending-detail-change">
                            <img src="{{ $iconBase }}/icon-change-booking.svg" alt="" width="16" height="16" class="pending-detail-icon-change">
                            Change booking
                        </button>
                    </div>
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

        .pending-detail-overlay {
            position: fixed;
            inset: 0;
            z-index: 2147483010;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(59, 55, 49, .18);
            font-family: Lato, sans-serif;
        }

        .pending-detail-modal {
            width: min(659px, calc(100vw - 32px));
            max-width: 100%;
            max-height: calc(100vh - 48px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            border: 1px solid #D8E8B7;
            border-radius: 10px;
            color: #3B3731;
            box-sizing: border-box;
        }

        .pending-detail-header {
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 40px 19px 20px;
            min-width: 0;
        }

        .pending-detail-heading {
            min-width: 0;
            flex: 1 1 auto;
        }

        .pending-detail-header h2 {
            margin: 0;
            color: #3B3731;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
            line-height: 25px;
            overflow-wrap: anywhere;
        }

        .pending-detail-id-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
            min-width: 0;
        }

        .pending-detail-id {
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            overflow-wrap: anywhere;
        }

        .pending-detail-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 25px;
            padding: 4px 10px;
            border-radius: 100px;
            background: rgba(255, 201, 122, .2);
            color: #FFBA55;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            white-space: nowrap;
        }

        .pending-detail-close {
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            margin-top: -4px;
            margin-right: -4px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .pending-detail-close img {
            display: block;
            width: 14px;
            height: 14px;
        }

        .pending-detail-scroll {
            min-width: 0;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 0 19px 8px;
        }

        .pending-detail-client {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 0;
            border-top: 1px solid #F0EAD9;
            border-bottom: 1px solid #F0EAD9;
            min-width: 0;
        }

        .pending-detail-client-main,
        .pending-detail-pet-main,
        .pending-detail-client-name,
        .pending-detail-outline-btn,
        .pending-detail-field strong,
        .pending-detail-footer-actions {
            display: flex;
            align-items: center;
        }

        .pending-detail-client-main {
            gap: 16px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .pending-detail-avatar,
        .pending-detail-pet-avatar {
            position: relative;
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            overflow: hidden;
            border-radius: 50%;
            color: #FDFDFD;
            text-align: center;
            font-family: Lato, sans-serif;
            font-size: 20px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .pending-detail-avatar.is-fallback,
        .pending-detail-pet-avatar.is-fallback {
            background: var(--bh-avatar-bg, #FFC97A);
        }

        .pending-detail-avatar.has-photo,
        .pending-detail-pet-avatar.has-photo {
            background: #fff;
            overflow: visible;
            color: var(--bh-avatar-ring, var(--bh-avatar-bg, #FFC97A));
        }

        .pending-detail-avatar {
            width: 60px;
            height: 60px;
            border: 0;
        }

        .pending-detail-avatar .bh-avatar__ring,
        .pending-detail-pet-avatar .bh-avatar__ring {
            position: absolute;
            inset: 0;
            width: 100% !important;
            height: 100% !important;
            max-width: none;
            pointer-events: none;
            z-index: 0;
            color: var(--bh-avatar-ring, var(--bh-avatar-bg, #FFC97A));
        }

        .pending-detail-avatar .bh-avatar__photo,
        .pending-detail-pet-avatar .bh-avatar__photo {
            position: absolute !important;
            top: 2px;
            left: 2px;
            z-index: 1;
            width: calc(100% - 4px) !important;
            height: calc(100% - 4px) !important;
            max-width: none !important;
            margin: 0 !important;
            border-radius: 50%;
            object-fit: cover !important;
            object-position: center;
            display: block;
        }

        .pending-detail-avatar img,
        .pending-detail-pet-avatar img {
            object-fit: cover;
        }

        .pending-detail-client-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .pending-detail-client-name {
            flex-wrap: wrap;
            gap: 8px;
            min-width: 0;
        }

        .pending-detail-client-name strong {
            color: #3B3731;
            font-size: 18px;
            font-weight: 600;
            line-height: 25px;
            overflow-wrap: break-word;
        }

        .pending-detail-new-client {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 100px;
            background: rgba(186, 207, 142, .1);
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 500;
            line-height: 1;
            white-space: nowrap;
        }

        .pending-detail-client-main p,
        .pending-detail-pet-main p {
            margin: 0;
            color: #9D9B98;
            font-size: 14px;
            font-weight: 400;
            line-height: 25px;
            overflow-wrap: break-word;
        }

        .pending-detail-outline-btn {
            flex: 0 0 auto;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
            border: 1px solid #F2F2F2;
            border-radius: 100px;
            background: #fff;
            color: #3B3731;
            font: 500 14px Lato, sans-serif;
            white-space: nowrap;
            cursor: pointer;
            box-sizing: border-box;
        }

        .pending-detail-message-sm {
            min-width: 120px;
            width: auto;
        }

        .pending-detail-message-lg {
            min-width: 176px;
            width: auto;
        }

        .pending-detail-outline-btn img,
        .pending-detail-change img,
        .pending-detail-field img,
        .pending-detail-note img,
        .pending-detail-close img {
            display: block;
            flex: 0 0 auto;
            width: 16px !important;
            height: auto !important;
            max-width: 16px !important;
            max-height: 16px;
            object-fit: contain;
        }

        .pending-detail-close img {
            width: 14px !important;
            max-width: 14px !important;
        }

        .pending-detail-icon-service {
            width: 15px !important;
            max-width: 15px !important;
        }

        .pending-detail-icon-date {
            width: 18px !important;
            max-width: 18px !important;
        }

        .pending-detail-icon-time {
            width: 16px !important;
            max-width: 16px !important;
        }

        .pending-detail-icon-location {
            width: 12px !important;
            max-width: 12px !important;
        }

        .pending-detail-icon-note {
            width: 15px !important;
            max-width: 15px !important;
        }

        .pending-detail-icon-message {
            width: 16px !important;
            max-width: 16px !important;
        }

        .pending-detail-icon-change {
            width: 16px !important;
            max-width: 16px !important;
        }

        .pending-detail-section {
            margin-top: 20px;
            min-width: 0;
        }

        .pending-detail-section h3 {
            margin: 0 0 20px;
            color: #C9C9C9;
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
            text-transform: uppercase;
        }

        .pending-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 20px;
            min-width: 0;
        }

        .pending-detail-field {
            min-width: 0;
            min-height: 68px;
            padding: 10px 20px;
            border-radius: 10px;
            background: #F7F4EC;
            box-sizing: border-box;
        }

        .pending-detail-field>span {
            display: block;
            margin-bottom: 10px;
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: 19px;
        }

        .pending-detail-field strong {
            gap: 10px;
            min-width: 0;
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: 19px;
            overflow-wrap: break-word;
        }

        .pending-detail-addons p {
            margin: 0;
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: 19px;
            overflow-wrap: break-word;
        }

        .pending-detail-pet-card {
            min-width: 0;
            padding: 20px;
            border-radius: 10px;
            background: #F7F4EC;
            box-sizing: border-box;
        }

        .pending-detail-pet-main {
            gap: 16px;
            min-width: 0;
        }

        .pending-detail-pet-main>div {
            min-width: 0;
            flex: 1 1 auto;
        }

        .pending-detail-pet-avatar {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .pending-detail-pet-main strong {
            display: block;
            color: #3B3731;
            font-size: 16px;
            font-weight: 600;
            line-height: 19px;
            overflow-wrap: break-word;
        }

        .pending-detail-pet-main p {
            line-height: 19px;
        }

        .pending-detail-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 10px;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .pending-detail-note img {
            margin-top: 2px;
        }

        .pending-detail-note p {
            margin: 0;
            min-width: 0;
            flex: 1 1 auto;
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: 19px;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .pending-detail-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 28px;
            padding: 18px 0 24px;
            border-top: 1px solid #F0EAD9;
            min-width: 0;
        }

        .pending-detail-total span {
            min-width: 0;
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
        }

        .pending-detail-total strong {
            flex: 0 1 auto;
            min-width: 0;
            color: #3B3731;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 600;
            line-height: 25px;
            overflow-wrap: break-word;
            text-align: right;
        }

        .pending-detail-footer {
            flex: 0 0 auto;
            min-width: 0;
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 19px;
            border-top: 1px solid #D8E8B7;
            background: #F5F9ED;
            box-sizing: border-box;
            overflow: hidden;
        }

        .pending-detail-cancel {
            flex: 0 0 auto;
            padding: 0;
            border: 0;
            background: transparent;
            color: #777572;
            font: 500 14px Lato, sans-serif;
            text-decoration: underline;
            text-underline-offset: 3px;
            cursor: pointer;
            white-space: nowrap;
        }

        .pending-detail-footer-actions {
            flex: 0 1 auto;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 12px;
            min-width: 0;
        }

        .pending-detail-change {
            flex: 0 0 auto;
            min-width: 165px;
            width: auto;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 18px;
            border: 0;
            border-radius: 100px;
            background: #3B3731;
            color: #fff;
            font: 500 14px Lato, sans-serif;
            white-space: nowrap;
            cursor: pointer;
            box-sizing: border-box;
        }

        @media (max-width: 700px) {
            .pending-detail-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .pending-detail-modal {
                width: 100%;
                max-height: calc(100vh - 18px);
                border-radius: 14px 14px 0 0;
            }

            .pending-detail-header,
            .pending-detail-scroll,
            .pending-detail-footer {
                padding-left: 20px;
                padding-right: 20px;
            }

            .pending-detail-header {
                padding-top: 28px;
            }

            .pending-detail-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
            }

            .pending-detail-client,
            .pending-detail-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .pending-detail-client>.pending-detail-outline-btn {
                align-self: flex-start;
            }

            .pending-detail-footer-actions {
                width: 100%;
                justify-content: stretch;
            }

            .pending-detail-message-lg,
            .pending-detail-change {
                flex: 1 1 auto;
                width: auto;
                min-width: 0;
                padding-inline: 12px;
            }

            .pending-detail-total {
                margin-top: 20px;
            }
        }

        @media (max-width: 520px) {
            .pending-detail-footer-actions {
                width: 100%;
            }

            .pending-detail-message-sm,
            .pending-detail-message-lg,
            .pending-detail-change {
                width: 100%;
            }
        }
    </style>
@endonce
