<?php

use App\Support\BusinessHubNav;
use Livewire\Volt\Component;

new class extends Component {
    // Parent Services index component.
}; ?>

@php
    $dashboardServiceMenu = BusinessHubNav::fromSession()['active_service_menu'];
@endphp

@include('components.business-hub.services.shared-styles')

<section class="services-dashboard" aria-label="Services section" x-data="{
    showAddService: false,
    showAddOn: false,
    showAddServiceArea: false,
    activeServiceMenu: @js($dashboardServiceMenu),
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
}" x-init="scrollPageToTop(false)"
    x-on:services-menu-selected.window="activeServiceMenu = $event.detail?.menu || 'services'; if (activeServiceMenu !== 'services') { showAddService = false } if (activeServiceMenu !== 'add-ons') { showAddOn = false } if (activeServiceMenu !== 'service-area') { showAddServiceArea = false }; scrollPageToTop(true)"
    x-on:service-created.window="returnToServiceList()" x-on:add-on-created.window="returnToAddOnList()"
    x-on:service-area-created.window="returnToServiceAreaList()" x-on:service-form-cancel.window="closeAllForms()">
    @php
        $userType = strtolower((string) data_get(auth()->user(), 'user_type', ''));
        $addServiceTitle = $userType === 'space' ? 'Hourly' : 'Full Groom';
        $showServiceArea = $userType !== 'space';
    @endphp

    <header class="service-list-header" x-ref="servicesHeader">
        <h3
            x-text="showAddOn || activeServiceMenu === 'add-ons' ? 'Add-ons' : (activeServiceMenu === 'pet-preferences' ? 'Pet Preferences' : (activeServiceMenu === 'service-area' ? (showAddServiceArea ? 'Add Service Area' : 'Service Area') : (showAddService ? '{{ $addServiceTitle }}' : 'Service List')))">
        </h3>
        <div class="service-list-header-actions">
            <button type="button" class="service-add-btn" x-show="activeServiceMenu === 'services' && !showAddService"
                x-cloak @click="showAddService = true; window.dispatchEvent(new CustomEvent('service-form-opened'))">+
                Add Service</button>
            <button type="button" class="service-add-btn" x-show="activeServiceMenu === 'add-ons' && !showAddOn"
                x-cloak @click="showAddOn = true; window.dispatchEvent(new CustomEvent('service-form-opened'))">+
                Add Add-ons</button>
            <button type="button" class="service-add-btn"
                x-show="activeServiceMenu === 'service-area' && !showAddServiceArea" x-cloak
                @click="showAddServiceArea = true; window.dispatchEvent(new CustomEvent('service-form-opened'))">+
                Add Service Area</button>
        </div>
    </header>

    <template x-if="activeServiceMenu === 'services'">
        <div>
            <div x-show="!showAddService" x-cloak>
                <livewire:business-hub.services.services-list />
            </div>

            <template x-if="showAddService">
                <div x-cloak x-transition:enter="service-view-enter" x-transition:enter-start="service-view-enter-start"
                    x-transition:enter-end="service-view-enter-end" x-transition:leave="service-view-leave"
                    x-transition:leave-start="service-view-leave-start" x-transition:leave-end="service-view-leave-end">
                    @if ($userType === 'space')
                        <livewire:business-hub.services.add-services-form-space />
                    @else
                        <livewire:business-hub.services.add-services-form-groomer />
                    @endif
                </div>
            </template>
        </div>
    </template>

    <template x-if="activeServiceMenu === 'add-ons'">
        <div>
            <div x-show="!showAddOn" x-cloak>
                <livewire:business-hub.services.add-ons-list />
            </div>

            <template x-if="showAddOn">
                <div x-cloak x-transition:enter="service-view-enter" x-transition:enter-start="service-view-enter-start"
                    x-transition:enter-end="service-view-enter-end" x-transition:leave="service-view-leave"
                    x-transition:leave-start="service-view-leave-start" x-transition:leave-end="service-view-leave-end">
                    @if ($userType === 'space')
                        <livewire:business-hub.services.add-addons-form-space />
                    @else
                        <livewire:business-hub.services.add-addons-form-groomer />
                    @endif
                </div>
            </template>
        </div>
    </template>

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
                <div x-show="!showAddServiceArea" x-cloak x-init="window.dispatchEvent(new CustomEvent('service-area-map-refresh'))">
                    <livewire:business-hub.services.service-area-list />
                </div>

                <template x-if="showAddServiceArea">
                    <div x-cloak x-transition:enter="service-view-enter"
                        x-transition:enter-start="service-view-enter-start"
                        x-transition:enter-end="service-view-enter-end" x-transition:leave="service-view-leave"
                        x-transition:leave-start="service-view-leave-start"
                        x-transition:leave-end="service-view-leave-end" x-init="window.dispatchEvent(new CustomEvent('service-area-form-map-refresh'))">
                        <livewire:business-hub.services.add-service-area-form />
                    </div>
                </template>
            </div>
        </template>
    @endif
</section>

<style>
    .service-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #d6d6d6;
        margin-bottom: 2.5rem;
        margin-top: 4rem;
    }

    [x-cloak] {
        display: none !important;
    }

    .service-list-header h3 {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-add-btn {
        border: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        background: transparent;
        cursor: pointer;
        padding: 0;
    }

    .service-list-header-actions {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        margin-left: auto;
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

    /* Keep form action button styling available before dynamic form mount. */
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
</style>
