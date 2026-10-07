<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('layouts.app'), Title('Fursgo - Settings')] class extends Component {
}; ?>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
    {{-- Current legacy settings styles, scoped to this page so shared imports stay intact. --}}
    <link rel="stylesheet" href="{{ asset('css/account-settings.css') }}">
    <style>
.modal {
            backdrop-filter: blur(0px);
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
            background: #FFF;
            border: 1px solid #E2E2E2;
        }

        .unlink-account-pill .social-icons {
            width: 34px;
            height: 34px;
            border-radius: 50px;
            border: 1px solid #385C8E;
            background: #FFF;
            padding: 6px;
        }

        .link-permissions-list {
            border-radius: 12px;
            border: 1px solid #E2E2E2;
            background: #FFF;
            overflow: hidden;
        }

        .link-permissions-list .link-permission-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
        }

        .link-permissions-list .link-permission-item+.link-permission-item {
            border-top: 1px solid #EFEFEF;
        }

        .link-privacy-note {
            display: flex;
            align-items: flex-start;
            margin-top: 16px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.4;
        }

        .link-privacy-note svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .linked-account-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 50px;
            border: 1px solid #A2C35D;
            background: #FFF;
        }

        .linked-account-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2F3A8F;
            color: #FFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: Lato;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .linked-account-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #A2C35D;
            font-family: Lato;
            font-size: 12px;
            font-weight: 400;
            white-space: nowrap;
        }

        .modal-buttons .update-btn.link-continue-btn {
            background: #3B3731;
        }

        .modal-buttons .update-btn.linked-done-btn {
            background: #C9DDA0;
            width: 79px;
            height: 36px;
        }

        .modal-buttons .update-btn.link-incomplete-done-btn {
            background: #3B3731;
            width: 100%;
            max-width: 185px;
        }

        .link-incomplete-content {
            text-align: center;
            padding: 10px 10px 0;
        }

        .link-incomplete-icon {
            margin: 0 auto 16px;
            display: flex;
            justify-content: center;
        }

        .link-incomplete-content .fs-18-pf-display-700 {
            margin-bottom: 12px;
        }

        .link-incomplete-content .link-incomplete-message {
            max-width: 340px;
            margin: 0 auto;
            line-height: 1.45;
        }

        .download-account-data.small-link-tag,
        .delete-account-data.small-link-tag {
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-underline-offset: 4px;
            text-decoration: underline;
        }

        .updated-password.link-tag {
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        #confirm_deactive_account_modal .form-field label,
        #confirm_delete_data_modal .form-field label {
            font-size: 12px;
        }

        .card-details .small-link-tag,
        .card-details .small-link-tag:hover,
        .card-details .small-link-tag:focus,
        .unblock-trigger,
        .unblock-trigger:hover,
        .unblock-trigger:focus,
        .account-linking-action a,
        .account-linking-action a:hover,
        .account-linking-action a:focus,
        .account-linking-action a:active {
            text-decoration: none !important;
            text-decoration-line: none !important;
        }
    </style>
@endpush

<div>
    @include('account_and_setting.settings')
</div>

