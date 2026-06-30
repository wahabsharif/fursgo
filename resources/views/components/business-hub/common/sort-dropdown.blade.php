@props([
    'pendingSort' => 'latest_submitted',
])

<div class="booking-list-sort">
    <div class="sort-dropdown" x-data="{ open: false }" @keydown.escape.window="open = false">
        <button type="button" class="sort-trigger" @click="open = !open" aria-label="Sort pending bookings"
            :aria-expanded="open.toString()">
            <span>Sort</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
        <div class="sort-menu" x-cloak x-show="open" @click.outside="open = false" x-transition.opacity.duration.100ms>
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
                :class="{ 'is-active': @js($pendingSort) === 'amount_low' }"
                wire:click="setPendingSort('amount_low')"
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
    </div>
</div>

<style>
    .booking-list-sort {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sort-dropdown {
        position: relative;
    }

    .sort-trigger {
        width: 69px;
        height: 32px;
        border-radius: 100px;
        border: 1px solid #A8A8A8;
        background: transparent;
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
        gap: 0.6rem;
    }

    .sort-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        min-width: 250px;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
        box-shadow: none;
        z-index: 20;
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
