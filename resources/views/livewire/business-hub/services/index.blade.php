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
    public string $filter = 'all';

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
        $allowed = ['newest', 'duration_asc', 'price_asc', 'name_asc'];
        $this->sort = in_array($sort, $allowed, true) ? $sort : 'newest';
        // Lists are synced from the toolbar Alpine layer to avoid concurrent Livewire updates.
    }

    public function setFilter(string $filter): void
    {
        $allowed = ['all', 'cat', 'dog', 'other'];
        $this->filter = in_array($filter, $allowed, true) ? $filter : 'all';
        // Lists are synced from the toolbar Alpine layer to avoid concurrent Livewire updates.
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
        $search = trim($this->search);
        $sort = $this->sort;
        $filter = $this->filter;

        // Event path (may miss remounted Alpine/x-if children in the hub shell).
        $this->dispatch('services-toolbar-updated', search: $search, sort: $sort, filter: $filter);

        // Direct sync — hub mounts services under Alpine x-if, so nested Livewire
        // parent→child / event delivery is unreliable for the list components.
        $this->js(
            '(() => {
                const search = ' .
                json_encode($search) .
                ';
                const sort = ' .
                json_encode($sort) .
                ';
                const filter = ' .
                json_encode($filter) .
                ';
                const targets = new Set([
                    "business-hub.services.services-list",
                    "business-hub.services.add-ons-list",
                ]);
                (window.Livewire?.all?.() || []).forEach((component) => {
                    if (!targets.has(component.name)) {
                        return;
                    }
                    if (typeof component.$wire?.applyToolbar === "function") {
                        component.$wire.applyToolbar(search, sort, filter);
                    }
                });
            })()',
        );
    }
}; ?>

