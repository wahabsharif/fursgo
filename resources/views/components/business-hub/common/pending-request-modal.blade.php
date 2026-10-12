@props(['booking' => null])

@if ($booking)
    @php
        $iconBase = asset('images/business-hub');
        $pets = $booking->relationLoaded('pets') ? $booking->pets : $booking->pets()->get();

        $created = $booking->created_at ?? now();
        $deadline = $created->copy()->addHours(48);
        $seconds = $deadline->getTimestamp() - now()->getTimestamp();
        if ($seconds <= 0) {
            $expiresLabel = 'Expired';
        } elseif ($seconds < 3600) {
            $expiresLabel = 'Expires in ' . max(1, (int) ceil($seconds / 60)) . 'm';
        } elseif ($seconds < 86400) {
            $expiresLabel = 'Expires in ' . max(1, (int) ceil($seconds / 3600)) . 'h';
        } else {
            $expiresLabel = 'Expires in ' . max(1, (int) ceil($seconds / 86400)) . 'd';
        }

        $serviceLabel = trim((string) ($booking->service ?? '')) ?: 'Service';
        $bookingDateLabel = $booking->date?->format('D j, M') ?? 'N/A';
        $dateLabel = $booking->date?->format('D j F') ?? 'N/A';
        $timeRaw = trim((string) ($booking->time ?? ''));
        $timeLabel = $timeRaw !== '' ? $timeRaw : 'N/A';
        $durationLabel = '1hr';
        if ($timeRaw !== '' && !str_contains(strtolower($timeRaw), 'am') && !str_contains(strtolower($timeRaw), 'pm')) {
            try {
                $start = \Carbon\Carbon::createFromFormat('H:i', substr($timeRaw, 0, 5));
                $timeLabel = strtolower($start->format('g:ia'));
            } catch (\Throwable) {
                // keep raw
            }
        }
        $dateTimeLabel = $dateLabel . ' · ' . $timeLabel . ' (' . $durationLabel . ')';

        $visitRaw = str_replace('_', ' ', strtolower(trim((string) ($booking->visit_type ?? ''))));
        $locationLabel = match (true) {
            str_contains($visitRaw, 'home') => 'Home Visit',
            str_contains($visitRaw, 'salon') => 'Salon',
            str_contains($visitRaw, 'mobile') => 'Mobile station',
            str_contains($visitRaw, 'garden'), str_contains($visitRaw, 'shed') => 'Garden / Shed',
            $visitRaw !== '' => ucwords($visitRaw),
            default => 'N/A',
        };

        $extraAddOns = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(function ($item) {
                $label = trim((string) data_get($item, 'label', data_get($item, 'name', '')));
                $amount = (float) data_get($item, 'amount', 0);

                return $label !== '' ? ['label' => $label, 'amount' => $amount] : null;
            })
            ->filter()
            ->values();
        $extraAmount = $extraAddOns->sum('amount');
        $serviceAmount = (float) ($booking->amount ?? 0);
        $discount = (float) ($booking->discount ?? 0);
        $total = $serviceAmount + $extraAmount - $discount;

        $formatWeight = static function ($weight): string {
            $weight = trim((string) $weight);
            if ($weight === '' || !is_numeric($weight)) {
                return '';
            }

            return rtrim(rtrim(number_format((float) $weight, 2, '.', ''), '0'), '.') . 'kg';
        };

        $subtitle = 'Booking date: ' . $bookingDateLabel . ' · ' . $expiresLabel;
    @endphp

    @teleport('body')
        <div class="bh-request-overlay" wire:click.self="closeDetailsModal" wire:keydown.escape.window="closeDetailsModal"
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
            <article class="bh-request-modal" role="dialog" aria-modal="true" aria-labelledby="bh-request-title">
                <header class="bh-request-header">
                    <div class="bh-request-heading">
                        <h2 id="bh-request-title">Booking Request</h2>
                        <p class="bh-request-subtitle">{{ $subtitle }}</p>
                    </div>
                    <button type="button" class="bh-request-close" wire:click="closeDetailsModal" aria-label="Close modal">
                        <img src="{{ $iconBase }}/icon-request-modal-close.svg" alt="" width="20" height="20">
                    </button>
                </header>

                <div class="bh-request-scroll">
                    <section class="bh-request-fields">
                        <div class="bh-request-row">
                            <span>Service</span>
                            <strong>{{ $serviceLabel }}</strong>
                        </div>
                        <div class="bh-request-row">
                            <span>Date &amp; Time</span>
                            <strong>{{ $dateTimeLabel }}</strong>
                        </div>
                        <div class="bh-request-row">
                            <span>Location</span>
                            <strong>{{ $locationLabel }}</strong>
                        </div>
                    </section>

                    <section class="bh-request-pets-section">
                        <h3>Pets</h3>
                        <div class="bh-request-pets">
                            @forelse ($pets as $pet)
                                @php
                                    $petName = trim((string) ($pet->name ?? 'Pet')) ?: 'Pet';
                                    $petType = trim((string) ($pet->pet_type ?? ($pet->type ?? ($pet->species ?? 'Pet'))));
                                    $petBreed = trim((string) ($pet->breed ?? ''));
                                    $petSex = trim((string) ($pet->sex ?? ''));
                                    $petWeight = $formatWeight($pet->weight ?? '');
                                    $petLine = $petName;
                                    if ($petType !== '') {
                                        $petLine .= ' - ' . $petType;
                                        if ($petBreed !== '') {
                                            $petLine .= ' (' . $petBreed . ')';
                                        }
                                    }
                                    if ($petSex !== '') {
                                        $petLine .= ' · ' . ucfirst($petSex);
                                    }
                                    if ($petWeight !== '') {
                                        $petLine .= ' · ' . $petWeight;
                                    }
                                    $petPhoto = \App\Support\BusinessHubAvatar::mediaUrl($pet->photo ?? null);
                                    $petInitials = \App\Support\BusinessHubAvatar::initials($petName);
                                @endphp
                                <div class="bh-request-pet">
                                    @if ($petPhoto)
                                        <span class="bh-request-pet-avatar has-photo" aria-hidden="true">
                                            <img src="{{ $petPhoto }}" alt="" width="16" height="16" loading="lazy"
                                                decoding="async"
                                                onerror="this.hidden=true; this.nextElementSibling.hidden=false; this.parentElement.classList.remove('has-photo'); this.parentElement.classList.add('is-fallback');">
                                            <span class="bh-request-pet-avatar__initials" hidden>{{ $petInitials }}</span>
                                        </span>
                                    @else
                                        <span class="bh-request-pet-avatar is-fallback" aria-hidden="true">
                                            <span class="bh-request-pet-avatar__initials">{{ $petInitials }}</span>
                                        </span>
                                    @endif
                                    <p>{{ $petLine }}</p>
                                </div>
                            @empty
                                <p class="bh-request-empty">No pets listed on this request.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="bh-request-price">
                        <div class="bh-request-price-block">
                            <p class="bh-request-price-heading">Service</p>
                            <div class="bh-request-price-row">
                                <span>{{ $serviceLabel }}</span>
                                <strong>£{{ number_format($serviceAmount, 2) }}</strong>
                            </div>
                        </div>

                        @if ($extraAddOns->isNotEmpty())
                            <div class="bh-request-price-block">
                                <p class="bh-request-price-heading">Extras &amp; Add-ons</p>
                                @foreach ($extraAddOns as $addon)
                                    <div class="bh-request-price-row">
                                        <span>{{ $addon['label'] }}</span>
                                        <strong>£{{ number_format((float) $addon['amount'], 2) }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($discount > 0)
                            <div class="bh-request-price-row">
                                <span>Discount</span>
                                <strong>-£{{ number_format($discount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="bh-request-price-total">
                            <span>Total paid</span>
                            <strong>£{{ number_format($total, 2) }}</strong>
                        </div>
                    </section>
                </div>

                <footer class="bh-request-footer">
                    <button type="button" class="bh-request-decline"
                        wire:click="openDeclineFromOverview({{ (int) $booking->id }})">Decline</button>
                    <button type="button" class="bh-request-accept"
                        wire:click="acceptRequest({{ (int) $booking->id }})">
                        <img src="{{ $iconBase }}/icon-request-accept-check.svg" alt="" width="13" height="9">
                        Accept Request
                    </button>
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

        .bh-request-overlay {
            position: fixed;
            inset: 0;
            z-index: 2147483010;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(59, 55, 49, 0.1);
            font-family: Lato, sans-serif;
        }

        .bh-request-modal {
            width: min(505px, calc(100vw - 32px));
            max-height: min(607px, calc(100vh - 48px));
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        }

        .bh-request-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 20px 18px;
            border-bottom: 1px solid #E2E2E2;
            flex-shrink: 0;
        }

        .bh-request-heading h2 {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 24px;
            font-weight: 600;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-request-subtitle {
            margin: 12px 0 0;
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-request-close {
            width: 20px;
            height: 20px;
            margin-top: 0;
            border: 0;
            padding: 0;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .bh-request-close img {
            width: 20px;
            height: 20px;
            display: block;
        }

        .bh-request-scroll {
            padding: 20px;
            overflow: auto;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1 1 auto;
            min-height: 0;
        }

        .bh-request-fields {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .bh-request-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 16px;
            min-height: 19px;
        }

        .bh-request-row span {
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .bh-request-row strong {
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            text-align: right;
        }

        .bh-request-pets-section h3 {
            margin: 0 0 20px;
            color: #3B3731;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-request-pets {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bh-request-pet {
            display: flex;
            align-items: center;
            gap: 5px;
            min-width: 0;
        }

        .bh-request-pet-avatar {
            width: 16px;
            height: 16px;
            border-radius: 199.5px;
            overflow: hidden;
            flex-shrink: 0;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            background: transparent;
            border: 0;
            padding: 0;
        }

        .bh-request-pet-avatar.is-fallback {
            background: var(--bh-avatar-bg, #FFC97A);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bh-request-pet-avatar img {
            width: 16px !important;
            height: 16px !important;
            max-width: none !important;
            margin: 0 !important;
            object-fit: cover;
            object-position: center;
            border-radius: 199.5px;
            display: block;
        }

        .bh-request-pet-avatar__initials {
            color: #FDFDFD;
            font-family: Lato, sans-serif;
            font-size: 8px;
            font-weight: 800;
            line-height: 1;
        }

        .bh-request-pet p,
        .bh-request-empty {
            margin: 0;
            color: #3B3731;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            text-box-edge: cap alphabetic;
            text-box-trim: trim-both;
        }

        .bh-request-price {
            background: #F9F9F9;
            border-radius: 5px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bh-request-price-block {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .bh-request-price-heading {
            margin: 0;
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: 20px;
        }

        .bh-request-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #9D9B98;
            font-size: 16px;
            line-height: 20px;
        }

        .bh-request-price-row strong {
            color: #3B3731;
            font-weight: 400;
        }

        .bh-request-price-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .bh-request-price-total span {
            color: #3B3731;
            font-size: 16px;
            font-weight: 600;
            line-height: 20px;
        }

        .bh-request-price-total strong {
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 20px;
            font-weight: 700;
            line-height: 20px;
        }

        .bh-request-footer {
            display: flex;
            gap: 20px;
            padding: 0 20px 20px;
            flex-shrink: 0;
        }

        .bh-request-decline,
        .bh-request-accept {
            height: 42px;
            border-radius: 75px;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            padding: 0;
            box-sizing: border-box;
        }

        .bh-request-decline {
            flex: 0 0 139px;
            width: 139px;
            border: 1px solid #FE6F56;
            background: rgba(254, 111, 86, 0.1);
            color: #FE6F56;
        }

        .bh-request-accept {
            flex: 1 1 auto;
            min-width: 0;
            border: 0;
            background: #C9DDA0;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .bh-request-accept img {
            width: 13px;
            height: 9px;
            display: block;
            flex-shrink: 0;
        }
    </style>
@endonce
