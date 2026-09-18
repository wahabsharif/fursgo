@props([
    'pendingSort' => 'latest_submitted',
])

<div class="booking-list-sort">
    <div class="sort-dropdown" x-data="{
        open: false,
        menuLeft: 8,
        menuTop: 8,
        menuWidth: 250,
        repositionMenu() {
            const rect = $refs.sortBtn.getBoundingClientRect();
            this.menuTop = rect.bottom + 10;
            this.menuLeft = Math.max(8, Math.min(rect.right - this.menuWidth, window.innerWidth - this.menuWidth - 8));
        },
        toggleMenu() {
            if (!this.open) {
                this.repositionMenu();
            }
            this.open = !this.open;
        }
    }" @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
        @scroll.window="if (open) repositionMenu()"
        @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
        <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
            aria-label="Sort pending bookings" :aria-expanded="open.toString()">
            <span>Sort</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
        <template x-teleport="body">
            <div class="sort-menu" x-cloak x-show="open" x-ref="sortMenu" x-transition.opacity.duration.100ms
                :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                <button type="button" class="sort-options"
                    :class="{ 'is-active': @js($pendingSort) === 'latest_submitted' }"
                    wire:click="setPendingSort('latest_submitted')"
                    @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                    <span>Recommended (default)</span>
                    <span class="sort-indicator"></span>
                </button>
                <button type="button" class="sort-options"
                    :class="{ 'is-active': @js($pendingSort) === 'oldest_submitted' }"
                    wire:click="setPendingSort('oldest_submitted')"
                    @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                    <span>New to Old</span>
                    <span class="sort-indicator"></span>
                </button>
                <button type="button" class="sort-options"
                    :class="{ 'is-active': @js($pendingSort) === 'amount_low' }" wire:click="setPendingSort('amount_low')"
                    @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                    <span>Old to New</span>
                    <span class="sort-indicator"></span>
                </button>
                <button type="button" class="sort-options"
                    :class="{ 'is-active': @js($pendingSort) === 'amount_high' }"
                    wire:click="setPendingSort('amount_high')"
                    @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                    <span>Price Descending</span>
                    <span class="sort-indicator"></span>
                </button>
            </div>
        </template>
    </div>
</div>

<style>
    .booking-list-sort {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 30;
    }

    .sort-dropdown {
        position: relative;
        z-index: 30;
    }

    .sort-trigger {
        width: 59px;
        height: 32px;
        border-radius: 100px;
        border: none;
        background: #FFF;
        color: #A8A8A8;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        box-shadow: 0px 1px 6.7px 0px rgba(59, 55, 49, 0.12);
    }

    .sort-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        width: 250px;
        min-width: 250px;
        box-sizing: border-box;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
        box-shadow: none;
        z-index: 99999;
        overflow: hidden;
    }

    .sort-options {
        width: 100%;
        border: 0;
        border-bottom: 2px solid #e6e6e5;
        background: #FFF;
        padding: 1rem;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        line-height: 1.15;
    }

    .sort-options:last-child {
        border-bottom: none;
    }

    .sort-options:hover {
        background: #F2F2F2;
    }

    .sort-indicator {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: transparent;
        position: relative;
        flex-shrink: 0;
    }

    .sort-options.is-active .sort-indicator::after {
        content: '';
        position: absolute;
        inset: 2px;
        border-radius: 999px;
        background: #FFC97A;
    }
</style>
