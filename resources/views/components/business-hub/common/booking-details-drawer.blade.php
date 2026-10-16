@php
    $iconBase = asset('images/business-hub');
    $detailIconBase = asset('images/booking-details');
@endphp

@teleport('body')
    <div class="booking-details-drawer-layer" x-cloak
        x-bind:class="{ 'is-open': open, 'is-space': isSpace, [details.statusClass || '']: !!details.statusClass }">
        <button type="button" class="booking-details-drawer-backdrop" @click="closeDrawer()"
            aria-label="Close booking details"></button>

        <aside class="booking-details-drawer" role="dialog" aria-modal="true"
            aria-labelledby="booking-details-drawer-title" x-bind:style="{ top: topOffset + 'px' }" @click.stop
            @wheel.stop.passive @touchmove.stop>
            <header class="bdd-head">
                <div class="bdd-head-copy">
                    <h3 id="booking-details-drawer-title" class="bdd-title">Booking details</h3>
                    <p class="bdd-meta">
                        <span x-text="details.headerMeta"></span>
                        <template x-if="details.status === 'pending' && details.expiresLabel">
                            <span class="bdd-meta-expire"> · <span x-text="details.expiresLabel"></span></span>
                        </template>
                    </p>
                </div>
                <div class="bdd-head-actions">
                    <span class="bdd-status-chip" x-bind:class="details.statusClass" x-text="details.statusLabel"></span>
                    <button type="button" class="bdd-close" @click="closeDrawer()" aria-label="Close">
                        <img src="{{ $detailIconBase }}/close.svg" alt="" width="20" height="20">
                    </button>
                </div>
            </header>

            <div class="bdd-scroll">
                <div class="bdd-cancel-banner" x-show="details.status === 'cancelled' && details.cancelBanner" x-cloak>
                    <img class="bdd-cancel-icon" src="{{ $iconBase }}/icon-cancel-banner-x.svg" alt="" width="11" height="11" aria-hidden="true">
                    <p class="bdd-cancel-copy">
                        <strong class="bdd-cancel-title" x-text="details.cancelBanner?.title"></strong>
                        <br>
                        <span class="bdd-cancel-sub" x-text="details.cancelBanner?.subtitle"></span>
                    </p>
                </div>

                <section class="bdd-section">
                    <h4 class="bdd-section-label" x-text="isSpace ? 'Client' : 'Owner'"></h4>
                    <div class="bdd-owner-card">
                        <div class="bdd-owner-main">
                            <div class="bdd-avatar" data-bh-avatar
                                x-bind:class="{ 'has-photo': details.ownerImageUrl && !details.ownerImageFailed, 'is-fallback': !details.ownerImageUrl || details.ownerImageFailed }">
                                <img x-show="details.ownerImageUrl && !details.ownerImageFailed"
                                    x-bind:src="details.ownerImageUrl" alt="" class="bdd-avatar__photo"
                                    x-on:error="details.ownerImageFailed = true">
                                <span x-show="!details.ownerImageUrl || details.ownerImageFailed"
                                    class="bdd-avatar__initials" x-text="details.ownerInitial"></span>
                            </div>
                            <div class="bdd-owner-copy">
                                <div class="bdd-owner-name-row">
                                    <strong x-text="details.ownerName"></strong>
                                    <span class="bdd-client-chip" x-show="details.clientTier" x-cloak
                                        x-bind:class="'is-' + (details.clientTierTone || 'new')"
                                        x-text="details.clientTier"></span>
                                </div>
                                <p class="bdd-owner-meta" x-text="details.ownerMeta"></p>
                            </div>
                        </div>
                        <button type="button" class="bdd-message-btn">
                            <img src="{{ $iconBase }}/icon-confirmed-message.svg" alt="" width="14" height="11">
                            <span>Message</span>
                        </button>
                    </div>
                </section>

                <section class="bdd-section">
                    <h4 class="bdd-section-label">Appointment Details</h4>
                    <div class="bdd-appt-grid">
                        <div class="bdd-appt-card">
                            <span class="bdd-appt-label">Service</span>
                            <div class="bdd-appt-value">
                                <img x-show="!isSpace" src="{{ $iconBase }}/icon-confirmed-service.svg" alt="" width="15" height="16" class="is-service">
                                <img x-show="isSpace" x-cloak x-bind:src="details.serviceIconUrl || '{{ $iconBase }}/icon-space-service-1h.svg'" alt="" width="18" height="15" class="is-space-service">
                                <strong x-text="details.serviceLabel"></strong>
                            </div>
                        </div>
                        <div class="bdd-appt-card">
                            <span class="bdd-appt-label">Date</span>
                            <div class="bdd-appt-value">
                                <img src="{{ $iconBase }}/icon-confirmed-date.svg" alt="" width="18" height="17" class="is-date">
                                <strong x-text="details.dateLabel"></strong>
                            </div>
                        </div>
                        <div class="bdd-appt-card">
                            <span class="bdd-appt-label">Time</span>
                            <div class="bdd-appt-value">
                                <img src="{{ $iconBase }}/icon-confirmed-clock.svg" alt="" width="16" height="16" class="is-time">
                                <strong x-text="details.timeLabel"></strong>
                            </div>
                        </div>
                        <div class="bdd-appt-card">
                            <span class="bdd-appt-label" x-text="isSpace ? 'Space' : 'Location'"></span>
                            <div class="bdd-appt-value">
                                <img x-show="!isSpace" src="{{ $iconBase }}/icon-location.svg" alt="" width="12" height="16" class="is-location">
                                <img x-show="isSpace" src="{{ $iconBase }}/icon-space-shed.svg" alt="" width="17" height="14" class="is-space-type" x-cloak>
                                <strong x-text="details.locationLabel"></strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bdd-section" x-show="(details.pets || []).length" x-cloak>
                    <h4 class="bdd-section-label">Pet/s</h4>
                    <div class="bdd-pets">
                        <template x-for="(pet, index) in (details.pets || [])" :key="'pet-' + index">
                            <div class="bdd-pet-card">
                                <div class="bdd-avatar is-pet" data-bh-avatar
                                    x-bind:class="{ 'has-photo': pet.photoUrl && !pet.imageFailed, 'is-fallback': !pet.photoUrl || pet.imageFailed }">
                                    <img x-show="pet.photoUrl && !pet.imageFailed" x-bind:src="pet.photoUrl" alt=""
                                        class="bdd-avatar__photo" x-on:error="pet.imageFailed = true">
                                    <span x-show="!pet.photoUrl || pet.imageFailed" class="bdd-avatar__initials"
                                        x-text="pet.initial"></span>
                                </div>
                                <div class="bdd-pet-copy">
                                    <div class="bdd-pet-title-row">
                                        <img src="{{ $detailIconBase }}/paw.svg" alt="" width="17" height="15">
                                        <p class="bdd-pet-title">
                                            <strong x-text="pet.name"></strong>
                                            <template x-if="pet.breed">
                                                <span> · <span class="bdd-pet-breed" x-text="pet.breed"></span></span>
                                            </template>
                                        </p>
                                    </div>
                                    <p class="bdd-pet-meta">
                                        <span x-show="pet.sex" x-text="pet.sex"></span>
                                        <span x-show="pet.sex && pet.weight"> · </span>
                                        <span x-show="pet.weight" x-text="pet.weight"></span>
                                        <template x-if="pet.notes">
                                            <span class="bdd-pet-note-inline">
                                                <img src="{{ $detailIconBase }}/note.svg" alt="" width="11" height="11">
                                                <span x-text="pet.notes"></span>
                                            </span>
                                        </template>
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <section class="bdd-section">
                    <h4 class="bdd-section-label">Service &amp; add-ons</h4>
                    <div class="bdd-lines">
                        <template x-for="(item, index) in (details.lineItems || [])" :key="'line-' + index">
                            <div class="bdd-line">
                                <span x-text="item.label"></span>
                                <strong x-text="'£' + item.amount"></strong>
                            </div>
                        </template>
                    </div>
                    <div class="bdd-earn" x-bind:class="details.statusClass">
                        <div class="bdd-earn-row">
                            <span x-text="details.subtotalLabel"></span>
                            <strong x-text="'£' + details.subtotal"></strong>
                        </div>
                        <div class="bdd-earn-row" x-show="details.status === 'cancelled' && details.refundedAmount" x-cloak>
                            <span>Refunded to customer</span>
                            <strong class="is-refund" x-text="'-£' + details.refundedAmount"></strong>
                        </div>
                        <div class="bdd-earn-total" x-bind:class="details.statusClass">
                            <span x-text="details.earnLabel"></span>
                            <strong x-text="'£' + details.earnAmount"></strong>
                        </div>
                    </div>
                </section>
            </div>

            <footer class="bdd-footer">
                <template x-if="details.status === 'pending'">
                    <div class="bdd-footer-row">
                        <button type="button" class="bdd-btn is-decline" @click="runAction('decline')">Decline</button>
                        <button type="button" class="bdd-btn is-accept" @click="runAction('accept')">
                            <img src="{{ $iconBase }}/icon-request-accept-check.svg" alt="" width="12" height="8">
                            <span>Accept Request</span>
                        </button>
                    </div>
                </template>
                <template x-if="details.status === 'confirmed'">
                    <div class="bdd-footer-row is-confirmed" x-bind:class="{ 'is-space-confirmed': isSpace }">
                        <button type="button" class="bdd-btn is-cancel" @click="runAction('cancel')">Cancel</button>
                        <button type="button" class="bdd-btn is-reschedule" @click="runAction('reschedule')">
                            <img src="{{ $iconBase }}/icon-booking-reschedule.svg" alt="" width="12" height="12">
                            <span>Reschedule</span>
                        </button>
                        <button type="button" class="bdd-btn is-directions" x-show="!isSpace" x-cloak @click="runAction('directions')">
                            <img src="{{ $iconBase }}/icon-directions-pin.svg" alt="" width="9" height="12">
                            <span>Directions</span>
                        </button>
                    </div>
                </template>
                <template x-if="details.status === 'completed' || details.status === 'cancelled'">
                    <div class="bdd-footer-row">
                        <button type="button" class="bdd-btn is-download" @click="runAction('download')">
                            <img src="{{ $iconBase }}/icon-booking-download-arrow.svg" alt="" width="12" height="14">
                            <span>Download receipt</span>
                        </button>
                    </div>
                </template>
            </footer>
        </aside>
    </div>
