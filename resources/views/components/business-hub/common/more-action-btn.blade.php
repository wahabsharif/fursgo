@props([
    'rowId',
    'menuWidth' => 210,
    'loadingEvent' => 'bookings-tabs-loading-start',
    'rescheduleMethod' => 'openRescheduleModal',
])

<div class="more-action-wrapper" x-data="{
    rowId: @js($rowId),
    openMore: false,
    menuLeft: 8,
    menuTop: 8,
    menuWidth: @js($menuWidth),
    repositionMore() {
        const rect = $refs.moreBtn.getBoundingClientRect();
        this.menuLeft = Math.min(Math.max(8, rect.left), window.innerWidth - this.menuWidth - 8);
        this.menuTop = Math.max(8, rect.bottom + 8);
    },
    toggleMore() {
        if (!this.openMore) {
            window.dispatchEvent(new CustomEvent('more-action-opened', { detail: { id: this.rowId } }));
            this.repositionMore();
        }
        this.openMore = !this.openMore;
    }
}"
    @more-action-opened.window="if (($event.detail?.id ?? null) !== rowId) { openMore = false }"
    @keydown.escape.window="openMore = false" @resize.window="if (openMore) repositionMore()"
    @scroll.window="if (openMore) repositionMore()"
    @click.window="if (openMore && !$refs.moreBtn.contains($event.target) && (!$refs.moreMenu || !$refs.moreMenu.contains($event.target))) { openMore = false }">
    <button type="button" class="more-action-trigger" aria-label="More actions" x-ref="moreBtn"
        @click.stop="toggleMore()">
        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
            <circle cx="18" cy="18" r="17.5" fill="white" stroke="#E2E2E2"/>
            <ellipse cx="10.8" cy="17.8" rx="1.8" ry="1.8" fill="#3B3731"/>
            <ellipse cx="18" cy="17.8" rx="1.8" ry="1.8" fill="#3B3731"/>
            <ellipse cx="25.2" cy="17.8" rx="1.8" ry="1.8" fill="#3B3731"/>
        </svg>
    </button>

    <template x-teleport="body">
        <div class="more-action-menu" x-cloak x-show="openMore" x-ref="moreMenu" x-transition.opacity.duration.120ms
            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; z-index: 99999;`">
            <button type="button" class="more-action-menu-item">
                <span>Message</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                    fill="none">
                    <path
                        d="M7.5 0.75C11.3248 0.75 14.25 3.44368 14.25 6.56348C14.25 9.58586 11.5045 12.2084 7.85547 12.3691L7.5 12.377H7.49805C6.82132 12.3784 6.14689 12.2902 5.49316 12.1152L5.2168 12.041L4.96094 12.1709C4.55369 12.3769 3.6394 12.7709 2.12793 13.0908C2.34446 12.4211 2.52462 11.6686 2.59375 10.9482L2.62695 10.5967L2.37793 10.3467C1.35243 9.3185 0.750021 7.99417 0.75 6.56348C0.75 3.44368 3.67522 0.75 7.5 0.75Z"
                        stroke="#CBDCE8" stroke-width="1.5" />
                </svg>
            </button>
            <button type="button" class="more-action-menu-item"
                @click.stop="window.dispatchEvent(new CustomEvent(@js($loadingEvent))); $wire.{{ $rescheduleMethod }}(rowId); openMore = false;">
                <span>Reschedule</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                    fill="none">
                    <path d="M2.36584 14.7456V12.0549H5.05648" stroke="#FFC97A" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M14.6246 6.46435C14.91 7.98755 14.6817 9.56243 13.9755 10.9419C13.2692 12.3213 12.125 13.4272 10.7223 14.0861C9.31964 14.745 7.7379 14.9196 6.2253 14.5824C4.7127 14.2452 3.35484 13.4154 2.36479 12.2232M0.86975 9.03565C0.58427 7.51245 0.812567 5.93757 1.51882 4.55813C2.22507 3.1787 3.36931 2.07277 4.77199 1.41388C6.17467 0.754998 7.7564 0.580442 9.269 0.917607C10.7816 1.25477 12.1395 2.08458 13.1295 3.27681"
                        stroke="#FFC97A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M13.1284 0.754517V3.44515H10.4377M4.58993 8.11254C4.20912 8.04636 4.20912 7.49956 4.58993 7.43337C5.26397 7.31547 5.88773 6.9998 6.3819 6.52649C6.87608 6.05318 7.21834 5.44361 7.36519 4.77528L7.38798 4.67005C7.47043 4.29357 8.00639 4.2914 8.0921 4.66679L8.12031 4.78939C8.27185 5.45513 8.61692 6.06118 9.1121 6.53126C9.60728 7.00135 10.2304 7.31446 10.9032 7.4312C11.2861 7.49738 11.2861 8.04745 10.9032 8.11471C10.2306 8.23138 9.60753 8.54433 9.11236 9.01421C8.6172 9.48409 8.27204 10.0899 8.12031 10.7554L8.0921 10.877C8.00639 11.2523 7.47043 11.2502 7.38798 10.8737L7.36628 10.7695C7.21928 10.1009 6.87669 9.49114 6.3821 9.0178C5.88751 8.54446 5.26328 8.22897 4.58885 8.11146"
                        stroke="#FFC97A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </template>
</div>

<style>
    .more-action-trigger,
    .booking-message-btn {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        color: #3B3731;
        font-weight: 700;
    }

    .more-action-trigger {
        font-size: 18px;
        line-height: 1;
    }

    .more-action-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
    }

    .more-action-menu {
        position: absolute;
        top: calc(100% + 0.45rem);
        left: 0;
        min-width: 205px;
        width: max-content;
        max-width: min(260px, calc(100vw - 24px));
        background: #F8F8F8;
        border: 1px solid #D9D9D9;
        border-radius: 8px;
        overflow: hidden;
        z-index: 9999;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .more-action-menu-item {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #D6D6D6;
        background: transparent;
        padding: 0.65rem 0.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: Lato;
        color: #3B3731;
        cursor: pointer;
    }

    .more-action-menu-item:last-child {
        border-bottom: 0;
    }

    .more-action-menu-item span {
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .more-action-menu-item:hover {
        background: #ECECEC;
    }
</style>
