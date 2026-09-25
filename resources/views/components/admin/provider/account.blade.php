@props(['profile'])

@php
$sessions = $profile['sessions'] ?? [];
$blocked = $profile['blocked_customers'] ?? [];
$marketing = $profile['marketing'] ?? [];
$connectedAccounts = $profile['connected_accounts'] ?? [];
$connectedApps = $profile['connected_apps'] ?? [];
$twoFaEnabled = false;
foreach (($profile['verification']['items'] ?? []) as $item) {
if (($item['label'] ?? '') === '2FA Enabled') {
$twoFaEnabled = ! empty($item['ok']);
break;
}
}
@endphp

<div class="admin-co-overview admin-bp-account-tab">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Account</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    {{-- Sessions + Blocked customers --}}
    <div class="admin-co-split">
        <section
            class="admin-card admin-co-panel"
            x-data="{
                revoking: false,
                sessionCount: {{ count($sessions) }},
            }">
            <div x-show="!revoking">
                <x-admin.customer.section-header title="Device & Session info">
                    <button type="button" class="admin-co-link-btn" @click="revoking = true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M6 2H3.5C2.67157 2 2 2.67157 2 3.5V12.5C2 13.3284 2.67157 14 3.5 14H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                            <path d="M10.5 11L14 8L10.5 5" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M14 8H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                        </svg>
                        Revoke Access
                    </button>
                </x-admin.customer.section-header>
            </div>
            <div x-show="revoking" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">Revoke Access</h3>
                    <button type="button" class="admin-co-link-btn" @click="revoking = false">Cancel</button>
                </div>
                <p class="admin-co-panel-footnote" style="margin-top:0;margin-bottom:12px;">Select a session to revoke from this account.</p>
            </div>

            <ul class="admin-co-session-list">
                @foreach ($sessions as $session)
                <li class="admin-co-session-item">
                    <div class="admin-co-session-icon{{ !empty($session['warning']) ? ' has-warning' : '' }}" aria-hidden="true">
                        @if (($session['device'] ?? 'laptop') === 'phone')
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="20" viewBox="0 0 13 20" fill="none">
                            <rect x="11.75" y="0.75" width="18.5" height="11" rx="1.25" transform="rotate(90 11.75 0.75)" stroke="#3B3731" stroke-width="1.5" />
                            <path d="M4.72705 16.6362L7.72705 16.6362" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        @elseif (($session['device'] ?? '') === 'desktop')
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="16" viewBox="0 0 21 16" fill="none">
                            <rect x="0.75" y="0.75" width="18.8636" height="11.2273" rx="1.25" stroke="#3B3731" stroke-width="1.5" />
                            <path d="M7 15L13 15" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                            <rect x="0.75" y="0.75" width="14.5" height="9" rx="1.5" stroke="#3B3731" stroke-width="1.2" />
                            <path d="M5 11.25H11" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                        </svg>
                        @endif
                        @if (!empty($session['warning']))
                        <span class="admin-co-session-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
                                <path d="M9.53101 0.535238C9.11998 -0.178413 8.01236 -0.178413 7.60133 0.535238L0.129134 13.5183L0.0672303 13.6453C-0.173102 14.2451 0.256901 14.9057 0.943487 14.9911L1.09504 15H16.0373C16.8614 15 17.3895 14.1901 17.0032 13.5183L9.53101 0.535238Z" fill="#FFC97A" />
                                <path d="M8.40419 5.47978L8.60418 9.73045L8.80382 5.48152C8.80505 5.45436 8.80073 5.42723 8.79113 5.40179C8.78152 5.37635 8.76683 5.35314 8.74795 5.33358C8.72906 5.31401 8.70639 5.2985 8.68131 5.28799C8.65623 5.27749 8.62928 5.27221 8.60209 5.27247C8.57537 5.27273 8.54898 5.27834 8.52447 5.28897C8.49996 5.2996 8.47783 5.31503 8.45938 5.33436C8.44094 5.35368 8.42655 5.3765 8.41707 5.40148C8.40759 5.42646 8.40321 5.45308 8.40419 5.47978Z" fill="#3B3731" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.4707 11.1465C8.60269 11.1202 8.7399 11.1341 8.86426 11.1855C8.98873 11.2371 9.09605 11.3245 9.1709 11.4365C9.24567 11.5485 9.28516 11.6808 9.28516 11.8154C9.28504 11.9959 9.21359 12.1692 9.08594 12.2969C8.95829 12.4245 8.785 12.496 8.60449 12.4961C8.46984 12.4961 8.33757 12.4566 8.22559 12.3818C8.11356 12.307 8.02617 12.1997 7.97461 12.0752C7.9232 11.9508 7.90929 11.8136 7.93555 11.6816C7.96188 11.5496 8.02689 11.4282 8.12207 11.333C8.21725 11.2378 8.3387 11.1728 8.4707 11.1465Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                            </svg>
                        </span>
                        @endif
                    </div>
                    <div class="admin-co-session-body">
                        <p class="admin-co-session-title">{{ $session['title'] }}</p>
                        <p class="admin-co-session-meta">{{ $session['location'] }} · {{ $session['ip'] }}</p>
                        <p class="admin-co-session-meta">{{ $session['time'] }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            <p class="admin-co-panel-footnote">
                {{ count($sessions) }} active sessions · 2FA {{ $twoFaEnabled ? 'enabled' : 'not enabled' }}.
            </p>
        </section>

        <section
            class="admin-card admin-co-panel"
            x-data="{
                blockedOpen: false,
                unblockOpen: false,
                blockedSearch: '',
                blockedList: @js($blocked),
                unblockIndex: -1,
                unblockName: '',
                unblockId: '',
                unblockEmail: '',
                unblockAvatar: '',
            }"
            x-init="
                const syncModalLock = () => {
                    const open = blockedOpen || unblockOpen;
                    if (open) {
                        if (!document.body.classList.contains('admin-co-modal-lock')) {
                            document.body.dataset.adminCoScrollY = String(window.scrollY);
                            document.body.style.top = '-' + window.scrollY + 'px';
                            document.body.classList.add('admin-co-modal-lock');
                        }
                    } else if (document.body.classList.contains('admin-co-modal-lock')) {
                        const y = parseInt(document.body.dataset.adminCoScrollY || '0', 10);
                        document.body.classList.remove('admin-co-modal-lock');
                        document.body.style.top = '';
                        delete document.body.dataset.adminCoScrollY;
                        window.scrollTo(0, y);
                    }
                };
                $watch('blockedOpen', () => $nextTick(() => syncModalLock()));
                $watch('unblockOpen', () => $nextTick(() => syncModalLock()));
            ">
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title">
                    Blocked Customers (<span x-text="blockedList.length"></span> blocked)
                </h3>
                <div class="admin-co-section-action-wrap">
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        @click="blockedOpen = true; unblockOpen = false; blockedSearch = ''">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                        </svg>
                        View All
                    </button>
                </div>
            </div>
            <ul class="admin-co-blocked-list">
                <template x-for="(customer, index) in blockedList.slice(0, 4)" :key="customer.user_id + '-' + index">
                    <li class="admin-co-blocked-item">
                        <img :src="customer.avatar" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                        <div class="admin-co-blocked-body">
                            <p class="admin-co-blocked-name">
                                <span x-text="customer.name"></span>
                                <span class="admin-co-blocked-sep">·</span>
                                <span class="admin-bp-blocked-id" x-text="customer.user_id"></span>
                            </p>
                        </div>
                    </li>
                </template>
            </ul>
            <p class="admin-co-panel-footnote">Blocked customers cannot book with this provider.</p>

            <template x-teleport="body">
                <div
                    class="admin-co-modal-backdrop"
                    :class="{ 'is-open': blockedOpen && !unblockOpen }"
                    @click.self="blockedOpen = false">
                    <div class="admin-co-modal admin-co-blocked-modal" role="dialog" aria-modal="true" @click.stop>
                        <div class="admin-co-modal-head admin-co-blocked-modal-head">
                            <div>
                                <div class="admin-co-modal-title-row">
                                    <span class="admin-co-blocked-modal-icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect width="24" height="24" rx="5" fill="#FF6E6E" fill-opacity="0.1" />
                                            <path d="M11.998 5.5C12.9002 5.50005 13.7422 5.67134 14.5293 6.01074H14.5303C15.3264 6.35387 16.0138 6.8175 16.5977 7.40039C17.1815 7.98328 17.6457 8.67057 17.9893 9.4668C18.3287 10.2535 18.5 11.0957 18.5 11.998C18.5 12.9004 18.3293 13.7431 17.9902 14.5303C17.6472 15.3268 17.1834 16.0144 16.5996 16.5977C16.0155 17.1813 15.328 17.6451 14.5332 17.9893C13.7486 18.3289 12.9067 18.5004 12.0029 18.5C11.0986 18.4995 10.2554 18.3287 9.46973 17.9902H9.4707C8.67447 17.6466 7.98631 17.183 7.40234 16.5996C6.81832 16.0161 6.35433 15.3284 6.01074 14.5332C5.67138 13.7477 5.5 12.9055 5.5 12.002C5.50005 11.0986 5.67139 10.2566 6.01074 9.4707V9.46973C6.35393 8.67346 6.81695 7.98527 7.39941 7.40137C7.98166 6.81772 8.66918 6.35431 9.46582 6.01074C10.2531 5.67124 11.0957 5.5 11.998 5.5Z" stroke="#FE6F56" />
                                            <path d="M17 16L7 8" stroke="#FE6F56" />
                                        </svg>
                                    </span>
                                    <div>
                                        <h3 class="admin-co-modal-title">Blocked customers</h3>
                                        <p class="admin-co-modal-sub">
                                            {{ $profile['name'] }} · <span x-text="blockedList.length"></span> customers blocked
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="admin-co-modal-close" @click="blockedOpen = false" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8" />
                                    <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>

                        <div class="admin-co-blocked-modal-body">
                            <div class="admin-co-blocked-search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                    <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#B7B7B7" />
                                </svg>
                                <input
                                    type="text"
                                    placeholder="Search by customer name..."
                                    x-model="blockedSearch">
                            </div>

                            <ul class="admin-co-blocked-modal-list">
                                <template x-for="(customer, index) in blockedList" :key="customer.user_id + '-' + index">
                                    <li
                                        class="admin-co-blocked-modal-row"
                                        x-show="
                                            !blockedSearch
                                            || customer.name.toLowerCase().includes(blockedSearch.toLowerCase())
                                            || (customer.user_id && customer.user_id.toLowerCase().includes(blockedSearch.toLowerCase()))
                                        "
                                        x-cloak>
                                        <img :src="customer.avatar" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                                        <div class="admin-co-blocked-body">
                                            <p class="admin-co-blocked-name">
                                                <span x-text="customer.name"></span>
                                                <span class="admin-co-blocked-sep">·</span>
                                                <span class="admin-bp-blocked-id" x-text="customer.user_id"></span>
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            class="admin-co-form-btn is-unblock"
                                            @click="
                                                unblockIndex = index;
                                                unblockName = customer.name;
                                                unblockId = customer.user_id;
                                                unblockEmail = customer.email || '';
                                                unblockAvatar = customer.avatar;
                                                unblockOpen = true;
                                            ">Unblock</button>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="admin-co-blocked-modal-foot">
                            <p class="admin-co-panel-footnote mb-0">Blocked customers cannot book with this provider</p>
                            <button type="button" class="admin-co-form-btn is-cancel" @click="blockedOpen = false">Cancel</button>
                        </div>
                    </div>
                </div>
            </template>

            <template x-teleport="body">
                <div
                    class="admin-co-modal-backdrop is-confirm-layer"
                    :class="{ 'is-open': unblockOpen }"
                    @click.self="unblockOpen = false">
                    <div class="admin-co-modal admin-co-unblock-modal" role="dialog" aria-modal="true" @click.stop>
                        <div class="admin-co-modal-head admin-co-blocked-modal-head">
                            <div>
                                <h3 class="admin-co-modal-title">Unblock customer</h3>
                                <p class="admin-co-modal-sub">
                                    <span x-text="unblockName"></span> · <span x-text="unblockId"></span>
                                </p>
                            </div>
                            <button type="button" class="admin-co-modal-close" @click="unblockOpen = false" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8" />
                                    <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>

                        <div class="admin-co-blocked-modal-body">
                            <p class="admin-co-unblock-copy">
                                You are about to unblock the following customer for {{ $profile['name'] }}. The customer will be able to book with them immediately.
                            </p>

                            <div class="admin-co-unblock-card">
                                <img :src="unblockAvatar" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                                <div class="admin-co-blocked-body">
                                    <p class="admin-co-blocked-name">
                                        <span x-text="unblockName"></span>
                                        <span class="admin-co-blocked-sep">·</span>
                                        <span class="admin-bp-blocked-id" x-text="unblockId"></span>
                                    </p>
                                    <p class="admin-co-blocked-sub" x-text="unblockEmail"></p>
                                </div>
                            </div>

                            <div class="admin-co-revoke-note admin-co-unblock-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#A6BBC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#A6BBC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p>The provider will be notified that this customer has been unblocked. This action will be logged under your name.</p>
                            </div>
                        </div>

                        <div class="admin-co-blocked-modal-foot is-confirm">
                            <button type="button" class="admin-co-form-btn is-cancel" @click="unblockOpen = false">Cancel - keep blocked</button>
                            <button
                                type="button"
                                class="admin-co-form-btn is-revoke"
                                @click="
                                    unblockIndex >= 0 && blockedList.splice(unblockIndex, 1);
                                    unblockOpen = false;
                                    unblockIndex = -1;
                                ">Yes, unblock customer</button>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    </div>

    {{-- Marketing + Verification --}}
    <div class="admin-co-split">
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Marketing Consent" />
            <ul class="admin-co-status-list">
                @foreach ($marketing as $item)
                <li class="admin-co-status-row">
                    <span>{{ $item['label'] }}</span>
                    <span class="admin-co-consent is-{{ $item['enabled'] ? 'on' : 'off' }}">
                        @if ($item['enabled'])
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                        </svg>
                        Enabled
                        @else
                        Not Enabled
                        @endif
                    </span>
                </li>
                @endforeach
            </ul>
        </section>

        <section
            class="admin-card admin-co-panel"
            x-data="{
                editingVerify: false,
                email: 'verified',
                phone: 'verified',
                twofa: 'not_verified',
                login: 'Google',
                saved_email: 'verified',
                saved_phone: 'verified',
                saved_twofa: 'not_verified',
                saved_login: 'Google',
                profile_label: '{{ ($profile['verification']['profile_complete'] ?? 70) }}% Completion',
            }">
            <div x-show="!editingVerify" x-cloak>
                <x-admin.customer.section-header title="Verification Status">
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        @click="
                            email = saved_email;
                            phone = saved_phone;
                            twofa = saved_twofa;
                            login = saved_login;
                            editingVerify = true;
                        ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Override Status
                    </button>
                </x-admin.customer.section-header>
                <ul class="admin-co-status-list">
                    <li class="admin-co-status-row">
                        <span>Email Verified</span>
                        <span class="admin-co-verify-ok" x-show="saved_email === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_email !== 'verified'" x-cloak>Not verified</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Phone Verified</span>
                        <span class="admin-co-verify-ok" x-show="saved_phone === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_phone !== 'verified'" x-cloak>Not verified</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>2FA Enabled</span>
                        <span class="admin-co-verify-ok" x-show="saved_twofa === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_twofa !== 'verified'" x-cloak>Not Enabled</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Connected Login</span>
                        <span class="admin-co-verify-text" x-text="saved_login"></span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Profile Complete</span>
                        <span class="admin-co-verify-progress" x-text="profile_label"></span>
                    </li>
                </ul>
            </div>

            <div x-show="editingVerify" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">Verification Status</h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="editingVerify = false">Cancel</button>
                        <button
                            type="button"
                            class="admin-co-form-btn is-save"
                            @click="
                                saved_email = email;
                                saved_phone = phone;
                                saved_twofa = twofa;
                                saved_login = login;
                                editingVerify = false;
                            ">Save</button>
                    </div>
                </div>
                <ul class="admin-co-status-list admin-co-verify-edit-list">
                    <li class="admin-co-status-row">
                        <span>Email Verified</span>
                        <select class="admin-co-dd-trigger" x-model="email" style="width:auto;min-width:140px;">
                            <option value="verified">Verified</option>
                            <option value="not_verified">Not verified</option>
                            <option value="pending">Pending</option>
                        </select>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Phone Verified</span>
                        <select class="admin-co-dd-trigger" x-model="phone" style="width:auto;min-width:140px;">
                            <option value="verified">Verified</option>
                            <option value="not_verified">Not verified</option>
                            <option value="pending">Pending</option>
                        </select>
                    </li>
                    <li class="admin-co-status-row">
                        <span>2FA Enabled</span>
                        <select class="admin-co-dd-trigger" x-model="twofa" style="width:auto;min-width:140px;">
                            <option value="verified">Verified</option>
                            <option value="not_verified">Not Enabled</option>
                        </select>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Connected Login</span>
                        <select class="admin-co-dd-trigger" x-model="login" style="width:auto;min-width:140px;">
                            <option value="Google">Google</option>
                            <option value="Apple">Apple</option>
                            <option value="Facebook">Facebook</option>
                            <option value="None">None</option>
                        </select>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Profile Complete</span>
                        <span class="admin-co-verify-progress" x-text="profile_label"></span>
                    </li>
                </ul>
            </div>
        </section>
    </div>

    {{-- Connected Accounts & Apps --}}
    <section class="admin-card admin-co-panel admin-bp-connected-panel">
        <x-admin.customer.section-header title="Connected Accounts & Apps">
            <button type="button" class="admin-co-link-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                </svg>
                View All
            </button>
        </x-admin.customer.section-header>

        @if (count($connectedAccounts))
        <ul class="admin-bp-connected-list">
            @foreach ($connectedAccounts as $account)
            <li class="admin-bp-connected-item">
                <span class="admin-bp-connected-icon is-{{ $account['icon'] }}" aria-hidden="true">
                    @if (($account['icon'] ?? '') === 'facebook')
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                        <path d="M25.5103 0H1.48973C0.666977 0 0 0.666977 0 1.48973V25.5103C0 26.333 0.666977 27 1.48973 27H25.5103C26.333 27 27 26.333 27 25.5103V1.48973C27 0.666977 26.333 0 25.5103 0Z" fill="#3D5A98" />
                        <path d="M18.6275 26.9977V16.5422H22.1363L22.661 12.4677H18.6275V9.86691C18.6275 8.68744 18.9561 7.88212 20.6466 7.88212H22.8047V4.23193C21.7597 4.12321 20.7094 4.07142 19.6587 4.07679C16.5515 4.07679 14.4116 5.97033 14.4116 9.46311V12.4677H10.9028V16.5422H14.4116V26.9977H18.6275Z" fill="white" />
                    </svg>
                    @endif
                </span>
                <div class="admin-bp-connected-body">
                    <p class="admin-bp-connected-name">{{ $account['name'] }}</p>
                    <p class="admin-bp-connected-meta">{{ $account['detail'] }} · {{ $account['connected_at'] }}</p>
                </div>
                <span class="admin-bp-connected-status is-{{ $account['status'] }}">
                    <span class="admin-bp-connected-dot" aria-hidden="true"></span>
                    {{ $account['status'] === 'connected' ? 'Connected' : 'Disconnected' }}
                </span>
            </li>
            @endforeach
        </ul>
        @endif

        @if (count($connectedApps))
        <p class="admin-bp-connected-label">Connected Apps</p>
        <ul class="admin-bp-connected-list">
            @foreach ($connectedApps as $app)
            <li class="admin-bp-connected-item">
                <span class="admin-bp-connected-icon is-{{ $app['icon'] }}" aria-hidden="true">
                    @if (($app['icon'] ?? '') === 'calendar')
                    <img src="{{ asset('images/integrations/google-calendar.png') }}" alt="" width="28" height="28">
                    @elseif (($app['icon'] ?? '') === 'quickbooks')
                    <img src="{{ asset('images/integrations/quickbooks.png') }}" alt="" width="28" height="28">
                    @elseif (($app['icon'] ?? '') === 'zapier')
                    <img src="{{ asset('images/integrations/zapier.png') }}" alt="" width="28" height="28">
                    @endif
                </span>
                <div class="admin-bp-connected-body">
                    <p class="admin-bp-connected-name">{{ $app['name'] }}</p>
                    <p class="admin-bp-connected-meta">
                        {{ $app['detail'] }}
                        <span class="admin-bp-connected-sep">·</span>
                        {{ strtolower($app['connected_at']) }}
                    </p>
                </div>
                <span class="admin-bp-connected-status is-{{ $app['status'] }}">
                    @if ($app['status'] === 'connected')
                    <span class="admin-bp-connected-dot" aria-hidden="true"></span>
                    @endif
                    {{ $app['status'] === 'connected' ? 'Connected' : 'Disconnected' }}
                </span>
            </li>
            @endforeach
        </ul>
        @endif
    </section>
</div>