@php
    $dashboardServiceMenu = BusinessHubNav::fromSession()['active_service_menu'];
    $userType = strtolower((string) data_get(auth()->user(), 'user_type', ''));
    $showServiceArea = $userType !== 'space';
    $addServiceTitle = $userType === 'space' ? 'Hourly' : 'Full Groom';
    $sortLabels = [
        'newest' => 'Newest first',
        'duration_asc' => 'Duration Ascending',
        'price_asc' => 'Price Ascending',
        'name_asc' => 'Service Name (A-Z)',
    ];
    $filterLabels = [
        'cat' => 'Cats Only',
        'dog' => 'Dogs Only',
        'other' => 'Others Only',
        'all' => 'All Pets',
    ];
    $currentSortKey = (string) $this->sort;
    $currentSortLabel = $sortLabels[$currentSortKey] ?? 'Newest first';
    $currentFilterKey = (string) $this->filter;
    $servicesIndexId = $this->getId();
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

        <div class="services-toolbar-row">
            <label class="services-search">
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="Search by service type or add-ons ..." />
                <span class="services-search-icon" aria-hidden="true">
                    <x-business-hub.common.icon name="search" />
                </span>
            </label>

            <div class="services-toolbar-actions">
                <div class="services-filter" x-data="{
                    open: false,
                    menuLeft: 8,
                    menuTop: 8,
                    menuWidth: 207,
                    componentId: @js($servicesIndexId),
                    activeKey: @js($currentFilterKey),
                    servicesWire() {
                        const byId = window.Livewire?.find?.(this.componentId);
                        if (byId) {
                            return byId;
                        }
                        return (window.Livewire?.all?.() || []).find((c) => (c.name || '') === 'business-hub.services.index') || null;
                    },
                    syncLists(filterKey) {
                        const parent = this.servicesWire();
                        const search = parent?.search ?? '';
                        const sort = parent?.sort ?? 'newest';
                        const targets = new Set([
                            'business-hub.services.services-list',
                            'business-hub.services.add-ons-list',
                        ]);
                        (window.Livewire?.all?.() || []).forEach((component) => {
                            if (!targets.has(component.name)) {
                                return;
                            }
                            if (typeof component.$wire?.applyToolbar === 'function') {
                                component.$wire.applyToolbar(search, sort, filterKey);
                            }
                        });
                    },
                    repositionMenu() {
                        const rect = $refs.filterBtn.getBoundingClientRect();
                        this.menuTop = rect.bottom + 7;
                        this.menuLeft = Math.max(8, Math.min(rect.right - this.menuWidth, window.innerWidth - this.menuWidth - 8));
                    },
                    toggleMenu() {
                        if (!this.open) {
                            this.repositionMenu();
                            window.dispatchEvent(new CustomEvent('services-tool-menu-open', { detail: 'filter' }));
                            this.open = true;
                            return;
                        }
                        this.open = false;
                    },
                    applyFilter(key) {
                        this.open = false;
                        this.activeKey = key;
                        // Update visible lists first — parent→child Livewire sync is unreliable under hub Alpine x-if.
                        this.syncLists(key);
                        const wire = this.servicesWire();
                        if (wire && typeof wire.setFilter === 'function') {
                            wire.setFilter(key);
                        } else if (wire && typeof wire.call === 'function') {
                            wire.call('setFilter', key);
                        }
                    }
                }" @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                    @scroll.window="if (open) repositionMenu()"
                    @services-tool-menu-open.window="if ($event.detail !== 'filter') open = false"
                    @click.window="if (open && !$refs.filterBtn.contains($event.target) && (!$refs.filterMenu || !$refs.filterMenu.contains($event.target))) { open = false }">
                    <button type="button" class="services-tool-btn" x-ref="filterBtn" @click.stop="toggleMenu()"
                        aria-label="Filter list" :aria-expanded="open.toString()">
                        <img src="{{ asset('images/business-hub/icon-booking-filter.svg') }}" alt="" width="8" height="10" />
                        <span>Filter</span>
                    </button>
                    <template x-teleport="body">
                        <div class="services-tool-menu services-filter-menu" x-cloak x-show="open" x-ref="filterMenu"
                            x-transition.opacity.duration.100ms
                            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                            @foreach ($filterLabels as $filterKey => $filterLabel)
                                <button type="button" class="services-tool-option"
                                    :class="{ 'is-active': activeKey === '{{ $filterKey }}' }"
                                    x-on:click="applyFilter('{{ $filterKey }}')">
                                    <span>{{ $filterLabel }}</span>
                                    <svg class="services-tool-check" xmlns="http://www.w3.org/2000/svg" width="8" height="6" viewBox="0 0 8 6" fill="none" aria-hidden="true">
                                        <path d="M1 3L3 5L7 1" stroke="#FFBA55" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </template>
                </div>

                <div class="services-sort" x-data="{
                    open: false,
                    menuLeft: 8,
                    menuTop: 8,
                    menuWidth: 207,
                    componentId: @js($servicesIndexId),
                    activeKey: @js($currentSortKey),
                    sortLabels: @js($sortLabels),
                    servicesWire() {
                        const byId = window.Livewire?.find?.(this.componentId);
                        if (byId) {
                            return byId;
                        }
                        return (window.Livewire?.all?.() || []).find((c) => (c.name || '') === 'business-hub.services.index') || null;
                    },
                    syncLists(sortKey) {
                        const parent = this.servicesWire();
                        const search = parent?.search ?? '';
                        const filter = parent?.filter ?? 'all';
                        const targets = new Set([
                            'business-hub.services.services-list',
                            'business-hub.services.add-ons-list',
                        ]);
                        (window.Livewire?.all?.() || []).forEach((component) => {
                            if (!targets.has(component.name)) {
                                return;
                            }
                            if (typeof component.$wire?.applyToolbar === 'function') {
                                component.$wire.applyToolbar(search, sortKey, filter);
                            }
                        });
                    },
                    repositionMenu() {
                        const rect = $refs.sortBtn.getBoundingClientRect();
                        this.menuTop = rect.bottom + 7;
                        this.menuLeft = Math.max(8, Math.min(rect.right - this.menuWidth, window.innerWidth - this.menuWidth - 8));
                    },
                    toggleMenu() {
                        if (!this.open) {
                            this.repositionMenu();
                            window.dispatchEvent(new CustomEvent('services-tool-menu-open', { detail: 'sort' }));
                            this.open = true;
                            return;
                        }
                        this.open = false;
                    },
                    applySort(key) {
                        this.open = false;
                        this.activeKey = key;
                        this.syncLists(key);
                        const wire = this.servicesWire();
                        if (wire && typeof wire.setSort === 'function') {
                            wire.setSort(key);
                        } else if (wire && typeof wire.call === 'function') {
                            wire.call('setSort', key);
                        }
                    }
                }" @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                    @scroll.window="if (open) repositionMenu()"
                    @services-tool-menu-open.window="if ($event.detail !== 'sort') open = false"
                    @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                    <button type="button" class="services-tool-btn services-sort-btn" x-ref="sortBtn" @click.stop="toggleMenu()"
                        aria-label="Sort list" :aria-expanded="open.toString()">
                        <img src="{{ asset('images/business-hub/icon-booking-sort.svg') }}" alt="" width="15" height="11" />
                        <span>
                            <strong>Sort:</strong>
                            <span x-text="sortLabels[activeKey] || 'Newest first'"></span>
                        </span>
                    </button>
                    <template x-teleport="body">
                        <div class="services-tool-menu" x-cloak x-show="open" x-ref="sortMenu"
                            x-transition.opacity.duration.100ms
                            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                            @foreach ($sortLabels as $sortKey => $sortLabel)
                                <button type="button" class="services-tool-option"
                                    :class="{ 'is-active': activeKey === '{{ $sortKey }}' }"
                                    x-on:click="applySort('{{ $sortKey }}')">
                                    <span>{{ $sortLabel }}</span>
                                    <svg class="services-tool-check" xmlns="http://www.w3.org/2000/svg" width="11" height="8" viewBox="0 0 11 8" fill="none" aria-hidden="true">
                                        <path d="M1 4L4 7L10 1" stroke="#FFBA55" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
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
            <livewire:business-hub.services.services-list :search="$search" :sort="$sort" :filter="$filter" wire:key="svc-list" />
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
            <livewire:business-hub.services.add-ons-list :search="$search" :sort="$sort" :filter="$filter" wire:key="addon-list" />
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
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .services-tabs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .services-tab {
        height: 42px;
        padding: 0 1.25rem;
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

    .services-toolbar-row {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: nowrap;
        width: 100%;
        max-width: none;
        box-sizing: border-box;
    }

    .services-search {
        position: relative;
        display: flex;
        align-items: center;
        flex: 1 1 auto;
        min-width: 0;
        height: 42px;
    }

    .services-search input {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        padding: 0 42px 0 20px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        outline: none;
    }

    .services-search input::placeholder {
        color: #9D9B98;
        font-size: 16px;
    }

    .services-search-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: inline-flex;
    }

    .services-toolbar-actions {
        display: flex;
        align-items: center;
        gap: 20px;
        flex: 0 0 auto;
        margin-left: 0;
    }

    .services-tool-btn {
        height: 42px;
        min-width: 76px;
        padding: 0 0.85rem;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .services-tool-btn img {
        width: auto;
        height: auto;
        flex-shrink: 0;
        display: block;
    }

    .services-sort-btn {
        min-width: 171px;
        font-weight: 500;
        justify-content: flex-start;
        padding: 0 0.75rem;
    }

    .services-sort-btn strong {
        font-weight: 600;
    }

    .services-tool-menu {
        box-sizing: border-box;
        min-height: 144px;
        background: #FFF;
        border: 1px solid #E2E2E2;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 0;
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.12);
        z-index: 99999;
    }

    .services-filter-menu {
        width: 207px;
        height: 144px;
        min-height: 144px;
    }

    .services-tool-option {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 0;
        min-height: 16px;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .services-tool-option.is-active {
        color: #FFBA55;
    }

    .services-tool-check {
        width: 8px;
        height: 6px;
        flex-shrink: 0;
        opacity: 0;
    }

    .services-tool-option.is-active .services-tool-check {
        opacity: 1;
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
        gap: 20px;
        margin-top: 0.5rem;
    }

    .service-form-btn {
        height: 42px;
        border-radius: 100px;
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
        width: 88px;
        border-color: #D9D9D9;
        background: #fff;
        color: #9D9B98;
    }

    .service-form-btn-save {
        width: 138px;
        background: #BACF8E;
        color: #fff;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
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
        .services-toolbar-row {
            flex-direction: column;
            align-items: stretch;
        }

        .services-toolbar-row {
            flex-wrap: wrap;
            max-width: none;
        }

        .services-search {
            flex: 1 1 100%;
            width: 100%;
        }

        .services-toolbar-actions {
            margin-left: 0;
            width: 100%;
        }

        .services-tool-btn,
        .services-sort-btn {
            flex: 1;
        }
    }
</style>
