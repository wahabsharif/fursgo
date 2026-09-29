<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] #[Title('Fursgo Admin')]
    class extends Component {
    public string $activeTab = 'overview';

    /** @var list<string> */
    public array $loadedTabs = ['overview'];

    /** @var list<string> */
    public array $openedCustomerIds = [];

    /** @var list<string> */
    public array $openedProviderIds = [];

    public function setTab(string $id): void
    {
        $allowed = ['overview', 'pet-owners', 'business-providers', 'website-content', 'settings-team'];
        if (! in_array($id, $allowed, true)) {
            return;
        }

        if (! in_array($id, $this->loadedTabs, true)) {
            $this->loadedTabs[] = $id;
        }

        $this->activeTab = $id;

        $this->js('stopAdminLoading()');

        if ($id === 'overview') {
            $this->dispatch('admin-overview-shown');
        }
    }

    public function openCustomer(string $id): void
    {
        if ($id !== '' && ! in_array($id, $this->openedCustomerIds, true)) {
            $this->openedCustomerIds[] = $id;
        }

        $this->js('stopAdminLoading()');
    }

    public function openProvider(string $id): void
    {
        if ($id !== '' && ! in_array($id, $this->openedProviderIds, true)) {
            $this->openedProviderIds[] = $id;
        }

        $this->js('stopAdminLoading()');
    }
}; ?>

@php
    $adminTabs = [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'pet-owners', 'label' => 'Pet Owners'],
        ['id' => 'business-providers', 'label' => 'Business Providers'],
        ['id' => 'website-content', 'label' => 'Website Content'],
        ['id' => 'settings-team', 'label' => 'Settings & Team'],
    ];

    $adminTabPanels = [
        'overview' => 'admin.tabs.overview',
        'pet-owners' => 'admin.tabs.pet-owners',
        'business-providers' => 'admin.tabs.business-providers',
        'website-content' => 'admin.tabs.website-content',
        'settings-team' => 'admin.tabs.settings-team',
    ];
@endphp

<div
    class="admin-portal"
    x-data="adminPortal(@js($adminTabs), @js($activeTab), @js($loadedTabs))"
    @admin-overview-shown.window="refreshAdminSignupGrowthChart()"
>
    <x-admin.header />

    <main class="admin-main">
        <template x-teleport="body">
            <div
                x-data="{ loading: false, timer: null }"
                x-on:admin-tab-loading-start.window="
                    loading = true;
                    clearTimeout(timer);
                    timer = $event.detail?.wait ? null : setTimeout(() => loading = false, 350);
                "
                x-on:admin-tab-loading-end.window="
                    loading = false;
                    clearTimeout(timer);
                    timer = null;
                "
            >
                <div class="admin-tab-loading-bar" x-cloak x-show="loading" aria-hidden="true">
                    <span class="admin-tab-loading-bar__sweep"></span>
                </div>
            </div>
        </template>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @foreach ($adminTabPanels as $tabId => $view)
                        @if (in_array($tabId, $loadedTabs, true))
                            <div
                                wire:key="admin-tab-{{ $tabId }}"
                                class="admin-tab-panel"
                                @if ($activeTab !== $tabId) style="display: none;" @endif
                            >
                                @include($view)
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
