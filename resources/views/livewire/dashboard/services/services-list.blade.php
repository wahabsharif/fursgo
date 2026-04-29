<?php

use Livewire\Volt\Component;

new class extends Component {
    // Static list UI scaffold.
}; ?>

<section class="service-list-wrapper" aria-label="Service list">
    <div class="service-list-table-shell">
        <table class="service-list-table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Applies to</th>
                    <th>Base Duration</th>
                    <th>Base Price</th>
                    <th>Active</th>
                    <th class="service-edit-col">Edit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Full Groom</td>
                    <td>Cat, Dog, Other</td>
                    <td>1h</td>
                    <td>£45.00</td>
                    <td><span class="service-toggle is-on" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr>
                    <td>Face Trim Only</td>
                    <td>Cat, Dog, Other</td>
                    <td>20 mins</td>
                    <td>£10.00</td>
                    <td><span class="service-toggle is-on" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr>
                    <td>Tail Trim Only</td>
                    <td>Cat, Dog, Other</td>
                    <td>10 mins</td>
                    <td>£5.00</td>
                    <td><span class="service-toggle is-on" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr class="is-muted">
                    <td>Bath &amp; Brush</td>
                    <td>Cat, Dog, Other</td>
                    <td>1h</td>
                    <td>£20.00</td>
                    <td><span class="service-toggle" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr>
                    <td>Nail Trim</td>
                    <td>Cat, Dog, Other</td>
                    <td>5 mins</td>
                    <td>£15.00</td>
                    <td><span class="service-toggle is-on" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr>
                    <td>Ear Cleaning</td>
                    <td>Cat, Dog</td>
                    <td>15 mins</td>
                    <td>£20.00</td>
                    <td><span class="service-toggle is-on" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
                <tr class="is-muted">
                    <td>Luxury Spa</td>
                    <td>Cat, Dog</td>
                    <td>90 mins</td>
                    <td>£60.00</td>
                    <td><span class="service-toggle" aria-hidden="true"></span></td>
                    <td class="service-edit-col">
                        <button type="button" class="icon-btn" aria-label="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                viewBox="0 0 24 24" fill="none">
                                <path d="M12 20H21" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                                <path d="M16.5 3.5A2.121 2.121 0 0 1 19.5 6.5L8 18L4 19L5 15L16.5 3.5Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="icon-btn dots-btn" aria-label="Service actions">•••</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="service-load-more-wrap">
        <button type="button" class="service-load-more-btn">Load More</button>
    </div>
</section>

<style>
    .service-list-wrapper {
        margin-top: 0;
    }

    .service-list-table-shell {
        overflow-x: auto;
    }

    .service-list-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }

    .service-list-table th,
    .service-list-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        padding: 1.2rem 0;
    }

    .service-list-table td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .service-list-table th {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-list-table .service-edit-col {
        border-left: 1px solid #dcdcdc;
        text-align: left;
        padding-left: 4rem;
    }

    .service-list-table th.service-edit-col {
        width: 180px;
        text-align: center;
    }

    .service-list-table th.service-edit-col:last-child {
        width: 23rem;
        text-align: left;
        padding-left: 4rem;
    }

    .service-list-table tr.is-muted td:not(.service-edit-col) {
        opacity: 0.5;
    }

    .service-toggle {
        width: 56px;
        height: 30px;
        border-radius: 999px;
        background: #cfcfcf;
        position: relative;
        display: inline-block;
        cursor: pointer;
        transition: background-color 0.24s ease;
    }

    .service-toggle::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 4px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: white !important;
        z-index: 1;
        transition: left 0.24s ease;
    }

    .service-toggle::before {
        content: none;
        opacity: 0;
        transform: scale(0.88);
    }

    .service-toggle.is-on {
        background: #c7d59f;
    }

    .service-toggle.is-on::after {
        left: 28.5px;
        background: #ffffff;
        z-index: 1;
    }

    .service-toggle.is-on::before {
        content: "";
        position: absolute;
        right: 9px;
        top: 9px;
        width: 13px;
        height: 13px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='11' viewBox='0 0 13 11' fill='none'%3E%3Cpath d='M1.25 5.8L4.4 8.95L11.75 1.6' stroke='%23C7D59F' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        z-index: 2;
        opacity: 1;
        transform: scale(1);
        animation: toggle-icon-in 0.16s ease-in 0.24s both;
    }

    @keyframes toggle-icon-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .icon-btn {
        border: 0;
        background: transparent;
        color: #4a4a4a;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        vertical-align: middle;
    }

    .dots-btn {
        font-size: 22px;
        letter-spacing: 2px;
        margin-left: 4.5rem;
    }

    .service-load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
    }

    .service-load-more-btn {
        width: 133px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 75px;
        border: 1px solid #3B3731;
        background: transparent;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }
</style>

@script
    <script>
        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('.service-toggle');

            if (!toggle) {
                return;
            }

            const row = toggle.closest('tr');
            const isOn = toggle.classList.toggle('is-on');

            if (row) {
                row.classList.toggle('is-muted', !isOn);
            }
        });
    </script>
@endscript
