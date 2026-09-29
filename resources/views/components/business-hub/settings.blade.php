@php
    use App\Support\BusinessHubNav;

    $dashboardNav = BusinessHubNav::fromSession();
@endphp

<div {{ $attributes->merge(['class' => 'dashboard-section-host settings-shell']) }} x-data="{
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
    <header class="settings-shell-header">
        <h2 x-text="activeSettingsMenu === 'general' ? 'Settings' : 'Business Details'"></h2>
        <p x-text="activeSettingsMenu === 'general'
            ? 'Manage your account, business details and policies.'
            : 'Keep your business information and compliance up to date.'"></p>

        <nav class="settings-shell-tabs" aria-label="Settings sections">
            @foreach ([
        'general' => 'General',
        'business-details' => 'Business Details',
        'service-policies' => 'Service Policies',
    ] as $menu => $label)
                <button type="button" @click="selectSettingsMenu('{{ $menu }}'); window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'settings', active_settings_menu: '{{ $menu }}' } }))"
                    :class="{ 'is-active': activeSettingsMenu === '{{ $menu }}' }"
                    :aria-current="activeSettingsMenu === '{{ $menu }}' ? 'page' : null">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </header>

    <div x-show="activeSettingsMenu === 'general'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:business-hub.settings />
    </div>

    <div x-show="activeSettingsMenu === 'business-details'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:business-hub.settings.business-details />
    </div>

    <div x-show="activeSettingsMenu === 'service-policies'" x-cloak x-transition:enter="settings-panel-enter"
        x-transition:enter-start="settings-panel-enter-start" x-transition:enter-end="settings-panel-enter-end"
        x-transition:leave="settings-panel-leave" x-transition:leave-start="settings-panel-leave-start"
        x-transition:leave-end="settings-panel-leave-end">
        <livewire:business-hub.settings.service-policies />
    </div>

    <style>
        .settings-shell {
            width: min(1030px, calc(100% - 45px));
            margin-left: 45px;
            overflow: visible;
        }

        .settings-shell-header {
            position: static;
            top: auto;
            z-index: auto;
            margin-bottom: 40px;
            background: transparent;
            color: #3B3731;
        }

        .settings-shell-header h2 {
            margin: 0;
            font-family: "Playfair Display";
            font-size: 28px;
            font-weight: 600;
            line-height: normal;
        }

        .settings-shell-header p {
            margin: 0;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .settings-shell-tabs {
            display: flex;
            align-items: center;
            justify-content: flex-start !important;
            gap: 10px;
            margin-top: 40px;
            padding: 0 !important;
        }

        .settings-shell-tabs button {
            height: 42px;
            padding: 0 20px;
            border: 0;
            border-radius: 100px;
            background: #F6F5F5;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            flex: 0 0 auto;
            transition: color .18s ease, background-color .18s ease, transform .18s ease;
        }

        .settings-shell-tabs button:hover {
            color: #3B3731;
        }

        .settings-shell-tabs button:active {
            transform: translateY(1px);
        }

        .settings-shell-tabs button.is-active {
            background: #3B3731;
            color: #FFF;
        }

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

        @media (max-width: 640px) {
            .settings-shell {
                width: 100%;
                margin-left: 0;
            }

            .settings-shell-header {
                margin-bottom: 28px;
            }

            .settings-shell-tabs {
                gap: 8px;
                margin-top: 28px;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .settings-shell-tabs button {
                flex: 0 0 auto;
            }
        }
    </style>
</div>
