<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Company Information')] class extends Component {
}; ?>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
@endpush

<div>
    @include('profile_pets_preferences.company_information')
</div>

@push('script')
{{-- Tabs only (do not load full common.js — it double-binds .custom-select with layout custom.js) --}}
<script>
    document.addEventListener('click', function (e) {
        const tab = e.target.closest('.tab-btn');
        if (!tab) return;

        const wrapper = tab.closest('[data-tabs]');
        if (!wrapper) return;

        const tabs = wrapper.querySelectorAll('.tab-btn');
        const targetId = tab.dataset.tab;

        let panels = wrapper.querySelectorAll('.tab-panel');
        if (!panels.length) {
            panels = document.querySelectorAll('.tab-panel');
        }

        tabs.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));

        tab.classList.add('active');
        const targetPanel = document.getElementById(targetId);
        if (!targetPanel) return;

        targetPanel.classList.add('active');

        // Scroll so the new tab content starts from the top (below header)
        const header = document.querySelector('header');
        const headerHeight = header ? header.offsetHeight : 0;
        const top = targetPanel.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    });

    const navbar = document.querySelector('header nav.navbar');

    function updateNavHeight() {
        if (!navbar) return;
        document.documentElement.style.setProperty('--nav-height', navbar.offsetHeight + 'px');
    }

    updateNavHeight();
    window.addEventListener('resize', updateNavHeight);

    // Open a tab from the URL hash, e.g. #tab-terms-conditions
    const tabId = window.location.hash.replace('#', '');

    if (tabId) {
        const tabButton = document.querySelector('.tab-btn[data-tab="' + tabId + '"]');
        const tabPanel = document.getElementById(tabId);

        if (tabButton && tabPanel) {
            document.querySelectorAll('.tab-btn').forEach(function (btn) {
                btn.classList.remove('active');
            });
            document.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.classList.remove('active');
            });

            tabButton.classList.add('active');
            tabPanel.classList.add('active');
        }
    }
</script>
@endpush
