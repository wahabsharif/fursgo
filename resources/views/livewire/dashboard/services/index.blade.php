<?php

use Livewire\Volt\Component;

new class extends Component {
    // Parent Services index component.
}; ?>

<section class="services-dashboard" aria-label="Services section">
    <header class="service-list-header">
        <h3>Service List</h3>
        <button type="button" class="service-add-btn">+ Add Service</button>
    </header>
    <livewire:dashboard.services.services-list />
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

    .service-list-header h3 {
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
</style>
