@php
    $profileWireTargets = $profileWireTargets ?? 'viewProfile, setProfileTab, setProfileSort, setProfilePetSort, loadMoreProfile, viewPetDetails, updateGroomerGuidanceNotes, galleryUploads, removePetGalleryPhoto, addGroomerNote, addOwnerNote, updateGroomerNote, deleteGroomerNote, updateOwnerNote, deleteOwnerNote, openCompletedBookingModal, closeCompletedBookingModal, openRescheduleModal, closeRescheduleModal, confirmRescheduleBookingFromClient, openDeclineModal, closeDeclineModal, confirmDeclineBooking, toggleReviewReply, closeReviewReply, submitReviewReply';
@endphp

@if ($selectedPetId && $this->selectedPet)
    <x-business-hub.clients.client-pet-medication-view :pet="$this->selectedPet" :medication="$this->selectedPetMedication"
        :vaccination-rows="$this->petVaccinationRows" :overdue-vaccination-count="$this->petOverdueVaccinationCount" />
@else
    @php
        $isSpaceUser = $this->isSpaceProfile();
        $meta = $this->profileMeta;
        $tabCounts = $this->profileTabCounts;
        $paidAmount = (float) ($meta['total_paid'] ?? 0);
        $paidLabel = abs($paidAmount - round($paidAmount)) < 0.001 ? '£' . number_format($paidAmount, 0) : '£' . number_format($paidAmount, 2);
        $profileTabs = $isSpaceUser
            ? [
                'upcoming' => 'Upcoming bookings',
                'bookings' => 'Bookings history',
                'reviews' => 'Reviews',
                'payments' => 'Payments',
            ]
            : [
                'upcoming' => 'Upcoming bookings',
                'pets' => 'Pets',
                'bookings' => 'Bookings history',
                'reviews' => 'Reviews',
                'payments' => 'Payments',
            ];
    @endphp
    <section class="client-profile-wrapper {{ $isSpaceUser ? 'is-space-user' : '' }}" aria-label="Client profile">
        <div class="client-profile-back-block">
            <button type="button" class="client-profile-back" @click="closeProfileView()">
                <img src="{{ asset('images/business-hub/icon-profile-back.svg') }}" alt="">
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
                const btn = this.$refs.moreBtn.getBoundingClientRect();
                this.menuStyle = `top:${btn.bottom + 10}px;left:${Math.max(8, btn.right - 156)}px;`;
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
        }" :class="{ 'is-blocked': blocked }" x-effect="if (openMenu) repositionMenu()"
            @keydown.escape.window="openMenu = false" @resize.window="if (openMenu) repositionMenu()"
            @scroll.window="if (openMenu) repositionMenu()"
            @click.window="if (openMenu && $refs.moreBtn && !$refs.moreBtn.contains($event.target) && $refs.moreMenu && !$refs.moreMenu.contains($event.target)) { openMenu = false }">
            <div class="client-profile-hero">
                <img class="client-profile-shape is-left" src="{{ asset('images/business-hub/profile-card-shape-left.svg') }}" alt="">
                <img class="client-profile-shape is-right" src="{{ asset('images/business-hub/profile-card-shape-right.svg') }}" alt="">

                <div class="client-profile-hero-inner">
                    <div class="client-profile-avatar-wrap">
                        @if ($meta['avatar_url'])
                            <img src="{{ $meta['avatar_url'] }}" alt="{{ $meta['name'] }}" class="client-profile-avatar-img" />
                        @else
                            <span class="client-profile-avatar-fallback">{{ $meta['initials'] }}</span>
                        @endif
                    </div>

                    <div class="client-profile-identity">
                        <div class="client-profile-name-row">
                            <h3 class="client-profile-name">{{ $meta['name'] }}</h3>
                            <span class="client-profile-status"
                                :class="blocked ? 'is-blocked' : (@js($meta['is_active']) ? 'is-active' : 'is-inactive')">
                                <span x-text="blocked ? 'Blocked' : (@js($meta['is_active']) ? 'Active' : 'Inactive')"></span>
                            </span>
                        </div>

                        <div class="client-profile-meta-row">
                            <span>Location <strong>{{ $meta['location'] }}</strong></span>
                            <span>Client since <strong>{{ $meta['client_since'] }}</strong></span>
                            @unless ($isSpaceUser)
                                <span>Pets <strong>{{ $meta['pets_label'] }}</strong></span>
                            @endunless
                        </div>
                    </div>

                    <div class="client-profile-actions">
                        <button type="button" class="client-profile-message-btn">
                            <img src="{{ asset('images/business-hub/icon-profile-message.svg') }}" alt="">
                            Message
                        </button>
                        <button type="button" class="client-profile-more-btn" x-ref="moreBtn" @click.stop="toggleMenu()"
                            aria-label="More options" :aria-expanded="openMenu.toString()">
                            <img src="{{ asset('images/business-hub/icon-profile-more.svg') }}" alt="">
                        </button>
                    </div>
                </div>
            </div>

            <div class="client-profile-stats">
                <button type="button" class="client-profile-stat" wire:click="setProfileTab('upcoming')">
                    <span class="client-profile-stat-label">Upcoming</span>
                    <span class="client-profile-stat-value">
                        <span class="client-profile-stat-figure">{{ $meta['upcoming_count'] }}</span>
                        <span class="client-profile-stat-suffix">/ bookings</span>
                    </span>
                </button>
                <button type="button" class="client-profile-stat" wire:click="setProfileTab('bookings')">
                    <span class="client-profile-stat-label">Completed</span>
                    <span class="client-profile-stat-value">
                        <span class="client-profile-stat-figure">{{ $meta['completed_count'] }}</span>
                        <span class="client-profile-stat-suffix">/ bookings</span>
                    </span>
                </button>
                <button type="button" class="client-profile-stat" wire:click="setProfileTab('payments')">
                    <span class="client-profile-stat-label">Total Paid</span>
                    <span class="client-profile-stat-value">
                        <span class="client-profile-stat-figure">{{ $paidLabel }}</span>
                    </span>
                </button>
                <button type="button" class="client-profile-stat" wire:click="setProfileTab('reviews')">
                    <span class="client-profile-stat-label">Avg Rating</span>
                    <span class="client-profile-stat-value">
                        @if ($meta['avg_rating'])
                            <img class="client-profile-stat-star" src="{{ asset('images/business-hub/icon-profile-star.svg') }}" alt="">
                            <span class="client-profile-stat-figure">{{ number_format($meta['avg_rating'], 1) }}</span>
                        @else
                            <span class="client-profile-stat-figure">—</span>
                        @endif
                    </span>
                </button>
            </div>

            <template x-teleport="body">
                <div class="client-profile-more-menu" x-cloak x-show="openMenu" x-ref="moreMenu"
                    wire:loading.class="is-loading" wire:target="toggleClientBlock" :style="menuStyle"
                    x-transition.opacity.duration.100ms>
                    <button type="button" class="client-profile-more-item" :class="blocked ? 'is-activate' : 'is-block'"
                        @click="toggleBlock()" wire:loading.attr="disabled" wire:loading.class="is-loading"
                        wire:target="toggleClientBlock">
                        <span wire:loading.remove wire:target="toggleClientBlock"
                            x-text="blocked ? 'Activate client' : 'Block client'"></span>
                        <span class="client-profile-more-item-loading" wire:loading.inline-flex
                            wire:target="toggleClientBlock">
                            <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </template>
        </article>

        <div class="client-profile-toolbar">
            <div class="client-profile-tabs">
                @foreach ($profileTabs as $tabKey => $tabLabel)
                    <button type="button" wire:click="setProfileTab('{{ $tabKey }}')"
                        class="client-profile-tab {{ $profileActiveTab === $tabKey ? 'is-active' : 'is-muted' }}">
                        <span>{{ $tabLabel }}</span>
                        <span class="client-profile-tab-count">{{ $tabCounts[$tabKey] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            <div class="client-profile-sort" x-show="$wire.profileActiveTab === 'pets'" x-cloak
                x-transition:enter="client-profile-toolbar-enter"
                x-transition:enter-start="client-profile-toolbar-enter-start"
                x-transition:enter-end="client-profile-toolbar-enter-end" x-transition:leave="client-profile-toolbar-leave"
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
                }" @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                @scroll.window="if (open) repositionMenu()"
                @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                <div class="sort-dropdown">
                    <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
                        aria-label="Sort pets" :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8" stroke-linecap="round"
                                stroke-linejoin="round" />
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

            <div class="client-profile-sort" x-show="['bookings', 'payments'].includes($wire.profileActiveTab)"
                x-cloak x-transition:enter="client-profile-toolbar-enter"
                x-transition:enter-start="client-profile-toolbar-enter-start"
                x-transition:enter-end="client-profile-toolbar-enter-end" x-transition:leave="client-profile-toolbar-leave"
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
                }" @keydown.escape.window="open = false" @resize.window="if (open) repositionMenu()"
                @scroll.window="if (open) repositionMenu()"
                @click.window="if (open && !$refs.sortBtn.contains($event.target) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) { open = false }">
                <div class="sort-dropdown">
                    <button type="button" class="sort-trigger" x-ref="sortBtn" @click.stop="toggleMenu()"
                        aria-label="Sort bookings" :aria-expanded="open.toString()">
                        <span>Sort</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                            <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8" stroke-linecap="round"
                                stroke-linejoin="round" />
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
                <x-business-hub.clients.client-pet-view :pets="$this->profilePets" />
            @elseif ($profileActiveTab === 'reviews')
                <x-business-hub.clients.client-reviews-view :reviews="$this->profileVisibleTabReviews"
                    :client-name="$meta['name']" :open-reply-id="$openReviewReplyId" />

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex wire:target="loadMoreProfile">
                                <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @elseif ($profileActiveTab === 'payments')
                <x-business-hub.clients.client-payments-view :payments="$this->profileVisibleTabPayments"
                    :is-space-user="$isSpaceUser" />

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex wire:target="loadMoreProfile">
                                <span class="client-profile-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @elseif ($profileActiveTab === 'bookings')
                <x-business-hub.clients.client-bookings-view :bookings="$this->profileVisibleTabBookings"
                    :is-space-user="$isSpaceUser" />

                @if ($this->profileCanLoadMore)
                    <div class="client-profile-load-more-wrap">
                        <button type="button" class="client-profile-load-more-btn" wire:click="loadMoreProfile"
                            wire:loading.attr="disabled" wire:target="loadMoreProfile">
                            <span wire:loading.remove wire:target="loadMoreProfile">Load More</span>
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex wire:target="loadMoreProfile">
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
                                    <th class="col-id">Booking ID</th>
                                    <th class="col-service">Service</th>
                                    <th class="col-location">Space</th>
                                    <th class="col-when">When</th>
                                    <th class="col-staff">Staff</th>
                                    <th class="client-profile-action-col">Action</th>
                                @else
                                    <th class="col-id">Booking ID</th>
                                    <th class="col-pet">Pet</th>
                                    <th class="col-service">Service</th>
                                    <th class="col-when">When</th>
                                    <th class="col-location">Location</th>
                                    <th class="col-staff">Staff</th>
                                    <th class="client-profile-action-col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->profileVisibleTabBookings as $booking)
                                @php
                                    $petNames = $booking->pets->pluck('name')->filter()->values()->all();
                                    $petTypes = $booking->pets->pluck('pet_type')->filter()->values()->all();
                                    $petName = $petNames[0] ?? '—';
                                    $petType = strtolower((string) ($petTypes[0] ?? ''));
                                    $petIcon = str_contains($petType, 'cat') ? 'images/business-hub/icon-profile-pet-cat.svg' : 'images/business-hub/icon-profile-pet-dog.svg';
                                    $whenLabel = $this->formatProfileUpcomingWhen($booking->date, (string) $booking->time);
                                    $locationLabel = $this->formatProfileLocationLabel($booking->visit_type ?? null);
                                    $spaceLabel = $this->formatProfileSpaceLabel($booking->visit_type ?? null);
                                @endphp
                                <tr wire:key="client-booking-{{ $booking->id }}">
                                    <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    @if ($isSpaceUser)
                                        <td>{{ $booking->service }}</td>
                                        <td>{{ $spaceLabel }}</td>
                                        <td>{{ $whenLabel }}</td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @else
                                        <td>
                                            <span class="client-profile-pet-inline">
                                                @if ($petNames !== [])
                                                    <img src="{{ asset($petIcon) }}" alt="">
                                                @endif
                                                <span>{{ $petName }}</span>
                                            </span>
                                        </td>
                                        <td>{{ $booking->service }}</td>
                                        <td>{{ $whenLabel }}</td>
                                        <td>{{ $locationLabel }}</td>
                                        <td>{{ $booking->staff ?: 'N/A' }}</td>
                                    @endif
                                    <td class="client-profile-action-col">
                                        <button type="button" class="client-profile-action-btn" aria-label="View booking">
                                            <img src="{{ asset('images/business-hub/icon-view-client.svg') }}" alt="">
                                        </button>
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
                            <span class="client-profile-load-more-loading" wire:loading.inline-flex wire:target="loadMoreProfile">
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
            margin-bottom: 32px;
        }

        .client-profile-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            margin-bottom: 0;
        }

        .client-profile-back img {
            display: block;
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
            border: 0;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.05);
            background: #FFF;
            overflow: hidden;
            margin-bottom: 40px;
        }

        .client-profile-hero {
            position: relative;
            min-height: 148px;
            background: #FCFCFC;
            overflow: hidden;
        }

        .client-profile-wrapper img {
            width: auto;
            max-width: none;
        }

        .client-profile-shape {
            position: absolute;
            top: 0;
            pointer-events: none;
            display: block;
        }

        .client-profile-shape.is-left {
            left: 0;
            width: 119px;
            height: 148px;
        }

        .client-profile-shape.is-right {
            right: 0;
            width: 120px;
            height: 149px;
            transform: rotate(180deg);
            transform-origin: center;
        }

        .client-profile-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            min-height: 148px;
            padding: 20px 20px 18px;
        }

        .client-profile-avatar-wrap {
            width: 110px;
            height: 110px;
            box-sizing: border-box;
            padding: 6.6px;
            border-radius: 999px;
            background: #FFC97A;
            flex-shrink: 0;
            display: flex;
        }

        .client-profile-avatar-wrap .client-profile-avatar-img,
        .client-profile-avatar-wrap .client-profile-avatar-fallback {
            width: 96.8px;
            height: 96.8px;
            border-radius: 999px;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FDFDFD;
            font-family: Lato;
            font-size: 28px;
            font-weight: 800;
        }

        .client-profile-avatar-fallback {
            background: #FBAC83;
        }

        .client-profile-identity {
            min-width: 0;
            flex: 1;
            padding-top: 5px;
        }

        .client-profile-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 100px;
            padding: 0 10px;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            min-width: 62px;
            height: 26px;
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
            gap: 12px;
            min-width: 0;
        }

        .client-profile-name {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 20px;
            font-weight: 600;
            line-height: normal;
        }

        .client-profile-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 28px 40px;
            margin-top: 16px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .client-profile-meta-row strong {
            color: #3B3731;
            font-weight: 400;
        }

        .client-profile-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-shrink: 0;
        }

        .client-profile-message-btn {
            width: 101px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0;
            border: 0;
            border-radius: 100px;
            background: #FFC97A;
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            flex-shrink: 0;
        }

        .client-profile-wrapper.is-space-user .client-profile-message-btn {
            background: #FFA899;
        }

        .client-profile-more-btn {
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .client-profile-more-menu {
            position: fixed;
            z-index: 99999;
            width: 156px;
            height: 36px;
            overflow: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-profile-more-menu.is-loading {
            width: 32px;
        }

        .client-profile-more-item {
            width: 130px;
            height: 100%;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #D9D9D9;
            background: #FAF8F4;
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
            border-top: 1px solid #E2E2E2;
            min-height: 100px;
        }

        .client-profile-stat {
            position: relative;
            border: 0;
            background: transparent;
            text-align: left;
            padding: 22px 16px 18px 28px;
            cursor: pointer;
        }

        .client-profile-stat:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 1px;
            background: #E2E2E2;
        }

        .client-profile-stat-label {
            display: block;
            color: #9D9B98;
            font-family: Lato;
            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .client-profile-stat-value {
            display: inline-flex;
            align-items: flex-end;
            gap: 8px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 1;
        }

        .client-profile-stat-figure {
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-style: normal;
            font-weight: 800;
            line-height: 1;
        }

        .client-profile-stat-suffix {
            padding-bottom: 3px;
        }

        .client-profile-stat-star {
            display: block;
            margin-bottom: 4px;
        }

        .client-profile-toolbar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 40px;
            border-bottom: 1px solid #E2E2E2;
        }

        .client-profile-tabs {
            display: flex;
            align-items: flex-end;
            gap: 36px;
            flex-wrap: wrap;
        }

        .client-profile-tab {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 0;
            border-radius: 0;
            background: transparent;
            padding: 0 0 16px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            outline: none;
            box-shadow: none;
        }

        .client-profile-tab:hover,
        .client-profile-tab:focus,
        .client-profile-tab:focus-visible,
        .client-profile-tab:active {
            outline: none;
            box-shadow: none;
            border: 0;
        }

        .client-profile-tab.is-active {
            color: #3B3731;
            background: transparent;
        }

        .client-profile-tab.is-active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 1.5px;
            background: #FFC97A;
        }

        .client-profile-wrapper.is-space-user .client-profile-tab.is-active::after {
            background: #FFA899;
        }

        .client-profile-tab.is-muted {
            color: #9D9B98;
            background: transparent;
        }

        .client-profile-tab-count {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 201, 122, 0.10);
            color: #FFC97A;
            font-family: Lato;
            font-size: 14px;
            font-weight: 600;
            line-height: 1;
        }

        .client-profile-tab.is-active .client-profile-tab-count {
            background: #FFC97A;
            color: #FFF;
        }

        .client-profile-wrapper.is-space-user .client-profile-tab-count {
            background: rgba(255, 168, 153, 0.12);
            color: #FFA899;
        }

        .client-profile-wrapper.is-space-user .client-profile-tab.is-active .client-profile-tab-count {
            background: #FFA899;
            color: #FFF;
        }

        .client-profile-tab-panel {
            opacity: 1;
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
            width: calc(100% - 4px);
            margin: 2px;
            overflow: visible;
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
        }

        .client-profile-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            min-width: 860px;
        }

        .client-profile-table thead {
            background: #F6F5F5;
        }

        .client-profile-table th,
        .client-profile-table td {
            border: 0;
            text-align: left;
            vertical-align: middle;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-profile-table th {
            height: 50px;
            padding: 0 8px;
            color: #948F88;
            font-weight: 600;
            background: #F6F5F5;
            white-space: nowrap;
        }

        .client-profile-table th:first-child {
            border-top-left-radius: 10px;
            padding-left: 20px;
        }

        .client-profile-table th:last-child {
            border-top-right-radius: 10px;
            padding-right: 20px;
        }

        .client-profile-table td {
            height: 56px;
            padding: 7px 8px;
        }

        .client-profile-table td:first-child {
            padding-left: 20px;
        }

        .client-profile-table td:last-child {
            padding-right: 20px;
        }

        .client-profile-table tbody tr {
            background-color: #FDFDFD;
        }

        .client-profile-table tbody tr:not(:last-child) {
            background-image: linear-gradient(#E2E2E2, #E2E2E2);
            background-repeat: no-repeat;
            background-size: calc(100% - 40px) 1px;
            background-position: center bottom;
        }

        .client-profile-table .col-id {
            width: 14%;
        }

        .client-profile-table .col-pet {
            width: 12%;
        }

        .client-profile-table .col-service {
            width: 14%;
        }

        .client-profile-table .col-when {
            width: 28%;
        }

        .client-profile-table .col-location {
            width: 14%;
        }

        .client-profile-table .col-staff {
            width: 10%;
        }

        .client-profile-action-col {
            width: 8%;
            text-align: right;
        }

        .client-profile-pet-inline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
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
            text-align: center !important;
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
            .client-profile-hero-inner {
                flex-wrap: wrap;
            }

            .client-profile-actions {
                margin-left: 0;
            }

            .client-profile-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .client-profile-stat:nth-child(2)::after {
                display: none;
            }
        }
    </style>
@endif