@endteleport

@teleport('body')
    <div class="bdd-directions-overlay" x-cloak x-show="directionsOpen" x-transition.opacity.duration.150ms
        @click.self="closeDirections()" role="presentation">
        <div class="bdd-directions-modal" role="dialog" aria-modal="true" aria-labelledby="bdd-directions-title"
            @click.stop>
            <button type="button" class="bdd-directions-close" @click="closeDirections()" aria-label="Close">
                <img src="{{ $iconBase }}/icon-directions-modal-close.svg" alt="" width="20" height="20">
            </button>

            <div class="bdd-directions-hero" aria-hidden="true">
                <img src="{{ $iconBase }}/icon-directions-modal-hero.svg" alt="" width="45" height="45">
            </div>

            <h3 id="bdd-directions-title" class="bdd-directions-title">Open directions</h3>

            <div class="bdd-directions-card">
                <p class="bdd-directions-address" x-text="details.directionsAddress || details.locationLabel || 'Address unavailable'"></p>
                <p class="bdd-directions-meta" x-show="details.directionsMeta" x-text="details.directionsMeta"></p>
            </div>

            <div class="bdd-directions-actions">
                <button type="button" class="bdd-directions-btn is-close" @click="closeDirections()">Close</button>
                <button type="button" class="bdd-directions-btn is-maps" @click="openInMaps()">
                    <img src="{{ $iconBase }}/icon-directions-external.svg" alt="" width="12" height="12">
                    <span>Open in Maps</span>
                </button>
            </div>
        </div>
    </div>
