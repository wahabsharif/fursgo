<div class="admin-overview">
    <div class="admin-dash-head">
        <div class="admin-dash-head-row">
            <h1 class="admin-page-title mb-0">Hello, Admin</h1>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Summary
            </button>
        </div>

        <div class="admin-dash-sub-row">
            <p class="admin-section-label mb-0 admin-label-growth">Growth - This Month</p>
            <p class="admin-section-label mb-0 admin-label-bookings">Bookings - Live</p>
            <nav class="admin-period-nav" aria-label="Time range">
                <template x-for="item in periods" :key="item.id">
                    <button type="button"
                        :class="{ 'is-active': period === item.id }"
                        @click="setPeriod(item.id)"
                        x-text="item.label">
                    </button>
                </template>
            </nav>
        </div>
    </div>

    <div class="admin-metric-row">
        <div class="admin-card admin-metric-card is-w-320">
            <p class="admin-card-title">New customer sign-ups</p>
            <p class="admin-card-value">47</p>
            <div class="admin-metric-foot">
                <span class="admin-pill-badge up">+10%</span>
                <span class="admin-metric-note">vs last month</span>
            </div>
        </div>
        <div class="admin-card admin-metric-card is-w-320">
            <p class="admin-card-title">New groomer / space host sign-ups</p>
            <p class="admin-card-value">284</p>
            <div class="admin-metric-foot">
                <span class="admin-pill-badge up">+10%</span>
                <span class="admin-metric-note">vs last month</span>
            </div>
        </div>
        <div class="admin-card admin-metric-card is-w-207">
            <p class="admin-card-title">Live active bookings</p>
            <p class="admin-card-value">135</p>
            <p class="admin-card-meta muted mb-0">
                <span class="admin-live-dot"></span>updated now - 15:30
            </p>
        </div>
        <div class="admin-card admin-metric-card is-w-433">
            <p class="admin-card-title">Completed vs cancelled — this month</p>
            <div class="admin-bar" aria-hidden="true">
                <div class="admin-bar-fill" style="width: 100%;"></div>
            </div>
            <div class="admin-bar-legend">
                <span class="is-done">Completed 82% (1,840)</span>
                <span class="is-cancel">Cancelled 18% (402)</span>
            </div>
        </div>
    </div>

    <div class="admin-dash-grid">
        {{-- Left: Growth chart --}}
        <div class="admin-dash-col admin-dash-growth">
            <div class="admin-card admin-card-chart">
                <div class="admin-chart-head">
                    <p class="admin-chart-title">Sign-up growth trends</p>
                    <div class="admin-chart-legend">
                        <div class="admin-chart-legend-top">
                            <span class="admin-chart-legend-dot" aria-hidden="true"></span>
                            <span class="admin-chart-legend-month">May 2025</span>
                        </div>
                        <div class="admin-chart-legend-meta">
                            <span class="admin-pill-badge up">+10%</span>
                            <span class="admin-chart-legend-note">vs last week</span>
                        </div>
                    </div>
                </div>
                <div class="admin-chart-wrap">
                    <canvas id="admin-signup-growth-chart" aria-label="Sign-up growth trends chart"></canvas>
                </div>
            </div>
        </div>

        {{-- Center: Revenue / Pay-outs --}}
        <div class="admin-dash-col admin-dash-mid">
            <p class="admin-section-label">Revenue - Month to Date</p>
            <div class="admin-card-grid admin-revenue-row">
                <div class="admin-card admin-card-link admin-size-207">
                    <p class="admin-card-title">Total revenue MTD</p>
                    <p class="admin-card-value admin-card-value-sm">£84,320</p>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="admin-pill-badge up">+12%</span> <span class="admin-metric-note">vs last month</span>
                        <span class="admin-card-arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.5" />
                            </svg></span>
                    </div>
                </div>
                <div class="admin-card admin-card-link admin-size-207">
                    <p class="admin-card-title">Cleared to bank</p>
                    <p class="admin-card-value admin-card-value-sm">£18,960</p>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="admin-pill-badge up">+22.5% take rate</span>
                        <span class="admin-card-arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.5" />
                            </svg></span>
                    </div>
                </div>
                <div class="admin-card admin-card-link admin-size-207">
                    <p class="admin-card-title">Fees in transit</p>
                    <p class="admin-card-value admin-card-value-sm">£6,140</p>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <span class="admin-pill-badge warn">Pending Settlement</span>
                        <span class="admin-card-arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.5" />
                            </svg></span>
                    </div>
                </div>
            </div>

            <p class="admin-section-label">Pay-outs</p>
            <div class="admin-card-grid admin-payout-row">
                <div class="admin-card admin-card-link admin-size-320">
                    <p class="admin-card-title">Pay-outs due this week</p>
                    <p class="admin-card-value admin-card-value-sm">£22,750</p>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <p class="admin-card-meta muted mb-0">Across 94 groomer / space host accounts</p>
                        <span class="admin-card-arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.5" />
                            </svg></span>
                    </div>
                </div>
                <div class="admin-card admin-card-link admin-size-320">
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <p class="admin-card-title">Verifications pending</p>
                        <span class="admin-pill-badge active-needed">Action needed</span>
                    </div>
                    <p class="admin-card-value admin-card-value-sm admin-value-active-needed">23</p>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                        <p class="admin-card-meta muted mb-0">ID + background checks</p>
                        <span class="admin-card-arrow" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.5" />
                            </svg></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Operations --}}
        <div class="admin-dash-col admin-dash-side">
            <p class="admin-section-label">Operations</p>
            <div class="admin-stack">
                <div class="admin-card admin-ops-card is-blue">
                    <div class="admin-ops-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <circle cx="15" cy="15" r="15" fill="#A3BCCD" fill-opacity="0.2" />
                            <path d="M15.7 10V11.4286M15.7 18.5714V20M15.7 14.2857V15.7143M8 12.8571C8.55695 12.8571 9.0911 13.0829 9.48492 13.4848C9.87875 13.8866 10.1 14.4317 10.1 15C10.1 15.5683 9.87875 16.1134 9.48492 16.5152C9.0911 16.9171 8.55695 17.1429 8 17.1429V18.5714C8 18.9503 8.1475 19.3137 8.41005 19.5816C8.6726 19.8495 9.0287 20 9.4 20H20.6C20.9713 20 21.3274 19.8495 21.5899 19.5816C21.8525 19.3137 22 18.9503 22 18.5714V17.1429C21.443 17.1429 20.9089 16.9171 20.5151 16.5152C20.1212 16.1134 19.9 15.5683 19.9 15C19.9 14.4317 20.1212 13.8866 20.5151 13.4848C20.9089 13.0829 21.443 12.8571 22 12.8571V11.4286C22 11.0497 21.8525 10.6863 21.5899 10.4184C21.3274 10.1505 20.9713 10 20.6 10H9.4C9.0287 10 8.6726 10.1505 8.41005 10.4184C8.1475 10.6863 8 11.0497 8 11.4286V12.8571Z" stroke="#A3BCCD" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div>
                            <p class="admin-card-title mb-1">Open support tickets</p>
                            <p class="admin-card-meta muted mb-0">14 total · 6 waiting over 24h</p>
                        </div>
                        <span class="admin-badge blue">14 open</span>
                    </div>
                </div>
                <div class="admin-card admin-ops-card is-red">
                    <div class="admin-ops-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <circle cx="15" cy="15" r="15" fill="#FEE8E4" />
                            <path d="M14.2116 8.43738C14.5475 7.85421 15.4526 7.85421 15.7885 8.43738L21.8946 19.0468C22.2103 19.5958 21.7788 20.2577 21.1053 20.2577V19.4332L15.0001 8.82463L8.89484 19.4332V20.2577L8.771 20.2504C8.20993 20.1806 7.85855 19.6408 8.05494 19.1507L8.10553 19.0468L14.2116 8.43738ZM21.1053 19.4332V20.2577H8.89484V19.4332H21.1053Z" fill="#FFA899" />
                            <path d="M14.8675 12.477L15.031 15.9506L15.1941 12.4785C15.1951 12.4563 15.1916 12.4341 15.1837 12.4133C15.1759 12.3925 15.1639 12.3735 15.1485 12.3576C15.133 12.3416 15.1145 12.3289 15.094 12.3203C15.0735 12.3117 15.0515 12.3074 15.0293 12.3076C15.0074 12.3078 14.9859 12.3124 14.9658 12.3211C14.9458 12.3298 14.9277 12.3424 14.9126 12.3582C14.8976 12.374 14.8858 12.3926 14.8781 12.413C14.8703 12.4335 14.8667 12.4552 14.8675 12.477Z" stroke="#FFA899" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M15.0309 18.2244C14.9182 18.2244 14.8081 18.191 14.7145 18.1285C14.6209 18.0659 14.5479 17.977 14.5048 17.8729C14.4617 17.7689 14.4504 17.6544 14.4724 17.5439C14.4943 17.4335 14.5486 17.332 14.6282 17.2524C14.7078 17.1727 14.8093 17.1185 14.9198 17.0965C15.0302 17.0745 15.1447 17.0858 15.2488 17.1289C15.3528 17.172 15.4418 17.245 15.5043 17.3386C15.5669 17.4323 15.6003 17.5424 15.6003 17.655C15.6003 17.806 15.5403 17.9509 15.4335 18.0577C15.3267 18.1644 15.1819 18.2244 15.0309 18.2244Z" fill="#FFA899" />
                        </svg>
                        <div>
                            <p class="admin-card-title mb-1">Open disputes</p>
                            <p class="admin-card-meta muted mb-0">9 total · 4 unassigned</p>
                        </div>
                        <span class="admin-badge red">4 unassigned</span>
                    </div>
                </div>
                <div class="admin-card admin-ops-card is-orange">
                    <div class="admin-ops-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <circle cx="15" cy="15" r="15" fill="#FEF0DC" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1227 7.50964C10.2613 7.50964 10.3942 7.5647 10.4922 7.6627C10.5902 7.76071 10.6453 7.89363 10.6453 8.03222V8.78822L11.742 8.56874C12.9915 8.31914 14.2867 8.43824 15.4697 8.91155L15.6112 8.96799C16.5969 9.36221 17.6818 9.43584 18.7118 9.17842C18.9121 9.12833 19.1213 9.12456 19.3233 9.1674C19.5253 9.21024 19.7148 9.29856 19.8776 9.42565C20.0404 9.55273 20.172 9.71525 20.2625 9.90084C20.3531 10.0864 20.4001 10.2902 20.4001 10.4967V15.6299C20.4001 16.3183 19.9312 16.9189 19.263 17.0861L19.1139 17.123C17.7742 17.4578 16.3631 17.362 15.0809 16.8492C14.0862 16.4515 12.9973 16.3515 11.9468 16.5614L10.6453 16.822V21.9677C10.6453 22.1063 10.5902 22.2392 10.4922 22.3372C10.3942 22.4352 10.2613 22.4903 10.1227 22.4903C9.98408 22.4903 9.85116 22.4352 9.75316 22.3372C9.65516 22.2392 9.6001 22.1063 9.6001 21.9677V8.03222C9.6001 7.89363 9.65516 7.76071 9.75316 7.6627C9.85116 7.5647 9.98408 7.50964 10.1227 7.50964ZM10.6453 15.756L11.742 15.5365C12.9915 15.2869 14.2867 15.406 15.4697 15.8793C16.5477 16.3102 17.734 16.3907 18.8602 16.1092L19.01 16.0716C19.1085 16.0469 19.1959 15.9901 19.2584 15.91C19.3209 15.83 19.3549 15.7314 19.3549 15.6299V10.4967C19.355 10.4491 19.3442 10.402 19.3233 10.3592C19.3024 10.3163 19.2721 10.2788 19.2345 10.2494C19.1969 10.2201 19.1532 10.1996 19.1066 10.1897C19.06 10.1798 19.0117 10.1807 18.9654 10.1922C17.7223 10.5031 16.4129 10.4143 15.2231 9.9386L15.0809 9.88146C14.0862 9.48375 12.9973 9.38377 11.9468 9.5937L10.6453 9.85429V15.756Z" fill="#FFC97A" />
                        </svg>
                        <div>
                            <p class="admin-card-title mb-1">Flagged reviews</p>
                            <p class="admin-card-meta muted mb-0">7 awaiting moderation</p>
                        </div>
                        <span class="admin-badge orange">7 pending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-lower-grid">
        <div class="admin-lower-col">
            <p class="admin-section-label admin-live-label">
                <span class="admin-live-dot"></span>Live Bookings - 135 Active Now
            </p>
            <div class="admin-card admin-card-table">
                <div class="admin-table-wrap">
                    <table class="admin-live-table">
                        <thead>
                            <tr>
                                <th>Customer · Pet <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Business provider <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Service <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Time <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15686 0.5L3.32843 3.32843L0.500006 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Jane Doe</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="cat" /> Luna - Blue Russian</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Pawfect Salon</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London SE3</p>
                                </td>
                                <td>Full groom</td>
                                <td>
                                    <p class="admin-cell-title">14:45 - 15:45</p>
                                    <p class="admin-cell-sub">60 min session</p>
                                </td>
                                <td><span class="admin-status is-progress">In progress</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Tom Harris</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Max - Labrador</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Happy Tails</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London N1</p>
                                </td>
                                <td>Bath & Brush</td>
                                <td>
                                    <p class="admin-cell-title">15:00 - 15:45</p>
                                    <p class="admin-cell-sub">45 min session</p>
                                </td>
                                <td><span class="admin-status is-soon">Starting soon</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Sarah Bell</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Bella - Poodle</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Fur & Friends</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London SW1</p>
                                </td>
                                <td>Half-Day</td>
                                <td>
                                    <p class="admin-cell-title">15:30 - 18:30</p>
                                    <p class="admin-cell-sub">3 hr session</p>
                                </td>
                                <td><span class="admin-status is-scheduled">Scheduled</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Alex Rivera</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Coco - Cockapoo</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">City Space Hub</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London E1</p>
                                </td>
                                <td>Nail trim</td>
                                <td>
                                    <p class="admin-cell-title">16:00 - 16:30</p>
                                    <p class="admin-cell-sub">30 min session</p>
                                </td>
                                <td><span class="admin-status is-progress">In progress</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Priya Kapoor</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Milo - Beagle</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Paws & Co</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London W2</p>
                                </td>
                                <td>Full groom</td>
                                <td>
                                    <p class="admin-cell-title">16:15 - 17:15</p>
                                    <p class="admin-cell-sub">60 min session</p>
                                </td>
                                <td><span class="admin-status is-soon">Starting soon</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">James Turner</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Rocky - Bulldog</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Happy Tails</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London N1</p>
                                </td>
                                <td>Bath & Brush</td>
                                <td>
                                    <p class="admin-cell-title">17:00 - 17:45</p>
                                    <p class="admin-cell-sub">45 min session</p>
                                </td>
                                <td><span class="admin-status is-scheduled">Scheduled</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Emily Watson</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="dog" /> Daisy - Spaniel</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Pawfect Salon</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London SE3</p>
                                </td>
                                <td>Full groom</td>
                                <td>
                                    <p class="admin-cell-title">17:30 - 18:30</p>
                                    <p class="admin-cell-sub">60 min session</p>
                                </td>
                                <td><span class="admin-status is-scheduled">Scheduled</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="admin-cell-title">Chris Nolan</p>
                                    <p class="admin-cell-sub"><x-admin.icon-pet type="other" /> Pip - Rabbit</p>
                                </td>
                                <td>
                                    <p class="admin-cell-title">Fur & Friends</p>
                                    <p class="admin-cell-sub"><x-admin.icon-location /> London SW1</p>
                                </td>
                                <td>Day care</td>
                                <td>
                                    <p class="admin-cell-title">18:00 - 20:00</p>
                                    <p class="admin-cell-sub">2 hr session</p>
                                </td>
                                <td><span class="admin-status is-progress">In progress</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="admin-table-footer">
                    <div class="admin-status-legend">
                        <span><i class="dot is-progress"></i> In progress</span>
                        <span><i class="dot is-soon"></i> Starting within 1 hr</span>
                        <span><i class="dot is-scheduled"></i> Scheduled</span>
                    </div>
                    <p class="admin-table-count">Showing 1-8 of 135 live bookings</p>
                </div>
            </div>
        </div>

        <div class="admin-lower-col">
            <p class="admin-section-label">Recent Activity</p>
            <div class="admin-card admin-activity-card">
                <ul class="admin-activity-list">
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="danger" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Dispute raised on booking <a href="#">BK-08456</a> by <a href="#">John Doe</a> - groomer late, service incomplete</p>
                            <span class="admin-activity-time">2 mins ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="tick" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">New groomer verified and approved – <a href="#">Fluffy Friends</a> SE22 is now live on the platform</p>
                            <span class="admin-activity-time">10 mins ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="support" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Support ticket opened by <a href="#">Jane Doe</a> – booking confirmation email not received</p>
                            <span class="admin-activity-time">56 mins ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="flag" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Review flagged on <a href="#">Pawfect Salon</a> by a customer — awaiting admin moderation</p>
                            <span class="admin-activity-time">1 hr ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="users" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">14 new pet owners signed up today — 3 have already made a booking</p>
                            <span class="admin-activity-time">6 hrs ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="setting" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Platform settings updated by Ben — referral reward value changed from £5 to £6</p>
                            <span class="admin-activity-time">1 day ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg>
                        </span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="payout" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Weekly payout of £1,240 processed successfully to <a href="#">Pawfect Salon</a></p>
                            <span class="admin-activity-time">3 days ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg></span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="refund" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Refund of £35 initiated for booking <a href="#">BK-08640</a> — awaiting processor</p>
                            <span class="admin-activity-time">1 week ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg></span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="danger" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">Dispute raised on booking <a href="#">BK-08456</a> by <a href="#">John Doe</a> - groomer late, service incomplete</p>
                            <span class="admin-activity-time">2 months ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg></span>
                    </li>
                    <li class="admin-activity-item">
                        <span class="admin-activity-icon"><x-admin.icon-activity type="tick" /></span>
                        <div class="admin-activity-body">
                            <p class="admin-activity-text">New groomer verified and approved – <a href="#">Fluffy Friends</a> SE22 is now live on the platform</p>
                            <span class="admin-activity-time">6 months ago</span>
                        </div>
                        <span class="admin-activity-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="7" viewBox="0 0 10 7" fill="none">
                                <path d="M10 3.51224L6.78692 7L6.44307 6.61451C6.36791 6.53292 6.33784 6.45134 6.35287 6.36975C6.37166 6.28817 6.413 6.21066 6.47689 6.13724L8.0947 4.39336C8.18489 4.29545 8.26945 4.20775 8.34837 4.13024C8.42728 4.05274 8.50432 3.98135 8.57948 3.91608C8.39158 3.94464 8.19241 3.96707 7.98196 3.98339C7.77527 3.99971 7.56107 4.00787 7.33935 4.00787H0V3.01049H7.33935C7.56107 3.01049 7.77715 3.01865 7.9876 3.03496C8.19805 3.05128 8.39722 3.07372 8.58512 3.10227C8.50996 3.037 8.43104 2.96562 8.34837 2.88811C8.26945 2.81061 8.18489 2.7229 8.0947 2.625L6.46561 0.868881C6.39797 0.795454 6.35663 0.717948 6.3416 0.636363C6.32657 0.554778 6.35475 0.473193 6.42616 0.391608L6.77565 0L10 3.51224Z" fill="#3B3731" fill-opacity="0.2" />
                            </svg></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>