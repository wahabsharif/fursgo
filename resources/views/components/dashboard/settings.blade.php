@php
    use App\Support\DashboardNav;

    $dashboardNav = DashboardNav::fromSession();
@endphp

<div {{ $attributes->merge(['class' => 'dashboard-section-host']) }} x-data="{
    activeSettingsMenu: @js($dashboardNav['active_settings_menu']),
    settingsLoadingTimeout: null,
    selectSettingsMenu(menu = 'general') {
        if (this.settingsLoadingTimeout) {
            clearTimeout(this.settingsLoadingTimeout);
        }

        window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }));
        this.activeSettingsMenu = menu;
        this.settingsLoadingTimeout = setTimeout(() => {
            window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
            this.settingsLoadingTimeout = null;
        }, 450);
    },
}"
    x-on:settings-menu-selected.window="selectSettingsMenu($event.detail?.menu || 'general')">
    <div x-show="activeSettingsMenu === 'general'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:dashboard.settings />
    </div>

    <div x-show="activeSettingsMenu === 'business-details'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:dashboard.settings.business-details />
    </div>

    <div x-show="activeSettingsMenu === 'service-policies'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:dashboard.settings />
    </div>

    <style>
        .settings-panel-enter {
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .settings-panel-enter-start {
            opacity: 0;
            transform: translateY(14px);
        }

        .settings-panel-enter-end {
            opacity: 1;
            transform: translateY(0);
        }

        .settings-panel-leave {
            transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .settings-panel-leave-start {
            opacity: 1;
            transform: translateY(0);
        }

        .settings-panel-leave-end {
            opacity: 0;
            transform: translateY(8px);
        }
    </style>
</div>
