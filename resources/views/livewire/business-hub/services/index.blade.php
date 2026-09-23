<?php

use App\Models\AddOn;
use App\Models\Service;
use App\Support\BusinessHubNav;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $editingServiceId = null;
    public ?int $editingAddOnId = null;

    public string $search = '';
    public string $sort = 'newest';

    private function profileId(): ?int
    {
        $id = auth('groomer_spacer')->id() ?? auth()->id();

        return $id ? (int) $id : null;
    }

    public function getServiceCountProperty(): int
    {
        $profileId = $this->profileId();

        return $profileId ? (int) Service::query()->where('groomer_spacer_id', $profileId)->count() : 0;
    }

    public function getAddOnCountProperty(): int
    {
        $profileId = $this->profileId();

        return $profileId ? (int) AddOn::query()->where('groomer_spacer_id', $profileId)->count() : 0;
    }

    public function setSort(string $sort): void
    {
        $allowed = ['newest', 'oldest', 'name_asc', 'name_desc'];
        $this->sort = in_array($sort, $allowed, true) ? $sort : 'newest';
        $this->dispatchToolbar();
    }

    public function updatedSearch(): void
    {
        $this->dispatchToolbar();
    }

    #[On('service-created')]
    #[On('service-deleted')]
    #[On('add-on-created')]
    #[On('add-on-deleted')]
    public function refreshCounts(): void
    {
        $this->dispatchToolbar();
    }

    #[On('reset-service-form')]
    public function clearEditingService(): void
    {
        $this->editingServiceId = null;
    }

    #[On('reset-addon-form')]
    public function clearEditingAddOn(): void
    {
        $this->editingAddOnId = null;
    }

    private function dispatchToolbar(): void
    {
        $this->dispatch('services-toolbar-updated', search: trim($this->search), sort: $this->sort);
    }
}; ?>

@php
    $dashboardServiceMenu = BusinessHubNav::fromSession()['active_service_menu'];
    $userType = strtolower((string) data_get(auth()->user(), 'user_type', ''));
    $showServiceArea = $userType !== 'space';
    $addServiceTitle = $userType === 'space' ? 'Hourly' : 'Full Groom';
@endphp

@include('components.business-hub.services.shared-styles')

