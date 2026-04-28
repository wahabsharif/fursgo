<div {{ $attributes->merge(['class' => 'dashboard-section-host']) }}>
    <section class="ma-board" aria-label="Manage availability board">
        <div class="ma-staff-strip">
            <div class="ma-staff-list">
                @foreach ([1, 2, 3] as $staffIndex)
                    <button type="button" class="ma-staff-pill {{ $loop->first ? 'ma-staff-pill--active' : '' }}">
                        <span class="ma-staff-avatar">N</span>
                        <span>
                            <strong>Name</strong>
                            <small>Job Title</small>
                        </span>
                    </button>
                @endforeach
                <button type="button" class="ma-staff-add" aria-label="Add staff">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14"
                        fill="none">
                        <path
                            d="M13 7.12813C13 7.68041 12.5523 8.12813 12 8.12813H7.66288V13C7.66288 13.5523 7.21516 14 6.66288 14H6.31024C5.75795 14 5.31024 13.5523 5.31024 13V8.12813H0.999999C0.447714 8.12813 0 7.68041 0 7.12813V6.85786C0 6.30557 0.447715 5.85786 1 5.85786H5.31024V0.999999C5.31024 0.447714 5.75795 0 6.31024 0H6.66288C7.21516 0 7.66288 0.447715 7.66288 1V5.85786H12C12.5523 5.85786 13 6.30557 13 6.85786V7.12813Z"
                            fill="#3B3731" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="ma-section">
            <div class="ma-title-row">
                <h3 style="border: none;width: fit-content; padding: 0;">Work Week Hours</h3>
                <div class="ma-staff-name">
                    <strong>Staff Name</strong>
                    <small>Job Title</small>
                </div>
            </div>

            <div class="ma-week-grid">
                <div class="ma-week-grid__head">Working Week</div>
                <div class="ma-week-grid__head">Start Time - End Time</div>
                <div class="ma-week-grid__head">Edit</div>

                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    @php
                        $isEnabled = in_array($day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], true);
                    @endphp
                    <div class="ma-cell" style="padding-left: 0 !important;">
                        <div class="ma-day-name">
                            <span>{{ $day }}</span>
                            <label class="ma-switch" style="height: 24px;">
                                <input type="checkbox" {{ $isEnabled ? 'checked' : '' }}>
                                <span class="ma-switch-slider"></span>
                                <span class="ma-switch-check-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="ma-cell ma-time-range">
                        @if ($isEnabled)
                            <span class="ma-time-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25" stroke="#9D9B98"
                                        stroke-width="1.5" />
                                    <path d="M12 8V12L14.75 13.75" stroke="#9D9B98" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                07:00 AM
                            </span>
                            <span class="ma-time-separator"></span>
                            <span class="ma-time-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                    viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="8.25" stroke="#9D9B98"
                                        stroke-width="1.5" />
                                    <path d="M12 8V12L14.75 13.75" stroke="#9D9B98" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                14:00 PM
                            </span>
                        @else
                            <div class="ma-time-disabled"></div>
                        @endif
                    </div>
                    <div class="ma-cell ma-edit-cell">
                        <button type="button" class="ma-row-action"
                            aria-label="{{ $isEnabled ? 'Delete day slot' : 'Edit day slot' }}">
                            @if ($isEnabled)
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="15"
                                    viewBox="0 0 13 15" fill="none">
                                    <path
                                        d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                        fill="#3B3731" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                    viewBox="0 0 17 16" fill="none">
                                    <path
                                        d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                        </button>
                        @if ($isEnabled)
                            <button type="button" class="ma-save-mini">Save</button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="ma-section">
            <h3>Holiday / Time Off</h3>
            <div class="ma-holiday-grid">
                <div class="ma-holiday-form">
                    <div class="ma-inline-fields">
                        <label class="ma-field">
                            <span>Date From</span>
                            <span class="ma-field-value">
                                <span class="ma-field-value__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                        viewBox="0 0 15 14" fill="none">
                                        <path
                                            d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                            stroke="#3B3731" />
                                        <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M11.643 10.166C11.643 10.3428 11.5696 10.5124 11.439 10.6374C11.3084 10.7624 11.1312 10.8327 10.9465 10.8327C10.7618 10.8327 10.5847 10.7624 10.4541 10.6374C10.3235 10.5124 10.2501 10.3428 10.2501 10.166C10.2501 9.98925 10.3235 9.81969 10.4541 9.69468C10.5847 9.56967 10.7618 9.49944 10.9465 9.49944C11.1312 9.49944 11.3084 9.56967 11.439 9.69468C11.5696 9.81969 11.643 9.98925 11.643 10.166ZM11.643 7.49961C11.643 7.67641 11.5696 7.84596 11.439 7.97098C11.3084 8.09599 11.1312 8.16622 10.9465 8.16622C10.7618 8.16622 10.5847 8.09599 10.4541 7.97098C10.3235 7.84596 10.2501 7.67641 10.2501 7.49961C10.2501 7.32282 10.3235 7.15327 10.4541 7.02825C10.5847 6.90324 10.7618 6.83301 10.9465 6.83301C11.1312 6.83301 11.3084 6.90324 11.439 7.02825C11.5696 7.15327 11.643 7.32282 11.643 7.49961ZM8.1609 10.166C8.1609 10.3428 8.08752 10.5124 7.95692 10.6374C7.82632 10.7624 7.64918 10.8327 7.46448 10.8327C7.27978 10.8327 7.10265 10.7624 6.97205 10.6374C6.84144 10.5124 6.76807 10.3428 6.76807 10.166C6.76807 9.98925 6.84144 9.81969 6.97205 9.69468C7.10265 9.56967 7.27978 9.49944 7.46448 9.49944C7.64918 9.49944 7.82632 9.56967 7.95692 9.69468C8.08752 9.81969 8.1609 9.98925 8.1609 10.166ZM8.1609 7.49961C8.1609 7.67641 8.08752 7.84596 7.95692 7.97098C7.82632 8.09599 7.64918 8.16622 7.46448 8.16622C7.27978 8.16622 7.10265 8.09599 6.97205 7.97098C6.84144 7.84596 6.76807 7.67641 6.76807 7.49961C6.76807 7.32282 6.84144 7.15327 6.97205 7.02825C7.10265 6.90324 7.27978 6.83301 7.46448 6.83301C7.64918 6.83301 7.82632 6.90324 7.95692 7.02825C8.08752 7.15327 8.1609 7.32282 8.1609 7.49961ZM4.67884 10.166C4.67884 10.3428 4.60546 10.5124 4.47486 10.6374C4.34426 10.7624 4.16712 10.8327 3.98242 10.8327C3.79772 10.8327 3.62059 10.7624 3.48999 10.6374C3.35938 10.5124 3.28601 10.3428 3.28601 10.166C3.28601 9.98925 3.35938 9.81969 3.48999 9.69468C3.62059 9.56967 3.79772 9.49944 3.98242 9.49944C4.16712 9.49944 4.34426 9.56967 4.47486 9.69468C4.60546 9.81969 4.67884 9.98925 4.67884 10.166ZM4.67884 7.49961C4.67884 7.67641 4.60546 7.84596 4.47486 7.97098C4.34426 8.09599 4.16712 8.16622 3.98242 8.16622C3.79772 8.16622 3.62059 8.09599 3.48999 7.97098C3.35938 7.84596 3.28601 7.67641 3.28601 7.49961C3.28601 7.32282 3.35938 7.15327 3.48999 7.02825C3.62059 6.90324 3.79772 6.83301 3.98242 6.83301C4.16712 6.83301 4.34426 6.90324 4.47486 7.02825C4.60546 7.15327 4.67884 7.32282 4.67884 7.49961Z"
                                            fill="#3B3731" />
                                    </svg>
                                </span>
                                <span id="manage-availability-date-from"></span>
                            </span>
                        </label>
                        <label class="ma-field">
                            <span>Date To</span>
                            <span class="ma-field-value">
                                <span class="ma-field-value__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                        viewBox="0 0 15 14" fill="none">
                                        <path
                                            d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                            stroke="#3B3731" />
                                        <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M11.643 10.166C11.643 10.3428 11.5696 10.5124 11.439 10.6374C11.3084 10.7624 11.1312 10.8327 10.9465 10.8327C10.7618 10.8327 10.5847 10.7624 10.4541 10.6374C10.3235 10.5124 10.2501 10.3428 10.2501 10.166C10.2501 9.98925 10.3235 9.81969 10.4541 9.69468C10.5847 9.56967 10.7618 9.49944 10.9465 9.49944C11.1312 9.49944 11.3084 9.56967 11.439 9.69468C11.5696 9.81969 11.643 9.98925 11.643 10.166ZM11.643 7.49961C11.643 7.67641 11.5696 7.84596 11.439 7.97098C11.3084 8.09599 11.1312 8.16622 10.9465 8.16622C10.7618 8.16622 10.5847 8.09599 10.4541 7.97098C10.3235 7.84596 10.2501 7.67641 10.2501 7.49961C10.2501 7.32282 10.3235 7.15327 10.4541 7.02825C10.5847 6.90324 10.7618 6.83301 10.9465 6.83301C11.1312 6.83301 11.3084 6.90324 11.439 7.02825C11.5696 7.15327 11.643 7.32282 11.643 7.49961ZM8.1609 10.166C8.1609 10.3428 8.08752 10.5124 7.95692 10.6374C7.82632 10.7624 7.64918 10.8327 7.46448 10.8327C7.27978 10.8327 7.10265 10.7624 6.97205 10.6374C6.84144 10.5124 6.76807 10.3428 6.76807 10.166C6.76807 9.98925 6.84144 9.81969 6.97205 9.69468C7.10265 9.56967 7.27978 9.49944 7.46448 9.49944C7.64918 9.49944 7.82632 9.56967 7.95692 9.69468C8.08752 9.81969 8.1609 9.98925 8.1609 10.166ZM8.1609 7.49961C8.1609 7.67641 8.08752 7.84596 7.95692 7.97098C7.82632 8.09599 7.64918 8.16622 7.46448 8.16622C7.27978 8.16622 7.10265 8.09599 6.97205 7.97098C6.84144 7.84596 6.76807 7.67641 6.76807 7.49961C6.76807 7.32282 6.84144 7.15327 6.97205 7.02825C7.10265 6.90324 7.27978 6.83301 7.46448 6.83301C7.64918 6.83301 7.82632 6.90324 7.95692 7.02825C8.08752 7.15327 8.1609 7.32282 8.1609 7.49961ZM4.67884 10.166C4.67884 10.3428 4.60546 10.5124 4.47486 10.6374C4.34426 10.7624 4.16712 10.8327 3.98242 10.8327C3.79772 10.8327 3.62059 10.7624 3.48999 10.6374C3.35938 10.5124 3.28601 10.3428 3.28601 10.166C3.28601 9.98925 3.35938 9.81969 3.48999 9.69468C3.62059 9.56967 3.79772 9.49944 3.98242 9.49944C4.16712 9.49944 4.34426 9.56967 4.47486 9.69468C4.60546 9.81969 4.67884 9.98925 4.67884 10.166ZM4.67884 7.49961C4.67884 7.67641 4.60546 7.84596 4.47486 7.97098C4.34426 8.09599 4.16712 8.16622 3.98242 8.16622C3.79772 8.16622 3.62059 8.09599 3.48999 7.97098C3.35938 7.84596 3.28601 7.67641 3.28601 7.49961C3.28601 7.32282 3.35938 7.15327 3.48999 7.02825C3.62059 6.90324 3.79772 6.83301 3.98242 6.83301C4.16712 6.83301 4.34426 6.90324 4.47486 7.02825C4.60546 7.15327 4.67884 7.32282 4.67884 7.49961Z"
                                            fill="#3B3731" />
                                    </svg>
                                </span>
                                <span id="manage-availability-date-to"></span>
                            </span>
                        </label>
                    </div>
                    <label class="ma-field">
                        <span>Reason (optional)</span>
                        <textarea rows="3"></textarea>
                    </label>
                    <div class="ma-form-actions">
                        <button type="button" class="ma-save-mini">Save</button>
                    </div>
                </div>
                <div class="ma-holiday-calendar">
                    <x-ui.range-date-calendar id="dashboard-manage-availability-range"
                        start-name="manage_availability_start_date" end-name="manage_availability_end_date"
                        start-value="2025-02-02" end-value="2025-03-02" calendar-width="100%" />
                </div>
            </div>
        </div>

        <div class="ma-section">
            <h3>Pause Bookings</h3>
            <div class="ma-pause-row">
                <span>Pause New Bookings <small>(effective today)</small></span>
                <label class="ma-switch">
                    <input type="checkbox" checked>
                    <span class="ma-switch-slider"></span>
                    <span class="ma-switch-check-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                fill="white" />
                        </svg>
                    </span>
                </label>
            </div>
        </div>

        <div class="ma-footer-actions">
            <button type="button" class="ma-btn ma-btn-light">Cancel</button>
            <button type="button" class="ma-btn ma-btn-primary">Save Changes</button>
        </div>
    </section>
