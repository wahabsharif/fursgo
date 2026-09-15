<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] #[Title('Fursgo Admin')]
    class extends Component {
    // One admin page. Tabs switch with Alpine — no route reloads.
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

<div class="admin-portal" x-data="adminPortal(@js($adminTabs))">
    <x-admin.header />

    <main class="admin-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @foreach ($adminTabPanels as $tabId => $view)
                        <div x-show="tab === '{{ $tabId }}'" x-cloak>
                            @include($view)
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>