@endteleport

<style>
    .booking-details-drawer-layer {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        overscroll-behavior: none;
        display: none;
    }

    .booking-details-drawer-layer.is-open,
    .booking-details-drawer-layer[style*="display: block"] {
        display: block;
    }

    .booking-details-drawer-layer[x-cloak] {
        display: none !important;
    }

    .booking-details-drawer-backdrop {
        position: absolute;
        inset: 0;
        border: 0;
        background: rgba(59, 55, 49, 0.1);
        cursor: pointer;
    }

    .booking-details-drawer {
        position: absolute;
        right: 0;
        bottom: 0;
        z-index: 1;
        width: min(505px, 100vw);
        background: #fff;
        border-radius: 10px 0 0 10px;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .booking-details-drawer img {
        width: auto;
        max-width: none;
        height: auto;
        flex-shrink: 0;
    }

    .booking-details-drawer-layer.is-open .booking-details-drawer {
        animation: booking-details-drawer-in 120ms ease-out;
    }

    @keyframes booking-details-drawer-in {
        from {
            transform: translateX(20px);
            opacity: 0.9;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .bdd-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 20px 18px;
        flex-shrink: 0;
        border-bottom: 1px solid #F0EEEA;
    }

    .bdd-title {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-weight: 600;
        line-height: 1.15;
    }

    .bdd-meta {
        margin: 10px 0 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.4;
    }

    .bdd-meta-expire {
        color: #FFA899;
        font-weight: 400;
    }

    .bdd-head-actions {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        padding-top: 4px;
    }

    .bdd-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 30px;
        padding: 0 12px;
        border-radius: 100px;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
    }

    .bdd-status-chip.is-pending {
        background: rgba(255, 201, 122, 0.2);
        color: #FFBA55;
    }

    .bdd-status-chip.is-confirmed {
        background: rgba(201, 221, 160, 0.2);
        color: #AFCD6F;
    }

    .bdd-status-chip.is-completed {
        background: rgba(148, 190, 219, 0.2);
        color: #94BEDB;
    }

    .bdd-status-chip.is-cancelled {
        background: rgba(254, 111, 86, 0.12);
        color: #FE6F56;
    }

    .bdd-close {
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
        line-height: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        overflow: hidden;
    }

    .bdd-close img {
        width: 20px !important;
        height: 20px !important;
        display: block;
    }

    .bdd-scroll {
        flex: 1;
        overflow: auto;
        padding: 20px 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .bdd-cancel-banner {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 20px;
        border-radius: 10px;
        background: #FDEEEB;
        box-sizing: border-box;
        min-height: 97px;
    }

    .bdd-cancel-icon {
        width: 11px !important;
        height: 11px !important;
        margin-top: 4px;
        flex-shrink: 0;
        object-fit: contain;
        display: block;
    }

    .bdd-cancel-copy {
        margin: 0;
        min-width: 0;
        flex: 1;
        color: #FF6E6E;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .bdd-cancel-title {
        margin: 0;
        color: #FF6E6E;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .bdd-cancel-sub {
        color: #FF6E6E;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .bdd-section-label {
        margin: 0 0 14px;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .bdd-owner-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px;
        border-radius: 5px;
        background: #F9F9F9;
        border: 1px solid #F7F4EC;
        box-sizing: border-box;
    }

    .bdd-owner-main {
        display: flex;
        align-items: center;
        gap: 20px;
        min-width: 0;
    }

    .bdd-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        background: #FFC97A;
        box-sizing: border-box;
        border: 3px solid #FFC97A;
    }

    .bdd-avatar.has-photo {
        border-color: #FFC97A;
        background: #fff;
    }

    .bdd-avatar.is-pet {
        width: 48px;
        height: 48px;
        border-width: 0;
    }

    .bdd-avatar__photo {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        display: block;
    }

    .bdd-avatar__initials {
        width: 100%;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #FDFDFD;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-weight: 800;
    }

    .bdd-owner-name-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .bdd-owner-name-row strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
    }

    .bdd-client-chip {
        display: inline-flex;
        align-items: center;
        height: 28px;
        padding: 0 10px;
        border-radius: 100px;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
    }

    .bdd-client-chip.is-new {
        background: rgba(186, 207, 142, 0.1);
        color: #AFCD6F;
    }

    .bdd-client-chip.is-repeat {
        background: rgba(216, 229, 238, 0.2);
        color: #94BEDB;
    }

    .bdd-client-chip.is-regular {
        background: #FDF8F0;
        color: #F9C45C;
    }

    .bdd-owner-meta {
        margin: 4px 0 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 25px;
    }

    .bdd-message-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 42px;
        padding: 0 16px;
        border-radius: 75px;
        border: 1px solid #E2E2E2;
        background: #fff;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        flex-shrink: 0;
    }

    .bdd-message-btn img {
        width: 14px !important;
        height: 11px !important;
        object-fit: contain;
    }

    .bdd-appt-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px 20px;
    }

    .bdd-appt-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        padding: 12px 15px;
        border-radius: 10px;
        background: #F7F4EC;
        min-width: 0;
        min-height: 68px;
        box-sizing: border-box;
    }

    .bdd-appt-label {
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.2;
    }

    .bdd-appt-value {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .bdd-appt-value img {
        object-fit: contain;
        flex-shrink: 0;
    }

    .bdd-appt-value img.is-service {
        width: 15px !important;
        height: 16px !important;
    }

    .bdd-appt-value img.is-space-service {
        width: 18px !important;
        height: 15px !important;
    }

    .bdd-appt-value img.is-date {
        width: 18px !important;
        height: 17px !important;
    }

    .bdd-appt-value img.is-time {
        width: 16px !important;
        height: 16px !important;
    }

    .bdd-appt-value img.is-location {
        width: 12px !important;
        height: 16px !important;
    }

    .bdd-appt-value img.is-space-type {
        width: 17px !important;
        height: 14px !important;
    }

    .bdd-footer-row.is-confirmed.is-space-confirmed .bdd-btn.is-cancel {
        flex: 1 1 0;
        width: auto;
        min-width: 0;
    }

    .bdd-footer-row.is-confirmed.is-space-confirmed .bdd-btn.is-reschedule {
        flex: 1 1 0;
    }

    .bdd-appt-value strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.3;
        word-break: break-word;
    }

    .bdd-pets {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .bdd-pet-card {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border-radius: 5px;
        border: 1px solid #F7F4EC;
        background: #F9F9F9;
        box-sizing: border-box;
    }

    .bdd-pet-copy {
        min-width: 0;
        flex: 1;
    }

    .bdd-pet-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bdd-pet-title-row img {
        width: 17px !important;
        height: 15px !important;
        object-fit: contain;
    }

    .bdd-pet-title {
        margin: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        line-height: 1.2;
    }

    .bdd-pet-title strong {
        font-weight: 600;
    }

    .bdd-pet-breed {
        color: #9D9B98;
        font-weight: 400;
    }

    .bdd-pet-meta {
        margin: 8px 0 0;
        color: #9C9A97;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.35;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }

    .bdd-pet-note-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #9C9A97;
    }

    .bdd-pet-note-inline img {
        width: 11px !important;
        height: 11px !important;
        object-fit: contain;
    }

    .bdd-lines {
        display: flex;
        flex-direction: column;
        gap: 0;
        margin-bottom: 20px;
    }

    .bdd-line {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        color: #606060;
        font-family: Lato, sans-serif;
        font-size: 16px;
        line-height: 25px;
    }

    .bdd-line span {
        font-weight: 400;
    }

    .bdd-line strong {
        font-weight: 400;
        white-space: nowrap;
    }

    .bdd-earn {
        border-radius: 5px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .bdd-earn.is-pending {
        background: #FFF4E4;
    }

    .bdd-earn.is-confirmed {
        background: #F4F8EC;
    }

    .bdd-earn.is-completed {
        background: #EEF5FA;
    }

    .bdd-earn.is-cancelled {
        background: #FFF1EF;
    }

    .bdd-earn-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: #606060;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 25px;
    }

    .bdd-earn-row strong {
        color: #606060;
        font-weight: 600;
    }

    .bdd-earn-row .is-refund {
        color: #FE6F56;
    }

    .bdd-earn-total {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        margin-top: 10px;
        padding-top: 13px;
        border-top: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
    }

    .bdd-earn-total::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: #FFC97A;
    }

    .bdd-earn-total.is-pending::before {
        background: #FFC97A;
    }

    .bdd-earn-total.is-confirmed::before {
        background: #AFCD6F;
    }

    .bdd-earn-total.is-completed::before {
        background: #9FC7E4;
    }

    .bdd-earn-total.is-cancelled::before {
        background: #FE6F56;
    }

    .bdd-earn-total strong {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-weight: 600;
    }

    .bdd-footer {
        flex-shrink: 0;
        padding: 19px 20px 24px;
        border-top: 1px solid #F0EEEA;
        background: #fff;
    }

    .bdd-footer-row {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .bdd-footer-row.is-confirmed {
        justify-content: stretch;
        gap: 19px;
    }

    .bdd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 42px;
        padding: 0 18px;
        border-radius: 75px;
        border: none;
        cursor: pointer;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        white-space: nowrap;
    }

    .bdd-btn img {
        width: 12px;
        height: 8px;
        object-fit: contain;
    }

    .bdd-btn.is-decline {
        flex: 0 0 139px;
        width: 139px;
        background: #FFF1EF;
        color: #FE6F56;
    }

    .bdd-btn.is-accept {
        flex: 1 1 auto;
        background: #C9DDA0;
        color: #fff;
        border: 1px solid #AFCD6F;
    }

    .bdd-btn.is-accept img {
        width: 12px;
        height: 8px;
        filter: none;
    }

    .bdd-btn.is-cancel {
        flex: 0 0 88px;
        width: 88px;
        padding: 0 12px;
        background: #FFF1EF;
        color: #FF6E6E;
    }

    .bdd-btn.is-reschedule {
        flex: 1 1 169px;
        background: #F9F9F9;
        color: #3B3731;
    }

    .bdd-btn.is-reschedule img {
        width: 12px !important;
        height: 12px !important;
    }

    .bdd-btn.is-directions {
        flex: 1 1 169px;
        background: #FFC97A;
        color: #fff;
    }

    .bdd-btn.is-directions img {
        width: 9px !important;
        height: 12px !important;
        filter: none;
    }

    .bdd-btn.is-download {
        width: 100%;
        background: #3B3731;
        color: #fff;
    }

    .bdd-btn.is-download img {
        width: 12px;
        height: 14px;
        filter: brightness(0) invert(1);
    }

    @media (max-width: 640px) {
        .booking-details-drawer {
            width: 100vw;
            border-radius: 10px 10px 0 0;
        }

        .bdd-head,
        .bdd-scroll,
        .bdd-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .bdd-appt-grid {
            grid-template-columns: 1fr;
        }

        .bdd-footer-row {
            gap: 12px;
        }

        .bdd-btn.is-decline {
            flex: 0 0 auto;
            width: auto;
            min-width: 110px;
        }

        .bdd-footer-row.is-confirmed {
            flex-wrap: wrap;
            gap: 10px;
        }

        .bdd-footer-row.is-confirmed .bdd-btn.is-cancel {
            flex: 0 0 auto;
            width: auto;
            min-width: 88px;
        }

        .bdd-footer-row.is-confirmed .bdd-btn.is-reschedule,
        .bdd-footer-row.is-confirmed .bdd-btn.is-directions {
            flex: 1 1 auto;
        }
    }

    .bdd-directions-overlay[x-cloak] {
        display: none !important;
    }

    .bdd-directions-overlay {
        position: fixed;
        inset: 0;
        z-index: 2147483600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(59, 55, 49, 0.28);
        box-sizing: border-box;
    }

    .bdd-directions-modal {
        position: relative;
        width: 100%;
        max-width: 505px;
        min-height: 274px;
        padding: 20px;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .bdd-directions-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 20px;
        height: 20px;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
        line-height: 0;
    }

    .bdd-directions-close img {
        width: 20px !important;
        height: 20px !important;
        display: block;
        object-fit: contain;
    }

    .bdd-directions-hero {
        width: 45px;
        height: 45px;
        margin: 0 auto;
        line-height: 0;
    }

    .bdd-directions-hero img {
        width: 45px !important;
        height: 45px !important;
        display: block;
        object-fit: contain;
    }

    .bdd-directions-title {
        margin: 20px 0 0;
        color: #3B3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-weight: 600;
        line-height: normal;
        text-align: center;
    }

    .bdd-directions-card {
        width: 100%;
        margin-top: 20px;
        padding: 20px 16px;
        border-radius: 5px;
        background: #F9F9F9;
        box-sizing: border-box;
        text-align: center;
    }

    .bdd-directions-address {
        margin: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .bdd-directions-meta {
        margin: 4px 0 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .bdd-directions-actions {
        width: 100%;
        margin-top: 20px;
        display: flex;
        gap: 20px;
    }

    .bdd-directions-btn {
        flex: 1 1 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 42px;
        padding: 0 18px;
        border-radius: 75px;
        border: none;
        cursor: pointer;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        white-space: nowrap;
    }

    .bdd-directions-btn.is-close {
        background: #fff;
        border: 1px solid #E2E2E2;
        color: #9D9B98;
    }

    .bdd-directions-btn.is-maps {
        background: #FFC97A;
        color: #fff;
    }

    .bdd-directions-btn.is-maps img {
        width: 12px !important;
        height: 12px !important;
        object-fit: contain;
        filter: none;
    }

    @media (max-width: 640px) {
        .bdd-directions-modal {
            max-width: 100%;
        }

        .bdd-directions-actions {
            gap: 12px;
        }
    }
</style>
