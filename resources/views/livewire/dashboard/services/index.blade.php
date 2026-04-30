<?php

use Livewire\Volt\Component;

new class extends Component {
    // Parent Services index component.
}; ?>

<section class="services-dashboard" aria-label="Services section" x-data="{ showAddService: false, activeServiceMenu: 'services' }"
    x-on:services-menu-selected.window="activeServiceMenu = $event.detail?.menu || 'services'; if (activeServiceMenu !== 'services') { showAddService = false }"
    x-on:service-form-cancel="showAddService = false; window.dispatchEvent(new CustomEvent('service-form-cancel')); window.dispatchEvent(new CustomEvent('nav-list-loading-start'))"
    x-on:service-form-cancel.window="showAddService = false">
    @php
        $userType = strtolower((string) data_get(auth()->user(), 'user_type', ''));
        $addServiceTitle = $userType === 'space' ? 'Hourly' : 'Full Groom';
    @endphp

    <header class="service-list-header">
        <h3
            x-text="activeServiceMenu === 'add-ons' ? 'Add-ons' : (activeServiceMenu === 'pet-preferences' ? 'Pet Preferences' : (activeServiceMenu === 'service-area' ? 'Service Area' : (showAddService ? '{{ $addServiceTitle }}' : 'Service List')))">
        </h3>
        <button type="button" class="service-add-btn" x-show="activeServiceMenu === 'services' && !showAddService"
            x-cloak
            @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); showAddService = true; window.dispatchEvent(new CustomEvent('service-form-opened'))">+
            Add Service</button>
    </header>

    <div x-show="activeServiceMenu === 'services' && !showAddService" x-cloak x-transition:enter="service-view-enter"
        x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
        x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
        x-transition:leave-end="service-view-leave-end">
        <livewire:dashboard.services.services-list />
    </div>

    <div x-show="activeServiceMenu === 'services' && showAddService" x-cloak x-transition:enter="service-view-enter"
        x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
        x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
        x-transition:leave-end="service-view-leave-end">
        @if ($userType === 'space')
            <livewire:dashboard.services.add-services-form-space />
        @else
            <livewire:dashboard.services.add-services-form-groomer />
        @endif
    </div>

    <div x-show="activeServiceMenu === 'add-ons'" x-cloak x-transition:enter="service-view-enter"
        x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
        x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
        x-transition:leave-end="service-view-leave-end">
        <livewire:dashboard.services.add-ons-list />
    </div>

    <div x-show="activeServiceMenu === 'pet-preferences'" x-cloak x-transition:enter="service-view-enter"
        x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
        x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
        x-transition:leave-end="service-view-leave-end">
        <p class="service-placeholder-copy">Pet Preferences panel coming next.</p>
    </div>

    <div x-show="activeServiceMenu === 'service-area'" x-cloak x-transition:enter="service-view-enter"
        x-transition:enter-start="service-view-enter-start" x-transition:enter-end="service-view-enter-end"
        x-transition:leave="service-view-leave" x-transition:leave-start="service-view-leave-start"
        x-transition:leave-end="service-view-leave-end">
        <p class="service-placeholder-copy">Service Area panel coming next.</p>
    </div>
</section>

<style>
    .service-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
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
</style>
