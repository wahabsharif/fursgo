@props(['profile'])

@php
$firstName = explode(' ', trim($profile['name'] ?? ''))[0] ?: 'Customer';
$referrals = $profile['referrals'] ?? [];
$balance = $referrals['balance'] ?? [];
$code = $referrals['code'] ?? [];
$rows = $referrals['rows'] ?? [];
$filterAll = $referrals['filter_all'] ?? count($rows);
$balanceTitle = $firstName . "'s credits balance";
$codeLabel = $firstName . "'s unique referral code";
@endphp

<div
    class="admin-co-referrals"
    x-data="{
        search: '',
        selectedIds: @js(collect($rows)->where('highlight', true)->pluck('id')->values()),
        copied: false,
        codeValue: @js($code['value'] ?? ''),
        allIds: @js(collect($rows)->pluck('id')->values()),
        selectedReferral: null,
        matchesRow(name, id, email, codeUsed) {
            const q = (this.search || '').trim().toLowerCase();
            if (!q) return true;
            return name.toLowerCase().includes(q)
                || id.toLowerCase().includes(q)
                || email.toLowerCase().includes(q)
                || (codeUsed || '').toLowerCase().includes(q);
        },
        isSelected(id) {
            return this.selectedIds.includes(id);
        },
        toggleOne(id) {
            if (this.isSelected(id)) {
                this.selectedIds = this.selectedIds.filter((x) => x !== id);
            } else {
                this.selectedIds.push(id);
            }
        },
        get allSelected() {
            return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
        },
        toggleAll() {
            this.selectedIds = this.allSelected ? [] : [...this.allIds];
        },
        openReferral(row) {
            this.selectedReferral = row;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            this.$dispatch('admin-referral-selected', { referral: row });
        },
        closeReferral() {
            this.selectedReferral = null;
            this.$dispatch('admin-referral-closed');
        },
        async copyCode() {
            try {
                await navigator.clipboard.writeText(this.codeValue);
                this.copied = true;
                setTimeout(() => this.copied = false, 1600);
            } catch (e) {
                this.copied = false;
            }
        },
    }"
    @admin-referral-close-request.window="closeReferral()">
    <div class="admin-co-ref-list" x-show="!selectedReferral" x-cloak>
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Referrals</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    <div class="admin-po-toolbar admin-co-ref-toolbar">
        <div class="admin-po-filters" role="tablist" aria-label="Referral filters">
            <button type="button" class="admin-po-filter is-all is-active">
                All ({{ $filterAll }})
            </button>
        </div>
        <div class="admin-po-tools">
            <label class="admin-po-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <circle cx="6.2" cy="6.2" r="4.7" stroke="#3B3731" stroke-width="1.2" />
                    <path d="M9.6 9.6L12.5 12.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                </svg>
                <input type="search" placeholder="Search..." x-model="search" />
            </label>
            <button type="button" class="admin-po-tool-btn">
                Sort by
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                    <path d="M1 1L5 5L9 1" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" class="admin-po-tool-btn filter-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                    <path d="M9.5 4.99974H3.48946M1.36789 4.99974H0.5M1.36789 4.99974C1.36789 4.70367 1.47963 4.41972 1.67852 4.21036C1.87741 4.001 2.14716 3.88339 2.42843 3.88339C2.70971 3.88339 2.97946 4.001 3.17835 4.21036C3.37724 4.41972 3.48897 4.70367 3.48897 4.99974C3.48897 5.29582 3.37724 5.57977 3.17835 5.78913C2.97946 5.99849 2.70971 6.1161 2.42843 6.1161C2.14716 6.1161 1.87741 5.99849 1.67852 5.78913C1.47963 5.57977 1.36789 5.29582 1.36789 4.99974ZM9.5 8.38313H6.70368M6.70368 8.38313C6.70368 8.67927 6.59167 8.96355 6.39274 9.17295C6.1938 9.38236 5.92399 9.5 5.64265 9.5C5.36138 9.5 5.09162 9.38187 4.89273 9.17251C4.69384 8.96316 4.58211 8.67921 4.58211 8.38313M6.70368 8.38313C6.70368 8.08698 6.59167 7.80323 6.39274 7.59382C6.1938 7.38441 5.92399 7.26677 5.64265 7.26677C5.36138 7.26677 5.09162 7.38439 4.89273 7.59375C4.69384 7.8031 4.58211 8.08705 4.58211 8.38313M4.58211 8.38313H0.5M9.5 1.61636H7.98946M5.86789 1.61636H0.5M5.86789 1.61636C5.86789 1.32028 5.97963 1.03633 6.17852 0.826974C6.37741 0.617616 6.64716 0.5 6.92843 0.5C7.0677 0.5 7.20561 0.528875 7.33428 0.584978C7.46295 0.64108 7.57987 0.72331 7.67835 0.826974C7.77683 0.930637 7.85495 1.0537 7.90824 1.18915C7.96154 1.32459 7.98897 1.46976 7.98897 1.61636C7.98897 1.76296 7.96154 1.90813 7.90824 2.04357C7.85495 2.17901 7.77683 2.30208 7.67835 2.40574C7.57987 2.50941 7.46295 2.59164 7.33428 2.64774C7.20561 2.70384 7.0677 2.73272 6.92843 2.73272C6.64716 2.73272 6.37741 2.6151 6.17852 2.40574C5.97963 2.19639 5.86789 1.91244 5.86789 1.61636Z" stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round" />
                </svg>
                Filter
            </button>
        </div>
    </div>

    <div class="admin-co-ref-grid">
        <section class="admin-card admin-co-panel admin-co-ref-balance">
            <x-admin.customer.section-header :title="$balanceTitle" />
            <div class="admin-co-ref-metrics">
                <div class="admin-co-ref-metric">
                    <p class="admin-co-ref-metric-value">{{ $balance['sent'] ?? '4' }}</p>
                    <p class="admin-co-ref-metric-label">Referrals sent</p>
                </div>
                <div class="admin-co-ref-metric">
                    <p class="admin-co-ref-metric-value">{{ $balance['booked'] ?? '3' }}</p>
                    <p class="admin-co-ref-metric-label">New user booked</p>
                </div>
                <div class="admin-co-ref-metric">
                    <p class="admin-co-ref-metric-value">{{ $balance['earned'] ?? '£18.00' }}</p>
                    <p class="admin-co-ref-metric-label">Total earned</p>
                </div>
                <div class="admin-co-ref-metric is-available">
                    <p class="admin-co-ref-metric-value">{{ $balance['available'] ?? '£12.00' }}</p>
                    <p class="admin-co-ref-metric-label">Credits available</p>
                </div>
            </div>
            <div class="admin-co-ref-meta">
                <div class="admin-co-details-row">
                    <dt>Pending credits</dt>
                    <dd>{{ $balance['pending'] ?? '£6.00' }}{{ ! empty($balance['pending_note']) ? ' - ' . $balance['pending_note'] : ' - referral not yet booked' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Expired credits</dt>
                    <dd>{{ $balance['expired'] ?? '£0.00' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Next expiry</dt>
                    <dd>{{ $balance['next_expiry'] ?? '28 Mar 2026' }}</dd>
                </div>
            </div>
        </section>

        <section class="admin-card admin-co-panel admin-co-ref-code">
            <x-admin.customer.section-header title="Referral code" />
            <div class="admin-co-ref-code-box">
                <div class="admin-co-ref-code-row">
                    <div>
                        <p class="admin-co-ref-code-label">{{ $codeLabel }}</p>
                        <p class="admin-co-ref-code-value" x-text="codeValue">{{ $code['value'] ?? 'JANE-XP26' }}</p>
                    </div>
                    <button type="button" class="admin-co-ref-copy" @click="copyCode()">
                        <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
                    </button>
                </div>
            </div>
            <dl class="admin-co-details">
                <div class="admin-co-details-row">
                    <dt>Code created</dt>
                    <dd>{{ $code['created'] ?? '02 Feb 2023' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Times used</dt>
                    <dd>{{ $code['times_used'] ?? '3' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Reward per referral</dt>
                    <dd>{{ $code['reward'] ?? '£5.00 credit' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Credit expiry</dt>
                    <dd>{{ $code['expiry'] ?? '6 months from issue' }}</dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Code status</dt>
                    <dd class="is-status-active">{{ $code['status'] ?? 'Active' }}</dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="admin-card admin-co-panel admin-co-ref-table-panel">
        <div class="admin-po-table-card admin-co-ref-table-card">
            <div class="table-responsive">
                <table class="admin-po-table admin-co-ref-table">
                    <thead>
                        <tr>
                            <th class="admin-po-check-col">
                                <input
                                    type="checkbox"
                                    class="admin-po-check"
                                    :checked="allSelected"
                                    @change="toggleAll()"
                                    aria-label="Select all referrals" />
                            </th>
                            <th>
                                <span class="admin-co-ref-th">
                                    Referred
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                        <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </th>
                            <th>
                                <span class="admin-co-ref-th">
                                    Code used
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                        <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </th>
                            <th>
                                <span class="admin-co-ref-th">
                                    Date signed up
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                        <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </th>
                            <th>
                                <span class="admin-co-ref-th">
                                    Credit issued
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                        <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </th>
                            <th>
                                <span class="admin-co-ref-th">
                                    Status
                                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                        <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                        <tr
                            class="{{ ! empty($row['highlight']) ? 'is-attention' : '' }} admin-co-ref-row"
                            :class="{ 'is-selected': isSelected(@js($row['id'])) }"
                            x-show="matchesRow(@js($row['name']), @js($row['id']), @js($row['email']), @js($row['code_used'] ?? ''))"
                            @click="openReferral(@js($row))"
                            @keydown.enter.prevent="openReferral(@js($row))"
                            @keydown.space.prevent="openReferral(@js($row))"
                            tabindex="0"
                            role="button"
                            x-cloak>
                            <td class="admin-po-check-col" @click.stop>
                                <input
                                    type="checkbox"
                                    class="admin-po-check"
                                    :checked="isSelected(@js($row['id']))"
                                    @change="toggleOne(@js($row['id']))"
                                    aria-label="Select {{ $row['name'] }}" />
                            </td>
                            <td>
                                <div class="admin-co-ref-user">
                                    <span class="admin-co-ref-user-name">
                                        {{ $row['name'] }} <span class="admin-co-ref-user-id">({{ $row['id'] }})</span>
                                    </span>
                                    <span class="admin-co-ref-user-email">{{ $row['email'] }}</span>
                                </div>
                            </td>
                            <td>{{ $row['code_used'] !== '' ? $row['code_used'] : '-' }}</td>
                            <td>{{ $row['signed_up'] }}</td>
                            <td>
                                <span class="admin-co-ref-credit">{{ $row['credit'] }}</span>
                            </td>
                            <td>
                                <span class="admin-po-status is-{{ $row['status'] }}">
                                    {{ $row['status_label'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="admin-po-table-footer admin-co-ref-footer">
            <p class="admin-table-count">SHOWING 1–{{ count($rows) }} OF {{ count($rows) }} REFERRALS</p>
        </div>
    </section>
    </div>{{-- /list --}}

    <x-admin.customer.referral-detail :profile="$profile" />
</div>