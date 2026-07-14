@php
    use App\Support\MarketingHubNav;

    $dashboardNav = MarketingHubNav::fromSession();
@endphp

<section class="dashboard-content-wrapper marketing-hub-content">
    <div class="active-section-header" x-cloak>
        <template x-if="activeSection === 'marketing-hub'">
            <div>
                <h2>Marketing Hub</h2>
            </div>
        </template>

        <template x-if="activeSection === 'promo-creation'">
            <div>
                <h2>Promo Creation</h2>
                {{-- <p>Create and manage promotional campaigns.</p> --}}
            </div>
        </template>

        <template x-if="activeSection === 'settings'">
            <div>
                <h2>Settings</h2>
                {{-- <p>Manage your marketing preferences and defaults.</p> --}}
            </div>
        </template>
    </div>

    <div class="marketing-hub-panels">
        <div x-show="activeSection === 'marketing-hub'" x-cloak>
            <p class="marketing-hub-placeholder">Marketing Hub overview coming soon.</p>
        </div>

        <div x-show="activeSection === 'promo-creation'" x-cloak>
            <p class="marketing-hub-placeholder">Promo Creation tools coming soon.</p>
        </div>

        <div x-show="activeSection === 'settings'" x-cloak>
            <p class="marketing-hub-placeholder">Marketing settings coming soon.</p>
        </div>
    </div>
</section>

<style>
    .dashboard-content-wrapper {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .active-section-header {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        position: relative;
    }

    .active-section-header h2,
    .active-section-header>div>h2 {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .active-section-header p,
    .active-section-header>div>p {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
        text-transform: none;
    }

    .marketing-hub-content {
        padding: 0 0 3rem;
        width: 100%;
        min-width: 0;
    }

    .marketing-hub-placeholder {
        margin: 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-weight: 400;
        line-height: normal;
    }

    .marketing-hub-panels {
        margin-top: 1.5rem;
    }
</style>