@push('script')
    <script>
        // The shared Laravel shell intentionally does not load legacy common.js.
        // Keep only the settings-page modal and tab behavior it needs.
        document.addEventListener('click', function (event) {
            const openTrigger = event.target.closest('[data-modal-open]');
            if (openTrigger) {
                const modal = document.getElementById(openTrigger.dataset.modalOpen);
                if (modal) {
                    modal.style.display = 'flex';
                }
            }

            if (event.target.closest('[data-modal-close]') || event.target.closest('[data-modal-submit-close]')) {
                const modal = event.target.closest('.modal');
                if (modal) {
                    modal.style.display = 'none';
                }
            }

            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }

            const toggle = event.target.closest('.toggle-switch');
            if (toggle) {
                toggle.classList.toggle('on');
                toggle.setAttribute('aria-pressed', toggle.classList.contains('on') ? 'true' : 'false');
            }

            const tab = event.target.closest('.tab-btn');
            if (!tab) {
                return;
            }

            const wrapper = tab.closest('[data-tabs]');
            if (!wrapper) {
                return;
            }

            wrapper.querySelectorAll('.tab-btn').forEach(function (button) {
                button.classList.remove('active');
            });
            wrapper.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.classList.remove('active');
            });

            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.tab);
            if (target) {
                target.classList.add('active');

                // Start every newly selected settings section at its top,
                // while keeping it visible below the sticky site header.
                requestAnimationFrame(function () {
                    const header = document.querySelector('header');
                    const headerHeight = header ? header.offsetHeight : 0;
                    const top = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;

                    window.scrollTo({
                        top: Math.max(0, top),
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>

<script>
        // const toggle = document.getElementById('toggle');
        // toggle.addEventListener('click', () => {
        //     toggle.classList.toggle('on');
        // });

        document.getElementById("updatePasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();

            // close current modal
            const updateModal = document.getElementById("update_password_modal");
            if (updateModal) {
                updateModal.style.display = "none";
            }

            // open success modal
            const successTrigger = document.querySelector('[data-modal-open="password_updated_modal"]');
            if (successTrigger) {
                successTrigger.click();
            }
        });

        document.getElementById('remove_card').addEventListener('click', function() {
            const card = this.dataset.card || 'visa';
            const last4 = this.dataset.last4 || '7890';
            const exp = this.dataset.exp || '06/27';
            const cardLabel = card.charAt(0).toUpperCase() + card.slice(1);

            const visaSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="65" height="34" viewBox="0 0 65 34" fill="none">
                <rect width="65" height="34" rx="5" fill="#222357" />
                <path transform="translate(-25 -21)" d="M58.2042 35.4756C58.1808 37.4259 59.8479 38.5142 61.1037 39.1612C62.3939 39.8251 62.8273 40.2509 62.8222 40.8446C62.8125 41.7532 61.793 42.1542 60.8389 42.1698C59.1744 42.1971 58.2066 41.6946 57.4372 41.3146L56.8376 44.2814C57.6095 44.6576 59.0389 44.9856 60.5212 45C64.0006 45 66.2769 43.1839 66.2892 40.3681C66.3028 36.7944 61.6146 36.5966 61.6466 34.9992C61.6577 34.5149 62.0947 33.998 63.0525 33.8666C63.5265 33.8002 64.8352 33.7494 66.3188 34.4719L66.9012 31.6014C66.1033 31.2942 65.0778 31 63.801 31C60.5262 31 58.2228 32.8409 58.2042 35.4756ZM72.4967 31.2473C71.8614 31.2473 71.326 31.6391 71.087 32.2405L66.1169 44.7892H69.5937L70.2856 42.7673H74.5342L74.9356 44.7892H78L75.3259 31.2473H72.4967ZM72.9831 34.9054L73.9865 39.9906H71.2385L72.9831 34.9054ZM53.9887 31.2474L51.2481 44.789H54.5613L57.3006 31.2471L53.9887 31.2474ZM49.0875 31.2474L45.639 40.4644L44.244 32.6273C44.0803 31.7524 43.434 31.2473 42.7161 31.2473H37.079L37 31.6405C38.1573 31.906 39.4722 32.3343 40.2688 32.7926C40.7563 33.0725 40.8953 33.3172 41.0555 33.9825L43.6976 44.7892H47.1988L52.5665 31.2473L49.0875 31.2474Z" fill="white" />
            </svg>`;
            const mastercardSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="65" height="34" viewBox="0 0 65 34" fill="none">
                <rect width="65" height="34" rx="5" fill="#3B3731" />
                <path d="M27.7742 9.13867H36.501V24.8609H27.7742V9.13867Z" fill="#FF5F00" />
                <path d="M28.3284 17C28.3284 13.8056 29.8243 10.9722 32.1237 9.13884C30.4339 7.80553 28.3007 7 25.9736 7C20.4603 7 16 11.4722 16 17C16 22.5278 20.4603 27 25.9735 27C28.3006 27 30.4338 26.1945 32.1237 24.861C29.8243 23.0555 28.3284 20.1944 28.3284 17Z" fill="#EB001B" />
                <path d="M48.2751 17C48.2751 22.5277 43.8148 27 38.3017 27C35.9745 27 33.8414 26.1945 32.1514 24.861C34.4786 23.0278 35.9469 20.1944 35.9469 17C35.9469 13.8056 34.4508 10.9722 32.1514 9.13884C33.8412 7.80553 35.9745 7 38.3017 7C43.8148 7 48.2751 11.5 48.2751 17Z" fill="#F79E1B" />
            </svg>`;

            document.getElementById('remove_card_icon').innerHTML =
                card === 'mastercard' ? mastercardSvg : visaSvg;
            document.getElementById('remove_card_text').textContent =
                `${cardLabel} ending in ${last4}`;
            document.getElementById('remove_card_exp').textContent =
                `Exp. date ${exp}`;
            document.getElementById('card_removed_text').innerHTML =
                `${cardLabel} ending in ${last4} is no longer saved <br> to your account.`;

            document.getElementById('payment_modal').style.display = 'none';
            document.getElementById('remove_card_alert_modal').style.display = 'flex';
        });

        document.getElementById('confirm_remove_card').addEventListener('click', function() {
            document.getElementById('remove_card_alert_modal').style.display = 'none';
            document.getElementById('card_removed_modal').style.display = 'flex';
        });

    </script>

    <script>
        const input = document.getElementById('toggle-input');

        input.addEventListener('change', () => {
            if (input.checked) {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        });

        const navbar = document.querySelector('header nav.navbar');

        function updateNavHeight() {
            const height = navbar.offsetHeight;
            document.documentElement.style.setProperty('--nav-height', height + 'px');
        }

        updateNavHeight();
        window.addEventListener('resize', updateNavHeight);
    </script>
@endpush
