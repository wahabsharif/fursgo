@props([
    'pendingSort' => 'soonest_first',
])

@php
    $sortLabels = [
        'soonest_first' => 'Soonest first',
        'amount_high' => 'Highest Value',
        'amount_low' => 'Lowest Value',
        'client_az' => 'Client (A-Z)',
    ];
    $currentLabel = $sortLabels[$pendingSort] ?? 'Soonest first';
@endphp

<div class="booking-list-sort">
    <div class="sort-dropdown" x-data="{
        open: false,
        menuLeft: 8,
        menuTop: 8,
        menuWidth: 220,
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
            aria-label="Sort bookings" :aria-expanded="open.toString()">
            <img src="{{ asset('images/business-hub/icon-booking-sort.svg') }}" alt="" width="15" height="11">
            <span><strong>Sort:</strong> {{ $currentLabel }}</span>
        </button>
        <template x-teleport="body">
            <div class="sort-menu" x-cloak x-show="open" x-ref="sortMenu" x-transition.opacity.duration.100ms
                :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                @foreach ($sortLabels as $sortKey => $sortLabel)
                    <button type="button" class="sort-options {{ $pendingSort === $sortKey ? 'is-active' : '' }}"
                        wire:click="setPendingSort('{{ $sortKey }}')"
                        @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                        <span>{{ $sortLabel }}</span>
                        <span class="sort-indicator"></span>
                    </button>
                @endforeach
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
        height: 42px;
        min-width: 171px;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 14px;
        box-sizing: border-box;
    }

    .sort-trigger strong {
        font-weight: 600;
    }

    .sort-trigger img {
        width: 15px;
        height: 11px;
        display: block;
        flex-shrink: 0;
    }

    .sort-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        width: 220px;
        min-width: 220px;
        box-sizing: border-box;
        background: #FFF;
        border: 1px solid #E6E6E5;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.12);
        z-index: 99999;
        overflow: hidden;
    }

    .sort-options {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #F0F0F0;
        background: #FFF;
        padding: 14px 16px;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sort-options:last-child {
        border-bottom: none;
    }

    .sort-options:hover {
        background: #FAFAFA;
    }

    .sort-indicator {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1.5px solid #D4D4D4;
        background: #FFF;
        position: relative;
        flex-shrink: 0;
    }

    .sort-options.is-active .sort-indicator {
        border-color: #FFC97A;
        background: #FFC97A;
    }

    .sort-options.is-active .sort-indicator::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 2px;
        width: 5px;
        height: 9px;
        border: solid #FFF;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
</style>
