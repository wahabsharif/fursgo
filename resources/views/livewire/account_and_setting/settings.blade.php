<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Settings')] class extends Component {
}; ?>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
    <style>
        .modal {
            backdrop-filter: blur(1px);
            align-items: center;

        }

        .modal-content.size {
            width: 450px;
            border-radius: 10px;
            background: #FDFCF8;
            top: 0;
        }

        .modal-buttons .close-btn {
            width: 170px;
            height: 36px;
            border-radius: 96px;
            border: 1px solid #E2E2E2;
            background: #FFF;
        }

        .modal-buttons .update-btn {
            color: #FFF;
            width: 170px;
            height: 36px;
            border-radius: 75px;
            background: #FFC97A;
            border: none;
        }

        .unlink-account-pill {
            padding: 12px 20px;
            border-radius: 50px;
            background: #F3F0E8;
            border: 1px solid #E8E4DC;
        }

        .unlink-account-pill .social-icons {
            width: 32px;
            height: 32px;
        }
    </style>

@endpush

<div>
    @include('account_and_setting.settings')
</div>

@push('script')
{{-- Dropdowns: use layouts.app custom.js only (loading common.js too double-toggles .open). --}}
<script>
    // Modals (from common.js) — event delegation
    document.addEventListener('click', (e) => {
        const openTrigger = e.target.closest('[data-modal-open]');
        if (openTrigger) {
            const modal = document.getElementById(openTrigger.dataset.modalOpen);
            if (modal) modal.style.display = 'flex';
        }

        if (e.target.closest('[data-modal-close]') || e.target.closest('[data-modal-submit-close]')) {
            const modal = e.target.closest('.modal');
            if (modal) modal.style.display = 'none';
        }

        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    // Tabs (from common.js)
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
        if (targetPanel) targetPanel.classList.add('active');
    });
</script>
<script>
    const updatePasswordForm = document.getElementById("updatePasswordForm");
    if (updatePasswordForm) {
        updatePasswordForm.addEventListener("submit", function(e) {
            e.preventDefault();

            const updateModal = document.getElementById("update_password_modal");
            if (updateModal) {
                updateModal.style.display = "none";
            }

            const successTrigger = document.querySelector('[data-modal-open="password_updated_modal"]');
            if (successTrigger) {
                successTrigger.click();
            }
        });
    }

    const removeCard = document.getElementById('remove_card');
    if (removeCard) {
        removeCard.addEventListener('click', function() {
            document.getElementById('payment_modal').style.display = 'none';
            document.getElementById('remove_card_alert_modal').style.display = 'flex';
        });
    }

    const confirmRemoveCard = document.getElementById('confirm_remove_card');
    if (confirmRemoveCard) {
        confirmRemoveCard.addEventListener('click', function() {
            document.getElementById('remove_card_alert_modal').style.display = 'none';
            document.getElementById('card_removed_modal').style.display = 'flex';
        });
    }

    document.addEventListener('click', function(e) {
        const circle = e.target.closest('.toggle-circle');
        if (!circle) return;

        const toggle = circle.closest('.toggle-switch');
        if (toggle) toggle.classList.toggle('on');
    });
</script>
<script>
    const input = document.getElementById('toggle-input');

    if (input) {
        input.addEventListener('change', () => {
            if (input.checked) {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        });
    }

    const navbar = document.querySelector('header nav.navbar');

    function updateNavHeight() {
        if (!navbar) return;
        const height = navbar.offsetHeight;
        document.documentElement.style.setProperty('--nav-height', height + 'px');
    }

    updateNavHeight();
    window.addEventListener('resize', updateNavHeight);
</script>
@endpush

