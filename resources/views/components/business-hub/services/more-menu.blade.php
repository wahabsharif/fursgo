@props(['id', 'duplicateMethod' => 'duplicateService', 'deleteMethod' => 'deleteService', 'duplicateLabel' => 'Duplicate', 'deleteLabel' => 'Delete', 'ariaLabel' => 'More actions'])

@php
    $rowId = (int) $id;
@endphp

<div class="service-more" x-data="{
    rowId: {{ $rowId }},
    open: false,
    menuLeft: 8,
    menuTop: 8,
    menuWidth: 130,
    reposition() {
        const rect = this.$refs.moreBtn.getBoundingClientRect();
        this.menuTop = Math.max(8, rect.bottom + 5);
        this.menuLeft = Math.max(8, Math.min(rect.right - this.menuWidth, window.innerWidth - this.menuWidth - 8));
    },
    toggle() {
        if (!this.open) {
            window.dispatchEvent(new CustomEvent('service-more-opened', { detail: { id: this.rowId } }));
            this.reposition();
        }
        this.open = !this.open;
    }
}" @service-more-opened.window="if (($event.detail?.id ?? null) !== rowId) open = false"
    @keydown.escape.window="open = false" @resize.window="if (open) reposition()"
    @scroll.window="if (open) reposition()"
    @click.window="if (open && !$refs.moreBtn.contains($event.target) && (!$refs.moreMenu || !$refs.moreMenu.contains($event.target))) open = false">
    <button type="button" class="service-action-btn" x-ref="moreBtn" aria-label="{{ $ariaLabel }}"
        @click.stop="toggle()">
        <x-business-hub.common.icon name="more" />
    </button>
    <template x-teleport="body">
        <div class="service-more-menu" x-cloak x-show="open" x-ref="moreMenu" x-transition.opacity.duration.120ms
            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; z-index: 99999;`">
            <button type="button"
                @click.stop="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } })); $wire.{{ $duplicateMethod }}(rowId); open = false">
                <span>{{ $duplicateLabel }}</span>
            </button>
            <button type="button" class="is-danger"
                @click.stop="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } })); $wire.{{ $deleteMethod }}(rowId); open = false">
                <span>{{ $deleteLabel }}</span>
            </button>
        </div>
    </template>
</div>