</div>

<style>
    .ma-board {
        margin-top: 2rem;
        width: 100%;
        color: #3B3731;
        font-family: Lato, sans-serif;
    }

    .ma-holiday-form {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .ma-staff-strip {
        border-bottom: 1px solid #d8d1c7;
        margin-bottom: 1.25rem;
    }

    .ma-staff-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .ma-staff-pill {
        border: 0;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        cursor: pointer;
        padding: 0.25rem 1rem 1rem;
        position: relative;
    }

    .ma-staff-pill::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 147px;
        height: 1px;
        background: #3B3731;
        transform: translateX(-50%) scaleX(0);
        transform-origin: center;
        opacity: 0;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .ma-staff-pill--active::after {
        transform: translateX(-50%) scaleX(1);
        opacity: 1;
    }

    .ma-staff-avatar {
        width: 43px;
        height: 43px;
        aspect-ratio: 42.50/42.75;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #d9d9d9;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .ma-staff-pill strong {
        display: block;
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-pill small {
        display: block;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-align: left;
    }

    .ma-staff-add {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 2.5rem;
        border: 0;
        background: transparent;
        color: #3B3731;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        padding: 0.2rem 0.5rem;
    }

    .ma-section {
        margin-bottom: 2rem;
    }

    .ma-section h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        width: 100%;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #D4D4D4;
        margin-bottom: 0.8rem;
    }

    .ma-title-row {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 0.8rem;
        border-bottom: 1px solid #d8d1c7;
        margin-bottom: 0.8rem;
    }

    .ma-staff-name {
        text-align: right;
    }

    .ma-staff-name strong {
        display: block;
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-staff-name small {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .ma-week-grid {
        display: grid;
        margin-top: 1.5rem;
        grid-template-columns: 1.2fr 1.9fr 0.8fr;
        border-bottom: 1px solid #dfd8ce;
    }

    .ma-week-grid__head {
        padding: 1rem 1.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border-right: 1px solid #dfd8ce;
    }

    .ma-week-grid>.ma-week-grid__head:nth-child(3) {
        border-right: 0 !important;
        padding-left: 4.5rem;
    }

    .ma-cell {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-top: 1px solid #ece7df;
        border-right: 1px solid #dfd8ce;
    }

    .ma-cell:nth-child(3n) {
        justify-content: start;
        border-right: 0 !important;
        padding-left: 4.5rem;
    }

    .ma-day-name {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.5rem 1rem;
        background: #F9FAFC;
        border-radius: 10px;
        background: #F7F7F7;
    }

    .ma-day-name span {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        /* 138.889% */
    }

    .ma-time-range {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .ma-time-chip {
        width: 100%;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.9rem;
        border: 1px solid #d5cfc6;
        border-radius: 999px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        min-width: 135px;
        justify-content: center;
    }

    .ma-time-separator {
        background: #D4D4D4;
        width: 1px;
        height: 25px;
    }

    .ma-time-disabled {
        height: 42px;
        width: 100%;
        border-radius: 10px;
        background: #F7F7F7;
    }

    .ma-edit-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
    }

    .ma-row-action {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0.2rem;
    }

    .ma-save-mini {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        width: 74px;
        height: 42px;
        border-radius: 100px;
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }

    .ma-holiday-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        align-items: start;
        margin-top: 2.5rem;
    }

    .ma-inline-fields {
        margin-top: 1.2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem;
    }

    .ma-field {
        display: block;
    }

    .ma-field span {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-field>span:first-child {
        margin-bottom: 1.5rem;
    }

    .ma-field input,
    .ma-field .ma-field-value,
    .ma-field textarea {
        width: 100%;
        border: 1px solid #d8d1c7;
        border-radius: 6px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        /* 138.889% */
        padding: 0.6rem 0.75rem;
    }

    .ma-field .ma-field-value {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 57px;
    }

    .ma-field .ma-field-value__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ma-field textarea {
        min-height: 96px;
        resize: vertical;
    }

    .ma-form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.7rem;
    }

    .ma-holiday-calendar {
        border-radius: 10px;
    }

    .ma-pause-row {
        width: fit-content;
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 1rem;
        border-bottom: 1px solid #dfd8ce;
        padding: 0.75rem 0;
    }

    .ma-pause-row span {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-pause-row small {
        color: #9C9790;
    }

    .ma-switch {
        position: relative;
        display: inline-block;
        width: 42px;
        /* height: 24px; */
    }

    .ma-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .ma-switch-slider {
        width: 44px;
        height: 24px;
        aspect-ratio: 11/6;
        position: absolute;
        cursor: pointer;
        inset: 0;
        border-radius: 999px;
        background: #dfdfdf;
        transition: background-color 0.2s ease;
    }

    .ma-switch-slider::before {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 999px;
        background: #fff;
        top: 4px;
        left: 4px;
        transition: transform 0.2s ease;
    }

    .ma-switch input:checked+.ma-switch-slider {
        background: #d4e5ad;
    }

    .ma-switch input:checked+.ma-switch-slider::before {
        transform: translateX(20px);
        background: transparent;
    }

    .ma-switch-check-icon {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        line-height: 0;
        pointer-events: none;
        opacity: 0;
        transform: translateX(0);
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .ma-switch input:checked~.ma-switch-check-icon {
        opacity: 1;
        transform: translateX(20px);
    }

    .ma-footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
    }

    .ma-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 138px;
        height: 42px;
        border-radius: 100px;
        border: none;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
    }

    .ma-btn-light {
        border: 1px solid #D9D9D9;
        background: transparent;
        color: #9D9B98;
    }

    .ma-btn-primary {
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        color: #fff;
    }
</style>

<script>
    (() => {
        const setupManageAvailability = () => {
            const root = document.querySelector('.ma-board');
            if (!root || root.dataset.rangeSyncBound === '1') {
                return;
            }
            const staffPills = document.querySelectorAll('.ma-staff-pill');

            staffPills.forEach((pill) => {
                pill.addEventListener('click', () => {
                    staffPills.forEach((item) => item.classList.remove(
                        'ma-staff-pill--active'));
                    pill.classList.add('ma-staff-pill--active');
                });
            });

            const calendarRoot = document.getElementById('dashboard-manage-availability-range');
            const dateFromInput = document.getElementById('manage-availability-date-from');
            const dateToInput = document.getElementById('manage-availability-date-to');

            if (!calendarRoot || !dateFromInput || !dateToInput) {
                return;
            }

            const startHiddenInput = calendarRoot.querySelector('input[name="manage_availability_start_date"]');
            const endHiddenInput = calendarRoot.querySelector('input[name="manage_availability_end_date"]');

            if (!startHiddenInput || !endHiddenInput) {
                return;
            }

            root.dataset.rangeSyncBound = '1';

            const formatDate = (isoDate) => {
                if (!isoDate) return '';

                const [year, month, day] = String(isoDate).split('-').map(Number);
                if (!year || !month || !day) return '';

                const date = new Date(year, month - 1, day);
                if (Number.isNaN(date.getTime())) return '';

                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                });
            };

            const syncRangeFields = () => {
                dateFromInput.textContent = formatDate(startHiddenInput.value);
                dateToInput.textContent = formatDate(endHiddenInput.value);
            };

            syncRangeFields();
            setTimeout(syncRangeFields, 0);

            window.addEventListener('range-calendar-changed', (event) => {
                const detail = event?.detail || {};
                if (detail.componentId && detail.componentId !==
                    'dashboard-manage-availability-range') {
                    return;
                }

                dateFromInput.textContent = formatDate(detail.start ?? startHiddenInput.value);
                dateToInput.textContent = formatDate(detail.end ?? endHiddenInput.value);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupManageAvailability);
        } else {
            setupManageAvailability();
        }

        document.addEventListener('livewire:navigated', setupManageAvailability);
    })();
</script>