<section class="services-dashboard" aria-label="Services section" x-data="{
    showAddService: false,
    showAddOn: false,
    showAddServiceArea: false,
    activeServiceMenu: @js($dashboardServiceMenu),
    formOpen() {
        return this.showAddService || this.showAddOn || this.showAddServiceArea;
    },
    scrollPageToTop(smooth = false) {
        const root = document.scrollingElement || document.documentElement;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const behavior = smooth && !reduceMotion ? 'smooth' : 'auto';

        if (smooth && root.scrollTop < 40) {
            return;
        }

        const go = () => window.scrollTo({ top: 0, left: 0, behavior });

        if (smooth) {
            this.$nextTick(() => setTimeout(go, 50));
            return;
        }

        root.scrollTop = 0;
        document.body.scrollTop = 0;
        go();
        this.$nextTick(() => {
            root.scrollTop = 0;
            document.body.scrollTop = 0;
            setTimeout(() => {
                root.scrollTop = 0;
                document.body.scrollTop = 0;
            }, 320);
        });
    },
    openForm(menu, isEdit = false, title = null) {
        const target = menu || this.activeServiceMenu;
        if (target === 'add-ons') {
            this.showAddOn = true;
            this.showAddService = false;
            this.showAddServiceArea = false;
            this.activeServiceMenu = 'add-ons';
        } else if (target === 'service-area') {
            this.showAddServiceArea = true;
            this.showAddService = false;
            this.showAddOn = false;
            this.activeServiceMenu = 'service-area';
        } else {
            this.showAddService = true;
            this.showAddOn = false;
            this.showAddServiceArea = false;
            this.activeServiceMenu = 'services';
        }
        const defaultTitle = target === 'add-ons' ? 'Add-ons' : (target === 'service-area' ? 'Add Service Area' : @js($addServiceTitle));
        window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
        window.dispatchEvent(new CustomEvent('service-form-opened', { detail: { editing: !!isEdit, title: title || defaultTitle, menu: target } }));
        this.scrollPageToTop(true);
    },
    selectMenu(menu) {
        this.activeServiceMenu = menu;
        if (menu !== 'services') {
            this.showAddService = false;
        }
        if (menu !== 'add-ons') {
            this.showAddOn = false;
        }
        if (menu !== 'service-area') {
            this.showAddServiceArea = false;
        }
        window.dispatchEvent(new CustomEvent('nav-list-loading-start'));
        window.dispatchEvent(new CustomEvent('services-menu-selected', { detail: { menu } }));
        window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'services', active_service_menu: menu } }));
        this.scrollPageToTop(true);
    },
    returnToServiceList() {
        this.showAddService = false;
        this.activeServiceMenu = 'services';
        window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
        window.dispatchEvent(new CustomEvent('service-form-closed'));
        this.scrollPageToTop(false);
    },
    returnToAddOnList() {
        this.showAddOn = false;
        this.activeServiceMenu = 'add-ons';
        window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
        window.dispatchEvent(new CustomEvent('service-form-closed'));
        this.scrollPageToTop(false);
    },
    returnToServiceAreaList() {
        this.showAddServiceArea = false;
        this.activeServiceMenu = 'service-area';
        window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
        window.dispatchEvent(new CustomEvent('service-form-closed'));
        this.scrollPageToTop(false);
    },
    closeAllForms() {
        const wasFormOpen = this.showAddService || this.showAddOn || this.showAddServiceArea;
        this.showAddService = false;
        this.showAddOn = false;
        this.showAddServiceArea = false;
        window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
        window.dispatchEvent(new CustomEvent('service-form-closed'));
        if (wasFormOpen) {
            this.scrollPageToTop(false);
        }
    },
    dispatchLivewire(eventName, detail = {}) {
        if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
            window.Livewire.dispatch(eventName, detail);
        }
    },
}" x-init="scrollPageToTop(false)"
    x-on:services-menu-selected.window="activeServiceMenu = $event.detail?.menu || 'services'; showAddService = false; showAddOn = false; showAddServiceArea = false; scrollPageToTop(true)"
    x-on:service-add-requested.window="
        const menu = $event.detail?.menu || activeServiceMenu;
        if (menu === 'services') { dispatchLivewire('reset-service-form'); }
        if (menu === 'add-ons') { dispatchLivewire('reset-addon-form'); }
        if (menu === 'service-area') { dispatchLivewire('reset-service-area-form'); }
        openForm(menu);
    "
    x-on:service-edit-requested.window="openForm('services', true, $event.detail?.title || null)"
    x-on:service-area-edit-requested.window="openForm('service-area', true, $event.detail?.title || null)"
    x-on:add-on-edit-requested.window="openForm('add-ons', true, $event.detail?.title || null)"
    x-on:service-form-launch.window="openForm($event.detail?.menu, !!$event.detail?.editing, $event.detail?.title || null)"
    x-on:service-created.window="returnToServiceList()" x-on:add-on-created.window="returnToAddOnList()"
    x-on:service-deleted.window="returnToServiceList()" x-on:add-on-deleted.window="returnToAddOnList()"
    x-on:service-area-created.window="returnToServiceAreaList()" x-on:service-form-cancel.window="closeAllForms()">

    <div class="services-toolbar" x-show="!formOpen()" x-cloak>
        <label class="services-search" x-show="activeServiceMenu !== 'service-area'">
            <input type="search" wire:model.live.debounce.300ms="search"
                :placeholder="activeServiceMenu === 'add-ons' || activeServiceMenu === 'pet-preferences' ? 'Search add-ons list ...' : 'Search service list ...'" />
            <span class="services-search-icon" aria-hidden="true">
                <x-business-hub.common.icon name="search" />
            </span>
        </label>

        <div class="services-toolbar-row">
            <div class="services-tabs" role="tablist" aria-label="Services views">
                <button type="button" class="services-tab is-services"
                    :class="{ 'is-active': activeServiceMenu === 'services' }"
                    @click="selectMenu('services')">
                    All Services ({{ $this->serviceCount }})
                </button>
                <button type="button" class="services-tab is-addons"
                    :class="{ 'is-active': activeServiceMenu === 'add-ons' }"
                    @click="selectMenu('add-ons')">
                    Add-ons ({{ $this->addOnCount }})
                </button>
                <button type="button" class="services-tab is-prefs"
                    :class="{ 'is-active': activeServiceMenu === 'pet-preferences' }"
                    @click="selectMenu('pet-preferences')">
                    Pet Preferences
                </button>
                @if ($showServiceArea)
                    <button type="button" class="services-tab is-area"
                        :class="{ 'is-active': activeServiceMenu === 'service-area' }"
                        @click="selectMenu('service-area')">
                        Service Area
                    </button>
                @endif
            </div>

            <div class="services-sort" x-show="activeServiceMenu !== 'service-area'" x-cloak>
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
                        aria-label="Sort list" :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                            fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <template x-teleport="body">
                        <div class="sort-menu" x-cloak x-show="open" x-ref="sortMenu"
                            x-transition.opacity.duration.100ms
                            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                            @foreach ([
        'newest' => 'Newest first',
        'oldest' => 'Oldest first',
        'name_asc' => 'Name (A–Z)',
        'name_desc' => 'Name (Z–A)',
    ] as $sortKey => $sortLabel)
                                <button type="button" class="sort-options"
                                    :class="{ 'is-active': @js($sort) === '{{ $sortKey }}' }"
                                    wire:click="setSort('{{ $sortKey }}')" @click="open = false">
                                    <span>{{ $sortLabel }}</span>
                                    <span class="sort-indicator"></span>
                                </button>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div x-show="activeServiceMenu === 'services'" x-cloak>
        <div x-show="!showAddService">
            <livewire:business-hub.services.services-list :search="$search" :sort="$sort" wire:key="svc-list" />
        </div>

        <div x-show="showAddService" x-cloak x-transition:enter="service-view-enter"
            x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
            x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
            x-transition:leave-end="service-view-leave-end">
            @if ($userType === 'space')
                <livewire:business-hub.services.add-services-form-space :editing-id="$editingServiceId"
                    wire:key="space-svc-form" />
            @else
                <livewire:business-hub.services.add-services-form-groomer :editing-id="$editingServiceId"
                    wire:key="groomer-svc-form" />
            @endif
        </div>
    </div>

    <div x-show="activeServiceMenu === 'add-ons'" x-cloak>
        <div x-show="!showAddOn">
            <livewire:business-hub.services.add-ons-list :search="$search" :sort="$sort" wire:key="addon-list" />
        </div>

        <div x-show="showAddOn" x-cloak x-transition:enter="service-view-enter"
            x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
            x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
            x-transition:leave-end="service-view-leave-end">
            @if ($userType === 'space')
                <livewire:business-hub.services.add-addons-form-space :editing-id="$editingAddOnId"
                    wire:key="space-addon-form" />
            @else
                <livewire:business-hub.services.add-addons-form-groomer :editing-id="$editingAddOnId"
                    wire:key="groomer-addon-form" />
            @endif
        </div>
    </div>

    <template x-if="activeServiceMenu === 'pet-preferences'">
        <div x-cloak x-transition:enter="service-view-enter" x-transition:enter-start="service-view-enter-start"
            x-transition:enter-end="service-view-enter-end" x-transition:leave="service-view-leave"
            x-transition:leave-start="service-view-leave-start" x-transition:leave-end="service-view-leave-end">
            <livewire:business-hub.services.pet-preferences />
        </div>
    </template>

    @if ($showServiceArea)
        <template x-if="activeServiceMenu === 'service-area'">
            <div>
                <div x-show="!showAddServiceArea" x-cloak x-init="const refreshServiceAreaMap = () => window.dispatchEvent(new CustomEvent('service-area-map-refresh'));
                refreshServiceAreaMap();
                $watch('showAddServiceArea', (value) => { if (!value) refreshServiceAreaMap(); })">
                    <livewire:business-hub.services.service-area-list />
                </div>

                <div x-show="showAddServiceArea" x-cloak x-transition:enter="service-view-enter"
                    x-transition:enter-start="service-view-enter-start"
                    x-transition:enter-end="service-view-enter-end" x-transition:leave="service-view-leave"
                    x-transition:leave-start="service-view-leave-start"
                    x-transition:leave-end="service-view-leave-end"
                    x-effect="if (showAddServiceArea) { $nextTick(() => window.dispatchEvent(new CustomEvent('service-area-form-map-refresh'))) }">
                    <livewire:business-hub.services.add-service-area-form />
                </div>
            </div>
        </template>
    @endif
