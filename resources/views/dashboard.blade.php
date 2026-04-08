<section class="dashboard-content-wrapper">
    <div class="active-section-header" x-text="activeSection.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())">
    </div>

    <div class="section-container">
        <div class="section-panel" :class="{ 'section-active': activeSection === 'business-hub' }">
            <x-dashboard.business-hub />
        </div>
        <div class="section-panel" :class="{ 'section-active': activeSection === 'bookings' }">
            <x-dashboard.bookings />
        </div>
    </div>
</section>

<style>
    [x-cloak] {
        display: none !important;
    }

    .dashboard-content-wrapper {
        display: flex;
        flex-direction: column;
    }

    .active-section-header {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .section-container {
        position: relative;
    }

    .section-panel {
        opacity: 0;
        transform: translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        pointer-events: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }

    .section-panel.section-active {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
        position: relative;
    }
</style>
