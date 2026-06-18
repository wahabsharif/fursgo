@props(['transactions' => [], 'isSpaceUser' => false])

<div>
    <div class="earnings-transactions-card">
        <div class="earnings-transactions-table-wrap">
            <table class="earnings-transactions-table {{ $isSpaceUser ? 'is-space-user' : '' }}">
                <thead>
                    <tr>
                        <th>Date &amp; Time</th>
                        @if ($isSpaceUser)
                            <th>Service Type</th>
                            <th>Space</th>
                        @else
                            <th>Client</th>
                            <th>Pet</th>
                        @endif
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Charge Status</th>
                        <th>Booking ID</th>
                        <th class="earnings-transactions-table__receipt">View Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr wire:key="earnings-transaction-{{ $transaction['booking_id'] }}-{{ $transaction['date'] }}">
                            <td>
                                <span class="earnings-transactions-date">{{ $transaction['date'] }}</span>
                                <span class="earnings-transactions-time">{{ $transaction['time'] }}</span>
                            </td>
                            @if ($isSpaceUser)
                                <td class="earnings-transactions-service-type" style="font-weight: 600;">
                                    {{ $transaction['service_type'] }}</td>
                                <td class="earnings-transactions-space" style="font-weight: 600;">
                                    {{ $transaction['space'] }}
                                </td>
                            @else
                                <td class="earnings-transactions-client">{{ $transaction['client'] }}</td>
                                <td>
                                    <div class="earnings-transactions-pet-cell">
                                        <span class="earnings-transactions-pet">{{ $transaction['pet'] }}</span>
                                        <span
                                            class="earnings-transactions-pet-type">{{ $transaction['pet_type'] }}</span>
                                    </div>
                                </td>
                            @endif
                            <td>£{{ number_format((float) $transaction['amount'], 2) }}</td>
                            <td>{{ $transaction['payment_method'] }}</td>
                            <td>
                                <span class="earnings-transactions-status is-{{ $transaction['status_key'] }}">
                                    {{ $transaction['status_label'] }}
                                </span>
                            </td>
                            <td>{{ $transaction['booking_reference'] }}</td>
                            <td class="earnings-transactions-table__receipt">
                                <button type="button" class="earnings-transactions-receipt-btn"
                                    data-earnings-receipt='{{ base64_encode(json_encode($transaction['receipt'] ?? [])) }}'
                                    onclick="window.openEarningsReceiptModal?.(this.dataset.earningsReceipt)"
                                    aria-label="View receipt">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="13"
                                        viewBox="0 0 19 13" fill="none">
                                        <path
                                            d="M9.49609 12C11.4291 12 12.9961 10.433 12.9961 8.5C12.9961 6.567 11.4291 5 9.49609 5C7.5631 5 5.99609 6.567 5.99609 8.5C5.99609 10.433 7.5631 12 9.49609 12Z"
                                            stroke="black" />
                                        <path
                                            d="M18.4961 8.5C18.4961 8.5 17.4961 0.5 9.49609 0.5C1.49609 0.5 0.496094 8.5 0.496094 8.5"
                                            stroke="black" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="earnings-transactions-empty">No transactions available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@once
    @push('script')
        <script>
            window.openEarningsReceiptModal = function(encodedReceipt) {
                if (!encodedReceipt) {
                    return;
                }

                let receipt = null;

                try {
                    receipt = JSON.parse(atob(encodedReceipt));
                } catch (error) {
                    console.warn('Could not read receipt data.', error);
                    return;
                }

                if (!receipt || !receipt.booking_id) {
                    return;
                }

                let modal = document.querySelector('[data-earnings-receipt-modal]');

                if (!modal) {
                    modal = document.createElement('div');
                    modal.dataset.earningsReceiptModal = 'true';
                    modal.className = 'completed-booking-modal-overlay';
                    modal.innerHTML = `
                        <div class="completed-booking-modal-card" data-receipt-card role="dialog" aria-modal="true" aria-labelledby="earnings-receipt-title">
                            <div class="completed-booking-modal-head">
                                <h3 class="completed-booking-modal-title" id="earnings-receipt-title">Booking Receipt</h3>
                                <button type="button" class="completed-booking-modal-close" data-receipt-close aria-label="Close modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                        <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                                        <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </button>
                            </div>
                            <div class="completed-booking-modal-booking-row">
                                <strong data-field="booking_id_label"></strong>
                                <div class="completed-booking-modal-booking-meta">
                                    <span data-field="date_label"></span>
                                    <button type="button" class="completed-booking-download-btn" data-receipt-download aria-label="Download invoice">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                            <path d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M7.99997 0.5V12.875M12.0909 8.75L7.99997 13.25L3.90906 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="completed-booking-modal-customer">
                                <div class="completed-booking-modal-user-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="36" viewBox="0 0 32 36" fill="none">
                                        <ellipse cx="17.3667" cy="18.0807" rx="10.2458" ry="9.64315" fill="white" />
                                        <path d="M16.8932 0.202494C16.6132 0.0698256 16.3132 0 15.9998 0C15.6865 0 15.3865 0.0698256 15.1065 0.202494L2.55333 5.78156C1.08668 6.43094 -0.00663626 7.94615 3.03229e-05 9.77559C0.0333633 16.7023 2.75333 29.3756 14.2399 35.1362C15.3532 35.6949 16.6465 35.6949 17.7598 35.1362C29.2463 29.3756 31.9663 16.7023 31.9996 9.77559C32.0063 7.94615 30.913 6.43094 29.4463 5.78156L16.8932 0.202494ZM9.65991 19.9841C9.97991 20.0679 10.3199 20.1098 10.6666 20.1098C13.0199 20.1098 14.9332 18.1058 14.9332 15.6409V11.1721H17.8798C18.6865 11.1721 19.4265 11.6469 19.7865 12.408L20.2665 13.4065H24.5331C25.1197 13.4065 25.5997 13.9093 25.5997 14.5237V16.7581C25.5997 19.8444 23.2131 22.3442 20.2665 22.3442H17.0665V25.8844C17.0665 26.3941 16.6732 26.813 16.1798 26.813C16.0598 26.813 15.9398 26.7851 15.8332 26.7362L9.25325 23.7826C8.81326 23.5871 8.53326 23.1332 8.53326 22.6375C8.53326 22.4419 8.57326 22.2534 8.65993 22.0789L9.65991 19.9841ZM9.59992 11.1721H12.7999V15.6409C12.7999 16.8769 11.8466 17.8754 10.6666 17.8754C9.48658 17.8754 8.53326 16.8769 8.53326 15.6409V12.2893C8.53326 11.6748 9.01326 11.1721 9.59992 11.1721ZM18.1331 14.5237C18.1331 14.2274 18.0208 13.9433 17.8207 13.7337C17.6207 13.5242 17.3494 13.4065 17.0665 13.4065C16.7836 13.4065 16.5123 13.5242 16.3123 13.7337C16.1122 13.9433 15.9998 14.2274 15.9998 14.5237C15.9998 14.82 16.1122 15.1042 16.3123 15.3137C16.5123 15.5232 16.7836 15.6409 17.0665 15.6409C17.3494 15.6409 17.6207 15.5232 17.8207 15.3137C18.0208 15.1042 18.1331 14.82 18.1331 14.5237Z" fill="#E2E2E2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="completed-booking-modal-owner" data-field="owner_name"></p>
                                    <p class="completed-booking-modal-pet" data-pet-line>
                                        <span data-field="pet_name"></span><span class="completed-booking-modal-pet-type" data-field="pet_type"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="completed-booking-modal-section">
                                <p class="completed-booking-modal-section-label">
                                    <span class="completed-booking-modal-section-label-inner">
                                        <svg data-space-section-icon xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13" fill="none" aria-hidden="true">
                                            <path d="M13.1097 12.1166V3.83417C13.1097 3.81429 13.1113 3.79482 13.1144 3.77576L10.875 1.86616C10.3988 1.46067 10.0698 1.18119 9.79071 0.998982C9.52119 0.823101 9.34008 0.766834 9.16683 0.766834C8.99372 0.766835 8.81374 0.823306 8.54452 0.998982C8.26536 1.18121 7.93548 1.46044 7.45863 1.86616L5.2177 3.77576C5.22078 3.7949 5.22398 3.81422 5.22398 3.83417V12.1166C5.22364 12.3281 5.04366 12.5 4.82168 12.5C4.59985 12.4998 4.41972 12.328 4.41938 12.1166V4.45573L4.00451 4.81069C3.83888 4.95183 3.58373 4.93709 3.43564 4.77924C3.28813 4.62148 3.30193 4.3796 3.46707 4.23856L6.92118 1.29553H6.92275C7.38366 0.90337 7.75691 0.583679 8.08879 0.366942C8.4307 0.143752 8.77013 2.24995e-07 9.16683 0C9.56348 0 9.90284 0.143743 10.2449 0.366942C10.577 0.583731 10.9518 0.903225 11.4125 1.29553L14.8666 4.23856C15.0317 4.3796 15.0455 4.62148 14.898 4.77924C14.7499 4.93709 14.4948 4.95183 14.3291 4.81069L13.9143 4.45573V12.1166C13.9139 12.328 13.7338 12.4998 13.512 12.5C13.29 12.5 13.11 12.3281 13.1097 12.1166Z" fill="#9D9B98" />
                                            <path d="M1.82418 6.66737C1.82418 6.37816 1.74192 6.13002 1.62487 5.96249C1.50777 5.79507 1.37173 5.7247 1.25 5.7247C1.12833 5.7248 0.992145 5.79519 0.875132 5.96249C0.758177 6.13002 0.675818 6.37832 0.675818 6.66737C0.675926 6.95653 0.758033 7.20483 0.875132 7.37226C0.992124 7.53946 1.12837 7.60853 1.25 7.60863C1.37164 7.60863 1.50783 7.53939 1.62487 7.37226C1.74197 7.20483 1.82407 6.95653 1.82418 6.66737ZM2.5 6.66737C2.49989 7.09818 2.37897 7.50235 2.16605 7.80679C1.95294 8.11149 1.63215 8.33333 1.25 8.33333C0.868121 8.33323 0.548331 8.11124 0.335269 7.80679C0.12233 7.50234 0.000106589 7.0982 0 6.66737C0 6.23634 0.122237 5.83113 0.335269 5.52654C0.548331 5.22219 0.868196 5.0001 1.25 5C1.63209 5 1.95294 5.22191 2.16605 5.52654C2.37908 5.83113 2.5 6.23634 2.5 6.66737Z" fill="#9D9B98" />
                                            <path d="M0.833008 12.1094V7.8906C0.833008 7.67488 1.01956 7.5 1.24967 7.5C1.47979 7.5 1.66634 7.67488 1.66634 7.8906V12.1094C1.66617 12.325 1.47968 12.5 1.24967 12.5C1.01966 12.5 0.833183 12.325 0.833008 12.1094Z" fill="#9D9B98" />
                                            <path d="M10.6579 9.31364C10.6579 8.9734 10.6564 8.75738 10.6348 8.59906C10.6147 8.4523 10.584 8.41411 10.5654 8.39576C10.5468 8.37748 10.5083 8.34577 10.3588 8.32597C10.1978 8.30466 9.97715 8.30473 9.63096 8.30473H8.92167C8.57549 8.30473 8.35488 8.30466 8.19387 8.32597C8.04438 8.34577 8.00583 8.37748 7.98725 8.39576C7.96865 8.41411 7.93793 8.4523 7.91787 8.59906C7.89622 8.75738 7.89474 8.9734 7.89474 9.31364V11.7229H10.6579V9.31364ZM9.98715 5.42972C10.2048 5.42988 10.3816 5.60399 10.3819 5.81811C10.3819 6.03251 10.205 6.20634 9.98715 6.2065H8.56548C8.34762 6.20634 8.17074 6.03251 8.17074 5.81811C8.17108 5.60399 8.34782 5.42988 8.56548 5.42972H9.98715ZM9.98715 3.33301L10.0658 3.34059C10.246 3.37657 10.3819 3.53349 10.3819 3.7214C10.3819 3.90931 10.246 4.06623 10.0658 4.10221L9.98715 4.10979H8.56548C8.34762 4.10963 8.17074 3.9358 8.17074 3.7214C8.17074 3.507 8.34762 3.33317 8.56548 3.33301H9.98715ZM11.4474 11.7229H14.6053C14.8233 11.7229 15 11.8968 15 12.1113C14.9997 12.3255 14.8231 12.4997 14.6053 12.4997H0.394737C0.176935 12.4997 0.000332468 12.3255 0 12.1113C0 11.8968 0.17673 11.7229 0.394737 11.7229H7.10526V9.31364C7.10526 8.99552 7.10427 8.71791 7.13456 8.4959C7.16648 8.26247 7.23958 8.03308 7.42907 7.84655C7.61867 7.66 7.85172 7.58819 8.08902 7.55678C8.31486 7.52691 8.59793 7.52795 8.92167 7.52795H9.63096C9.95471 7.52795 10.2378 7.52691 10.4636 7.55678C10.7009 7.58819 10.934 7.66 11.1236 7.84655C11.313 8.03308 11.3862 8.26247 11.4181 8.4959C11.4484 8.71791 11.4474 8.99552 11.4474 9.31364V11.7229Z" fill="#9D9B98" />
                                        </svg>
                                        <svg data-service-section-icon xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none" aria-hidden="true">
                                            <path d="M3.79507 8.7133C4.74998 9.66821 7.07244 8.89426 8.98226 6.98414C10.8924 5.07433 11.6663 2.75186 10.7114 1.79695M6.60477 1.14832L7.03699 1.58084M5.09202 2.66138L5.52423 3.09359M3.79476 4.39054L4.22698 4.82276M3.36255 6.55192L3.79476 6.98414M8.98226 0.5L9.41447 0.932215M8.55004 3.0939L9.41447 3.95833M7.03729 4.60696L7.90172 5.47139M5.30813 5.9036L6.17256 6.76803" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M3.79466 10.0107C4.15277 9.65258 4.15277 9.07196 3.79466 8.71385C3.43655 8.35574 2.85593 8.35574 2.49782 8.71385L0.768699 10.443C0.410587 10.8011 0.410587 11.3817 0.768699 11.7398C1.12681 12.0979 1.70743 12.0979 2.06554 11.7398L3.79466 10.0107Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span data-field="section_label"></span>
                                    </span>
                                </p>
                                <div class="completed-booking-modal-line">
                                    <div>
                                        <p data-field="service_primary"></p>
                                        <p class="completed-booking-modal-line-sub" data-field="service_sub"></p>
                                    </div>
                                    <span data-field="service_amount"></span>
                                </div>
                            </div>
                            <div class="completed-booking-modal-section">
                                <p class="completed-booking-modal-section-title">Extras &amp; Add-ons</p>
                                <div data-addons></div>
                            </div>
                            <div class="completed-booking-modal-total-block">
                                <div class="completed-booking-modal-total-row"><span>Service:</span><span data-field="service_total"></span></div>
                                <div class="completed-booking-modal-total-row"><span>Extras &amp; Add-ons:</span><span data-field="extras_total"></span></div>
                                <div class="completed-booking-modal-total-row"><span>Promo discount:</span><span style="color: #9D9B98;" data-field="promo_discount"></span></div>
                                <div class="completed-booking-modal-total-row is-grand"><span>Total</span><span data-field="grand_total"></span></div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    modal.addEventListener('click', function(event) {
                        if (event.target === modal || event.target.closest('[data-receipt-close]')) {
                            closeEarningsReceiptModal();
                        }
                    });
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            closeEarningsReceiptModal();
                        }
                    });
                }

                const setText = (name, value) => {
                    modal.querySelectorAll(`[data-field="${name}"]`).forEach((node) => {
                        node.textContent = value || '';
                    });
                };

                modal.querySelector('[data-receipt-card]')?.classList.toggle('is-space-user', Boolean(receipt
                    .is_space_user));
                setText('booking_id_label', `Booking ID: ${receipt.booking_id_label}`);
                setText('date_label', receipt.date_label);
                setText('owner_name', receipt.owner_name);
                setText('pet_name', receipt.pet_name);
                setText('pet_type', receipt.pet_type);
                setText('section_label', receipt.is_space_user ? 'Space' : 'Service');
                setText('service_primary', receipt.is_space_user ? receipt.space_label : receipt.service);
                setText('service_sub', receipt.is_space_user ? receipt.service_time_label_for_space : receipt.pet_name);
                setText('service_amount', `£${receipt.service_amount_formatted}`);
                setText('service_total', `£${receipt.service_amount_formatted}`);
                setText('extras_total', `£${receipt.extras_amount_formatted}`);
                setText('promo_discount', `- £${receipt.promo_discount_formatted}`);
                setText('grand_total', `£${receipt.total_amount_formatted}`);

                const petLine = modal.querySelector('[data-pet-line]');
                if (petLine) {
                    petLine.style.display = receipt.is_space_user ? 'none' : '';
                }

                const spaceIcon = modal.querySelector('[data-space-section-icon]');
                const serviceIcon = modal.querySelector('[data-service-section-icon]');
                if (spaceIcon && serviceIcon) {
                    spaceIcon.style.display = receipt.is_space_user ? '' : 'none';
                    serviceIcon.style.display = receipt.is_space_user ? 'none' : '';
                }

                const downloadButton = modal.querySelector('[data-receipt-download]');
                if (downloadButton) {
                    downloadButton.onclick = () => window.downloadBookingInvoicePdf?.(receipt.invoice_url);
                }

                const addons = modal.querySelector('[data-addons]');
                if (addons) {
                    addons.textContent = '';
                    const items = Array.isArray(receipt.addons) && receipt.addons.length ?
                        receipt.addons : [{
                            label: 'No add-ons recorded',
                            amount_formatted: receipt.extras_amount_formatted,
                        }];

                    items.forEach((addon) => {
                        const row = document.createElement('div');
                        row.className = 'completed-booking-modal-line completed-booking-addon-line';
                        row.innerHTML = `<p class="completed-booking-modal-line-sub"></p><span></span>`;
                        row.querySelector('p').textContent = addon.label;
                        row.querySelector('span').textContent = `£${addon.amount_formatted}`;
                        addons.appendChild(row);
                    });
                }

                if (modal.__closeTimer) {
                    clearTimeout(modal.__closeTimer);
                    modal.__closeTimer = null;
                }

                modal.style.display = 'flex';
                requestAnimationFrame(() => {
                    modal.classList.add('is-open');
                });
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            };

            window.closeEarningsReceiptModal = function() {
                const modal = document.querySelector('[data-earnings-receipt-modal]');

                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.__closeTimer = setTimeout(() => {
                    modal.style.display = 'none';
                    modal.__closeTimer = null;
                }, 180);
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            };
        </script>
    @endpush
@endonce
