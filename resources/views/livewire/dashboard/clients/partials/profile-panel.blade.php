@php
    $profileWireTargets =
        $profileWireTargets ??
        'viewProfile, setProfileTab, setProfileSort, setProfilePetSort, loadMoreProfile, viewPetDetails, updateGroomerGuidanceNotes, openCompletedBookingModal, closeCompletedBookingModal, toggleReviewReply, closeReviewReply, submitReviewReply';
@endphp

@if ($selectedPetId && $this->selectedPet)
    <x-dashboard.clients.client-pet-medication-view :pet="$this->selectedPet" :medication="$this->selectedPetMedication" :vaccination-rows="$this->petVaccinationRows"
        :overdue-vaccination-count="$this->petOverdueVaccinationCount" />
@else
    @php
        $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
        $meta = $this->profileMeta;
        $tabCounts = $this->profileTabCounts;
        $activeTab = $this->profileActiveTab;
        $profileTabs = $isSpaceUser
            ? [
                'bookings' => 'Bookings',
                'upcoming' => 'Upcoming Bookings',
                'reviews' => 'Reviews',
                'payments' => 'Payments',
            ]
            : [
                'bookings' => 'Bookings',
                'upcoming' => 'Upcoming Bookings',
                'pets' => 'Pets',
                'reviews' => 'Reviews',
                'payments' => 'Payments',
            ];

        if (isset($profileTabs[$activeTab])) {
            $profileTabs =
                [$activeTab => $profileTabs[$activeTab]] + array_diff_key($profileTabs, [$activeTab => true]);
        }
    @endphp
    <section class="client-profile-wrapper {{ $isSpaceUser ? 'is-space-user' : '' }}" aria-label="Client profile">
        <div class="client-profile-back-block">
            <button type="button" class="client-profile-back" @click="closeProfileView()">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none"
                    aria-hidden="true">
                    <path
                        d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                        fill="black" />
                </svg>
                Clients
            </button>
            <div class="client-profile-back-loader" :class="{ 'is-visible': profileLoading }"
                wire:loading.class="is-visible" wire:target="{{ $profileWireTargets }}" aria-hidden="true">
                <div class="active-section-loading-bar">
                    <span class="active-section-loading-bar__sweep"></span>
                </div>
            </div>
        </div>

        <article class="client-profile-card" x-ref="profileCard" x-data="{
            openMenu: false,
            blocked: @entangle('profileIsBlocked').live,
            menuStyle: '',
            repositionMenu() {
                const card = this.$refs.profileCard.getBoundingClientRect();
                this.menuStyle = `top:calc(${card.top}px + 3rem);left:calc(${card.right}px + 4.5rem - 130px);`;
            },
            toggleMenu() {
                if (!this.openMenu) {
                    this.$nextTick(() => this.repositionMenu());
                }
                this.openMenu = !this.openMenu;
            },
            toggleBlock() {
                this.blocked = !this.blocked;
                this.$wire.toggleClientBlock();
            }
        }"
            :class="{ 'is-blocked': blocked }" x-effect="if (openMenu) repositionMenu()"
            @keydown.escape.window="openMenu = false" @resize.window="if (openMenu) repositionMenu()"
            @scroll.window="if (openMenu) repositionMenu()"
            @click.window="if (openMenu && $refs.moreBtn && !$refs.moreBtn.contains($event.target) && $refs.moreMenu && !$refs.moreMenu.contains($event.target)) { openMenu = false }">
            <span class="client-profile-accent" :class="{ 'is-blocked': blocked }" aria-hidden="true"></span>

            <div class="client-profile-avatar-wrap">
                @if ($meta['avatar_url'])
                    <img src="{{ $meta['avatar_url'] }}" alt="{{ $meta['name'] }}" class="client-profile-avatar-img" />
                @else
                    <span class="client-profile-avatar-fallback">{{ $meta['initials'] }}</span>
                @endif
            </div>

            <div class="client-profile-card-main">
                <div class="client-profile-top">
                    <div class="client-profile-info">
                        <div class="client-profile-header-row">
                            <span class="client-profile-status"
                                :class="blocked ? 'is-blocked' : (@js($meta['is_active']) ? 'is-active' : 'is-inactive')">
                                <span
                                    x-text="blocked ? 'Blocked' : (@js($meta['is_active']) ? 'Active' : 'Inactive')"></span>
                            </span>

                            <div class="client-profile-more-wrap">
                                <button type="button" class="client-profile-more-btn" x-ref="moreBtn"
                                    @click.stop="toggleMenu()" aria-label="More options"
                                    :aria-expanded="openMenu.toString()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="5"
                                        viewBox="0 0 25 5" fill="none" aria-hidden="true">
                                        <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                                        <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                                        <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="client-profile-body-row">
                            <div class="client-profile-details">
                                <div class="client-profile-name-row">
                                    <h3 class="client-profile-name">{{ $meta['name'] }}</h3>
                                    @if ($meta['is_verified'])
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 18 18" fill="none" aria-label="Verified">
                                            <circle cx="9" cy="9" r="9" fill="#9FC7E4" />
                                            <path d="M5.5 9.2L7.8 11.5L12.5 6.8" stroke="white" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="client-profile-meta-row">
                                    <span style="display:flex;gap:5px">
                                        Location
                                        <span style="color: #3B3731;">{{ $meta['location'] }}</span>
                                    </span>
                                    <span style="display:flex;gap:5px">Client since
                                        <span style="color: #3B3731;">{{ $meta['client_since'] }}</span></span>
                                    @unless ($isSpaceUser)
                                        <span style="display:flex;gap:5px">Pets<span
                                                style="color: #3B3731;">{{ $meta['pets_label'] }}</span></span>
                                    @endunless
                                </div>
                            </div>

                            <button type="button" class="client-profile-message-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="18"
                                    viewBox="0 0 19 18" fill="none">
                                    <path
                                        d="M9.21191 0.75C13.9709 0.750052 17.6738 4.15879 17.6738 8.18262C17.6738 12.2065 13.9709 15.6152 9.21191 15.6152H9.20996C8.36471 15.6171 7.52277 15.5061 6.70605 15.2842L6.42578 15.208L6.16699 15.3408C5.69627 15.5825 4.70064 16.0237 3.09375 16.4053L2.36816 16.5645C2.36428 16.5652 2.36033 16.5656 2.35645 16.5664C2.36139 16.552 2.36717 16.5379 2.37207 16.5234L2.37695 16.5088L2.38086 16.4951L2.38379 16.4824C2.6793 15.6051 2.92377 14.5916 3.01465 13.6309L3.04785 13.2832L2.80371 13.0342C1.51469 11.7224 0.75 10.0242 0.75 8.18262C0.750027 4.15876 4.45291 0.75 9.21191 0.75Z"
                                        stroke="#94BEDB" stroke-width="1.5" />
                                </svg>
                                Message
                            </button>
                        </div>
                    </div>
                </div>

                <div class="client-profile-stats">
                    <button type="button" class="client-profile-stat" wire:click="setProfileTab('upcoming')">
                        <span class="client-profile-stat-label">Upcoming</span>
                        <span class="client-profile-stat-value"><span
                                class="client-profile-stat-figure">{{ $meta['upcoming_count'] }}</span> /
                            bookings</span>
                        <span class="client-profile-stat-chevron" aria-hidden="true">›</span>
                    </button>
                    <button type="button" class="client-profile-stat" wire:click="setProfileTab('bookings')">
                        <span class="client-profile-stat-label">Completed</span>
                        <span class="client-profile-stat-value"><span
                                class="client-profile-stat-figure">{{ $meta['completed_count'] }}</span> /
                            bookings</span>
                        <span class="client-profile-stat-chevron" aria-hidden="true">›</span>
                    </button>
                    <button type="button" class="client-profile-stat" wire:click="setProfileTab('payments')">
                        <span class="client-profile-stat-label">Total Paid</span>
                        <span class="client-profile-stat-value"><span
                                class="client-profile-stat-figure">£{{ number_format($meta['total_paid'], 2) }}</span></span>
                        <span class="client-profile-stat-chevron" aria-hidden="true">›</span>
                    </button>
                    <button type="button" class="client-profile-stat" wire:click="setProfileTab('reviews')">
                        <span class="client-profile-stat-label">Avg Rating</span>
                        <span class="client-profile-stat-value">
                            @if ($meta['avg_rating'])
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M8.75651 0.943537C9.14791 -0.314515 10.8521 -0.314511 11.2435 0.943541L12.7078 5.65027C12.8829 6.21288 13.3849 6.5938 13.9513 6.5938H18.69C19.9566 6.5938 20.4832 8.2865 19.4585 9.06402L15.6249 11.9729C15.1666 12.3207 14.9748 12.937 15.1499 13.4996L16.6142 18.2063C17.0056 19.4644 15.6269 20.5105 14.6022 19.733L10.7685 16.8241C10.3103 16.4764 9.68974 16.4764 9.23148 16.8241L5.3978 19.733C4.37311 20.5105 2.99439 19.4644 3.38579 18.2063L4.85012 13.4996C5.02516 12.937 4.83341 12.3207 4.37515 11.9729L0.541471 9.06402C-0.483225 8.2865 0.0434023 6.5938 1.31 6.5938H6.04868C6.61512 6.5938 7.11714 6.21288 7.29217 5.65027L8.75651 0.943537Z"
                                            fill="#FFC97A" />
                                    </svg>
                                    <span
                                        class="client-profile-stat-figure">{{ number_format($meta['avg_rating'], 0) }}</span></span>
                                / rating
                            @else
                                <span class="client-profile-stat-figure">—</span> / rating
                            @endif
                        </span>
                        <span class="client-profile-stat-chevron" aria-hidden="true">›</span>
                    </button>
                </div>
            </div>

            <template x-teleport="body">
                <div class="client-profile-more-menu" x-cloak x-show="openMenu" x-ref="moreMenu"
                    wire:loading.class="is-loading" wire:target="toggleClientBlock" :style="menuStyle"
                    x-transition.opacity.duration.100ms>
                    <button type="button" class="client-profile-more-item"
                        :class="blocked ? 'is-activate' : 'is-block'" @click="toggleBlock()"
                        wire:loading.attr="disabled" wire:loading.class="is-loading" wire:target="toggleClientBlock">
                        <span wire:loading.remove wire:target="toggleClientBlock"
                            x-text="blocked ? 'Activate' : 'Block'"></span>
                        <span class="client-profile-more-item-loading" wire:loading.inline-flex
                            wire:target="toggleClientBlock">
                            <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                        </span>
                        <template x-if="blocked">
                            <svg wire:loading.remove wire:target="toggleClientBlock"
                                xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                fill="none" aria-hidden="true">
                                <circle cx="7.5" cy="7.5" r="7.125" stroke="#3B3731"
                                    stroke-width="0.75" />
                                <path d="M4.5 7.6L6.7 9.8L10.5 5.5" stroke="#3B3731" stroke-width="0.75"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </template>
                        <template x-if="!blocked">
                            <svg wire:loading.remove wire:target="toggleClientBlock"
                                xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
                                fill="none" aria-hidden="true">
                                <path
                                    d="M7.05664 0.375C7.86761 0.375004 8.63276 0.509461 9.35449 0.777344L9.66113 0.900391C10.4786 1.25264 11.1862 1.72915 11.7871 2.3291C12.3879 2.92892 12.8651 3.63584 13.2178 4.45312C13.5673 5.26324 13.7432 6.12989 13.7432 7.05664C13.7432 7.98354 13.5669 8.85036 13.2178 9.66113C12.8656 10.4789 12.3889 11.1868 11.7881 11.7871C11.187 12.3876 10.4798 12.8646 9.66406 13.2178C8.8559 13.5676 7.98971 13.7437 7.06152 13.7432C6.13461 13.7432 5.26684 13.5669 4.45605 13.2178C3.63899 12.8651 2.93181 12.3883 2.33105 11.7881C1.73017 11.1877 1.25306 10.4812 0.900391 9.66504C0.550891 8.85611 0.375027 7.98939 0.375 7.06152C0.375 6.24972 0.509614 5.4845 0.777344 4.76367L0.900391 4.45703C1.25262 3.63963 1.72876 2.93196 2.32812 2.33105C2.92737 1.73033 3.63433 1.25309 4.45215 0.900391C5.26288 0.550756 6.12982 0.375 7.05664 0.375Z"
                                    stroke="#3B3731" stroke-width="0.75" />
                                <line x1="1.92621" y1="2.22629" x2="11.8918" y2="12.1919" stroke="#3B3731"
                                    stroke-width="0.75" />
                            </svg>
                        </template>
                    </button>
                </div>
            </template>
        </article>

        <div class="client-profile-toolbar">
            <div class="client-profile-tabs">
                @foreach ($profileTabs as $tabKey => $tabLabel)
                    <button type="button" wire:click="setProfileTab('{{ $tabKey }}')"
                        class="client-profile-tab {{ $profileActiveTab === $tabKey ? 'is-active' : 'is-muted' }}">
                        {{ $tabLabel }}
                        @if ($tabKey === 'upcoming' && ($tabCounts['upcoming'] ?? 0) > 0)
                            ({{ $tabCounts['upcoming'] }})
                        @elseif ($tabKey === 'bookings' && ($tabCounts['bookings'] ?? 0) > 0)
                            ({{ $tabCounts['bookings'] }})
                        @elseif (!$isSpaceUser && $tabKey === 'pets' && ($tabCounts['pets'] ?? 0) > 0)
                            ({{ $tabCounts['pets'] }})
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="client-profile-sort" x-show="$wire.profileActiveTab === 'pets'" x-cloak
                x-transition:enter="client-profile-toolbar-enter"
                x-transition:enter-start="client-profile-toolbar-enter-start"
                x-transition:enter-end="client-profile-toolbar-enter-end"
                x-transition:leave="client-profile-toolbar-leave"
                x-transition:leave-start="client-profile-toolbar-leave-start"
                x-transition:leave-end="client-profile-toolbar-leave-end" x-data="{
                    open: false,
                    menuRight: 0,
                    menuTop: 0,
                    menuWidth: 220,
                    repositionMenu() {
                        const rect = $refs.sortBtn.getBoundingClientRect();
                        this.menuRight = Math.max(8, rect.right - this.menuWidth);
                        this.menuTop = rect.bottom + 8;
                    },
                    toggleMenu() {
                        if (!this.open) {
                            this.repositionMenu();
                        }
                        this.open = !this.open;
                    }
                }"
                @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                @scroll.window="if (open) repositionMenu()"
                @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                <div class="sort-dropdown">
                    <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
                        aria-label="Sort pets" :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                            fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <template x-teleport="body">
                        <div class="sort-menu client-profile-sort-menu" x-cloak x-show="open" x-ref="sortMenu"
                            x-transition.opacity.duration.100ms
                            :style="`position: fixed; left: ${menuRight}px; top: ${menuTop}px; z-index: 99999;`">
                            @foreach ([
        'name_asc' => 'Name (A–Z)',
        'name_desc' => 'Name (Z–A)',
        'type_asc' => 'Pet Type (A–Z)',
        'type_desc' => 'Pet Type (Z–A)',
        'weight_high' => 'Heaviest',
        'weight_low' => 'Lightest',
    ] as $profilePetSortKey => $profilePetSortLabel)
                                <button type="button" class="sort-options"
                                    :class="{ 'is-active': @js($profilePetSort) === '{{ $profilePetSortKey }}' }"
                                    wire:click="setProfilePetSort('{{ $profilePetSortKey }}')" @click="open = false">
                                    <span>{{ $profilePetSortLabel }}</span>
                                    <span class="sort-indicator"></span>
                                </button>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>

            <div class="client-profile-sort"
                x-show="['upcoming', 'bookings', 'payments'].includes($wire.profileActiveTab)" x-cloak
                x-transition:enter="client-profile-toolbar-enter"
                x-transition:enter-start="client-profile-toolbar-enter-start"
                x-transition:enter-end="client-profile-toolbar-enter-end"
                x-transition:leave="client-profile-toolbar-leave"
                x-transition:leave-start="client-profile-toolbar-leave-start"
                x-transition:leave-end="client-profile-toolbar-leave-end" x-data="{
                    open: false,
                    menuRight: 0,
                    menuTop: 0,
                    menuWidth: 220,
                    repositionMenu() {
                        const rect = $refs.sortBtn.getBoundingClientRect();
                        this.menuRight = Math.max(8, rect.right - this.menuWidth);
                        this.menuTop = rect.bottom + 8;
                    },
                    toggleMenu() {
                        if (!this.open) {
                            this.repositionMenu();
                        }
                        this.open = !this.open;
                    }
                }"
                @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                @scroll.window="if (open) repositionMenu()"
                @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                <div class="sort-dropdown">
                    <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
                        aria-label="Sort bookings" :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                            fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <template x-teleport="body">
                        <div class="sort-menu client-profile-sort-menu" x-cloak x-show="open" x-ref="sortMenu"
                            x-transition.opacity.duration.100ms
                            :style="`position: fixed; left: ${menuRight}px; top: ${menuTop}px; z-index: 99999;`">
                            @foreach ([
        'date_asc' => 'Date (Earliest)',
        'date_desc' => 'Date (Latest)',
        'amount_high' => 'Highest Paid',
        'amount_low' => 'Lowest Paid',
    ] as $profileSortKey => $profileSortLabel)
                                <button type="button" class="sort-options"
                                    :class="{ 'is-active': @js($profileSort) === '{{ $profileSortKey }}' }"
                                    wire:click="setProfileSort('{{ $profileSortKey }}')" @click="open = false">
                                    <span>{{ $profileSortLabel }}</span>
                                    <span class="sort-indicator"></span>
                                </button>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div wire:key="client-profile-tab-{{ $profileActiveTab }}" class="client-profile-tab-panel">
            @if ($profileActiveTab === 'pets')
                <x-dashboard.clients.client-pet-view :pets="$this->profilePets" />
            @elseif ($profileActiveTab === 'reviews')
                <x-dashboard.clients.client-reviews-view :reviews="$this->profileVisibleTabReviews" :client-name="$meta['name']" :open-reply-id="$openReviewReplyId" />

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreProfile">
                                <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @elseif (in_array($profileActiveTab, ['bookings', 'payments'], true))
                <x-dashboard.clients.client-bookings-view :bookings="$this->profileVisibleTabBookings" :is-space-user="$isSpaceUser" />

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreProfile">
                                <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @elseif ($profileActiveTab === 'upcoming')
                <div class="client-profile-table-shell">
                    <table class="client-profile-table">
                        <thead>
                            <tr>
                                @if ($isSpaceUser)
                                    <th style="width: 15rem">Booking ID</th>
                                    <th style="width: 15rem">Service Type</th>
                                    <th style="width: 15rem">Space</th>
                                    <th style="width: 15rem">Booking Details</th>
                                    <th style="width: 15rem">Staff</th>
                                    <th style="width: 15rem" class="client-profile-action-col">Action</th>
                                @else
                                    <th style="width: 15rem">Booking ID</th>
                                    <th style="width: 15rem">Booking Details</th>
                                    <th style="width: 15rem">Pet</th>
                                    <th style="width: 15rem">Service Type</th>
                                    <th style="width: 15rem">Location</th>
                                    <th style="width: 15rem">Staff</th>
                                    <th style="width: 15rem" class="client-profile-action-col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->profileVisibleTabBookings as $booking)
                                @php
                                    $petNames = $booking->pets->pluck('name')->filter()->values()->all();
                                    $petTypes = $booking->pets->pluck('pet_type')->filter()->unique()->values()->all();
                                    $petName = $petNames[0] ?? 'N/A';
                                    $petType = $petTypes[0] ?? null;
                                    $bookingDate = optional($booking->date)->format('d/m/y');
                                    $bookingTime = $this->formatProfileBookingTimeDisplay(
                                        (string) $booking->time,
                                        $isSpaceUser,
                                    );
                                    $locationLabel = $this->formatProfileLocationLabel($booking->visit_type ?? null);
                                    $spaceLabel = $this->formatProfileSpaceLabel($booking->visit_type ?? null);
                                @endphp
                                <tr wire:key="client-booking-{{ $booking->id }}">
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    @if ($isSpaceUser)
                                        <td class="client-profile-service-type">{{ $booking->service }}</td>
                                        <td><span>{{ $spaceLabel }}</span></td>
                                        <td>
                                            <div class="client-profile-booking-details">
                                                <div>{{ $bookingDate }}</div>
                                                <div class="client-profile-booking-time-space">{{ $bookingTime }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @else
                                        <td>
                                            <div class="client-profile-booking-details">
                                                <div>{{ $bookingDate }}</div>
                                                <div>{{ $bookingTime }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="client-profile-pet-cell">
                                                <span
                                                    class="client-profile-pet-name-inline">{{ $petName }}</span>
                                                @if ($petType)
                                                    <span class="client-profile-pet-type">{{ $petType }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="client-profile-service-type">{{ $booking->service }}</td>
                                        <td>{{ $locationLabel }}</td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @endif
                                    <td class="client-profile-action-col">
                                        <div class="client-profile-action-cell">
                                            <button type="button" class="client-profile-action-btn is-message"
                                                aria-label="Message">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                    viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18"
                                                        fill="#CBDCE8" />
                                                    <path
                                                        d="M18.3955 11.25C22.4278 11.25 25.542 14.1354 25.542 17.5137C25.542 20.892 22.4278 23.7773 18.3955 23.7773H18.3945C17.6796 23.779 16.9672 23.6847 16.2764 23.4971L15.9951 23.4209L15.7373 23.5537C15.3001 23.7782 14.314 24.2099 12.6807 24.5547C12.9199 23.8218 13.1163 22.9878 13.1914 22.1934L13.2236 21.8457L12.9795 21.5967C11.8924 20.4903 11.25 19.0614 11.25 17.5137C11.25 14.1355 14.3634 11.2502 18.3955 11.25Z"
                                                        stroke="white" stroke-width="1.5" />
                                                </svg>
                                            </button>
                                            <button type="button" class="client-profile-action-btn is-reschedule"
                                                aria-label="Reschedule">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                    viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18"
                                                        fill="#FFC97A" />
                                                    <path d="M12.2312 25.4951V22.6123H15.114" stroke="white"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M25.3656 16.6225C25.6715 18.2545 25.4269 19.9419 24.6702 21.4199C23.9135 22.8978 22.6875 24.0827 21.1846 24.7887C19.6818 25.4946 17.987 25.6817 16.3664 25.3204C14.7458 24.9592 13.2909 24.0701 12.2301 22.7927M10.6283 19.3775C10.3224 17.7455 10.567 16.0581 11.3237 14.5801C12.0804 13.1022 13.3064 11.9173 14.8093 11.2113C16.3121 10.5054 18.0069 10.3183 19.6275 10.6796C21.2481 11.0408 22.703 11.9299 23.7638 13.2073"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M23.7626 10.5049V13.3877H20.8797M14.6142 18.3885C14.2062 18.3176 14.2062 17.7317 14.6142 17.6608C15.3364 17.5345 16.0047 17.1963 16.5342 16.6891C17.0637 16.182 17.4304 15.5289 17.5877 14.8128L17.6121 14.7001C17.7005 14.2967 18.2747 14.2944 18.3666 14.6966L18.3968 14.828C18.5592 15.5413 18.9289 16.1906 19.4594 16.6943C19.99 17.1979 20.6576 17.5334 21.3784 17.6585C21.7888 17.7294 21.7888 18.3187 21.3784 18.3908C20.6578 18.5158 19.9902 18.8511 19.4597 19.3546C18.9292 19.858 18.5594 20.5071 18.3968 21.2202L18.3666 21.3504C18.2747 21.7526 17.7005 21.7502 17.6121 21.3469L17.5889 21.2353C17.4314 20.5189 17.0643 19.8655 16.5344 19.3584C16.0045 18.8513 15.3357 18.5132 14.6131 18.3873"
                                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                            <button type="button" class="client-profile-action-btn is-cancel"
                                                aria-label="Cancel">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                    viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18"
                                                        fill="#FF6E6E" />
                                                    <path d="M13 23L23 13M13 13L23 23" stroke="white"
                                                        stroke-width="1.5" stroke-linecap="round" />
                                                </svg>
                                            </button>
                                            <x-dashboard.common.more-action-btn :row-id="$booking->id" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isSpaceUser ? 6 : 7 }}" class="client-profile-empty-cell">No
                                        bookings
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreProfile">
                                <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .client-profile-wrapper {
            margin-top: 0;
        }

        .client-profile-back-block {
            margin-bottom: 4rem;
        }

        .client-profile-back {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            margin-bottom: 0.75rem;
        }

        .client-profile-back-loader {
            display: none;
            position: relative;
            height: 4px;
        }

        .client-profile-back-loader.is-visible {
            display: block;
        }

        .client-profile-back-loader .active-section-loading-bar {
            position: relative;
            left: 0;
            right: 0;
            bottom: auto;
            height: 4px;
        }

        .client-profile-card {
            position: relative;
            border-radius: 10px;
            border: 1px solid #C9DDA0;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.05);
            background: #FFF;
            overflow: hidden;
            margin-bottom: 2rem;
            transition: border-color 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-card.is-blocked {
            border-color: #A8A8A8;
        }

        .client-profile-accent {
            width: 80px;
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1;
            background: #C9DDA0;
            box-sizing: border-box;
            border-right: 1px solid #C9DDA0;
            transition: background-color 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-accent.is-blocked {
            background: #A8A8A8;
            border-right-color: #A8A8A8;
        }

        .client-profile-avatar-wrap {
            position: absolute;
            left: 80px;
            top: 1.75rem;
            transform: translateX(-50%);
            z-index: 3;
            width: 96px;
            height: 96px;
            box-sizing: border-box;
            padding: 5px;
            border-radius: 999px;
            overflow: hidden;
            background: #FFF;
            border: 1px solid #FFC97A;
            animation: client-profile-avatar-in 0.45s ease-out 0.12s both;
        }

        @keyframes client-profile-avatar-in {
            from {
                opacity: 0;
                transform: translateX(-50%) scale(0.88);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) scale(1);
            }
        }

        .client-profile-card-main {
            position: relative;
            z-index: 2;
            padding: 1.5rem 1.75rem 0 calc(80px + 5rem);
        }

        .client-profile-top {
            width: 100%;
        }

        .client-profile-info {
            min-width: 0;
        }

        .client-profile-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.35rem;
        }

        .client-profile-body-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .client-profile-details {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 1;
        }

        .client-profile-avatar-img,
        .client-profile-avatar-fallback {
            width: 100%;
            height: 100%;
            border-radius: 999px;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-weight: 600;
        }

        .client-profile-avatar-fallback {
            background: #f0ebe4;
        }

        .client-profile-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 100px;
            padding: 0.2rem 0.65rem;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            width: 80px;
            height: 32px;
            transition: background-color 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-status.is-active {
            color: #AFCD6F;
            background: rgba(186, 207, 142, 0.10);
        }

        .client-profile-status.is-inactive {
            color: #9D9B98;
            background: #ECEBEB;
        }

        .client-profile-status.is-blocked {
            background: #ECEBEB;
            color: #9D9B98;
        }

        .client-profile-name-row {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            min-width: 0;
        }

        .client-profile-name {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
            line-height: normal;
        }

        .client-profile-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 3rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }


        .client-profile-message-btn {
            width: 130px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0 1rem;
            border: 0;
            border-radius: 100px;
            background: rgba(203, 220, 232, 0.20);
            color: #94BEDB;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            flex-shrink: 0;
        }

        .client-profile-more-btn {
            width: 32px;
            height: 32px;
            border: 0;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .client-profile-more-wrap {
            flex-shrink: 0;
        }

        .client-profile-more-menu {
            position: fixed;
            z-index: 99999;
            width: 130px;
            height: 33px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-more-menu.is-loading {
            width: 32px;
        }

        .client-profile-more-item {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #D9D9D9;
            background: #F8F8F8;
            padding: 0 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            line-height: 1;
            cursor: pointer;
            white-space: nowrap;
            transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                gap 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-more-item.is-loading {
            justify-content: center;
            gap: 0;
            padding: 0;
            background: #FFF;
        }

        .client-profile-more-item:hover {
            background: #F8F8F8;
        }

        .client-profile-more-item[disabled] {
            opacity: 0.7;
            cursor: wait;
        }

        .client-profile-more-item-loading {
            display: none;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 18px;
            height: 18px;
            background: #F8F8F8;
        }

        .client-profile-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
            margin-top: 1rem;
        }

        .client-profile-stat {
            position: relative;
            border: 0;
            background: transparent;
            text-align: left;
            padding: 1.1rem 1rem 1.25rem;
            cursor: pointer;
        }

        .client-profile-stat:first-child {
            padding-left: 0;
        }

        .client-profile-stat:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 1rem;
            bottom: 1rem;
            width: 2px;
            background: #E0E0E0;
        }

        .client-profile-stat-label {
            display: block;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-bottom: 0.7rem;
        }

        .client-profile-stat-value {
            display: inline-flex;
            align-items: center;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            gap: 1.5rem;
        }

        .client-profile-stat-figure {
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .client-profile-stat-chevron {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9D9B98;
            font-size: 18px;
        }

        .client-profile-toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .client-profile-tabs {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .client-profile-tab {
            border: none;
            border-radius: 100px;
            padding: 0.55rem 1rem;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            transition: background-color 0.28s ease, color 0.28s ease;
        }

        .client-profile-tab.is-active {
            color: #FFF;
            background: #FFC97A;
        }

        .client-profile-wrapper.is-space-user .client-profile-tab.is-active {
            background: #FFA899;
        }

        .client-profile-tab.is-muted {
            color: #9D9B98;
            background: rgba(221, 221, 221, 0.10);
        }

        .client-profile-tab-panel {
            animation: client-profile-tab-in 0.32s ease-out;
        }

        @keyframes client-profile-tab-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .client-profile-toolbar-enter {
            transition: opacity 0.24s ease, transform 0.24s ease;
        }

        .client-profile-toolbar-enter-start {
            opacity: 0;
            transform: translateX(8px);
        }

        .client-profile-toolbar-enter-end {
            opacity: 1;
            transform: translateX(0);
        }

        .client-profile-toolbar-leave {
            transition: opacity 0.18s ease, transform 0.18s ease;
        }

        .client-profile-toolbar-leave-start {
            opacity: 1;
            transform: translateX(0);
        }

        .client-profile-toolbar-leave-end {
            opacity: 0;
            transform: translateX(-8px);
        }

        @media (prefers-reduced-motion: reduce) {

            .client-profile-tab,
            .client-profile-tab-panel,
            .client-profile-toolbar-enter,
            .client-profile-toolbar-leave {
                animation: none;
                transition: none;
                transform: none;
            }
        }

        .client-profile-sort .sort-dropdown {
            position: relative;
        }

        .client-profile-sort .sort-trigger {
            width: 69px;
            height: 32px;
            border-radius: 100px;
            border: 1px solid #A8A8A8;
            background: transparent;
            color: #A8A8A8;
            font-family: Lato;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }

        .client-profile-sort-menu {
            min-width: 220px;
            width: max-content;
            background: #F8F8F8;
            border: 2px solid #e6e6e5;
            border-radius: 10px 0 10px 10px;
            overflow: hidden;
        }

        .client-profile-sort-menu .sort-options {
            width: 100%;
            border: 0;
            border-bottom: 2px solid #e6e6e5;
            background: #FFF;
            padding: 1rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .client-profile-sort-menu .sort-options:last-child {
            border-bottom: none;
        }

        .client-profile-sort-menu .sort-indicator {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 2px solid #FFC97A;
            background: transparent;
            position: relative;
            flex-shrink: 0;
        }

        .client-profile-sort-menu .sort-options.is-active .sort-indicator::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 999px;
            background: #FFC97A;
        }

        .client-profile-table-shell {
            overflow-x: auto;
        }

        .client-profile-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 960px;
        }

        .client-profile-table th,
        .client-profile-table td {
            border-bottom: 1px solid #dcdcdc;
            text-align: left;
            padding: 1.1rem 0;
            vertical-align: middle;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
        }

        .client-profile-table th {
            font-weight: 600;
            color: #000;
        }

        .client-profile-table th:last-child,
        .client-profile-table td:last-child {
            padding-left: 2rem;
        }


        .client-profile-action-col {
            text-align: center;
            width: 200px;
            border-left: 1px solid #E2E2E2;
        }

        .client-profile-booking-details {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .client-profile-booking-time-space {
            color: #9D9B98;
            font-weight: 400;
        }

        .client-profile-service-type {
            font-weight: 600;
        }

        .client-profile-pet-cell {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .client-profile-pet-type {
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-profile-pet-name-inline {
            font-weight: 600;
        }

        .client-profile-action-cell {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
        }

        .client-profile-action-btn {
            border: none;
            padding: 0;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
        }

        .client-profile-empty,
        .client-profile-empty-cell {
            color: #9D9B98 !important;
            text-align: center;
            padding: 2rem 0;
            font-family: Lato;
            font-size: 16px;
        }

        .client-profile-load-more-wrap {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .client-profile-load-more-btn {
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

        .client-profile-load-more-btn[disabled] {
            opacity: 0.9;
            cursor: wait;
        }

        .client-profile-load-more-loading {
            display: none;
            align-items: center;
            justify-content: center;
        }

        .client-profile-load-more-spinner {
            width: 18px;
            height: 18px;
            border-radius: 9999px;
            border: 2px solid #3B3731;
            border-top-color: transparent;
            animation: client-profile-load-more-spin 0.7s linear infinite;
        }

        @keyframes client-profile-load-more-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 900px) {
            .client-profile-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .client-profile-avatar-wrap {
                width: 80px;
                height: 80px;
                left: 64px;
                top: 1.5rem;
            }

            .client-profile-card-main {
                padding-left: calc(64px + 3rem);
            }
        }
    </style>
@endif