</section>

<style>
    .services-dashboard {
        padding-top: 0.25rem;
    }

    [x-cloak] {
        display: none !important;
    }

    .services-toolbar {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
        margin-bottom: 2rem;
    }

    .services-search {
        position: relative;
        display: flex;
        align-items: center;
        width: 400px;
        max-width: 100%;
        height: 42px;
    }

    .services-search input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #FFC97A;
        background: #FFF;
        padding: 0 42px 0 16px;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        outline: none;
    }

    .services-search input::placeholder {
        color: #C4C0BA;
        font-size: 14px;
    }

    .services-search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: inline-flex;
    }

    .services-toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .services-tabs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .services-tab {
        height: 42px;
        padding: 0 1.5rem;
        border: 0;
        border-radius: 100px;
        background: #F6F5F5;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        white-space: nowrap;
    }

    .services-tab.is-services.is-active {
        background: #3B3731;
        color: #fff;
    }

    .services-tab.is-addons.is-active {
        background: color-mix(in srgb, #CBDCE8 30%, transparent);
        color: #99BBD3;
    }

    .services-tab.is-prefs.is-active {
        background: color-mix(in srgb, #FFC97A 30%, transparent);
        color: #FDB752;
    }

    .services-tab.is-area.is-active {
        background: color-mix(in srgb, #FFA899 30%, transparent);
        color: #FF8B77;
    }

    .services-sort {
        margin-left: auto;
    }

    .services-sort .sort-trigger {
        width: 59px;
        height: 32px;
        border-radius: 100px;
        border: none;
        background: #FFF;
        color: #A8A8A8;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
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
        width: 220px;
        min-width: 220px;
        box-sizing: border-box;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
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
        font-weight: 400;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
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

    .service-view-enter {
        transition: opacity 280ms ease, transform 280ms ease;
    }

    .service-view-enter-start {
        opacity: 0;
        transform: translateY(10px);
    }

    .service-view-enter-end {
        opacity: 1;
        transform: translateY(0);
    }

    .service-view-leave {
        transition: opacity 180ms ease, transform 180ms ease;
    }

    .service-view-leave-start {
        opacity: 1;
        transform: translateY(0);
    }

    .service-view-leave-end {
        opacity: 0;
        transform: translateY(-6px);
    }

    .service-placeholder-copy {
        margin-top: 2rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
    }

    .service-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .service-form-btn {
        width: 138px;
        height: 42px;
        border-radius: 75px;
        border: 1px solid transparent;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: opacity 0.15s ease;
    }

    .service-form-btn:hover {
        opacity: 0.92;
    }

    .service-form-btn-cancel {
        border-color: #D9D9D9;
        background: transparent;
        color: #9D9B98;
    }

    .service-form-btn-save {
        background: #c9dda0;
        color: #fff;
    }

    .save-btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .hidden {
        display: none !important;
    }

    .save-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 999px;
        animation: save-spin 0.8s linear infinite;
    }

    @keyframes save-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .services-search {
            width: 100%;
        }
    }
</style>
