<header class="admin-header">
    <div class="container">
        <div class="admin-header-inner">
            <button type="button" class="admin-brand" aria-label="FursGo Admin"
                @click="setTab('overview')">
                <img src="{{ asset('images/header/logo-fursgo.svg') }}" alt="fursgo" class="admin-logo"
                    width="98" height="27">
                <span class="admin-pill">Admin</span>
            </button>

            <nav class="admin-tabs" aria-label="Admin sections">
                <template x-for="item in tabs" :key="item.id">
                    <button type="button"
                        class="admin-tab"
                        :class="{ 'is-active': ($wire && $wire.activeTab ? $wire.activeTab : tab) === item.id }"
                        @click="setTab(item.id)"
                        x-text="item.label">
                    </button>
                </template>
            </nav>

            <div class="admin-header-actions"
                x-data="{ openMenu: null, unreadMessages: true, unreadNotifications: true }"
                @keydown.escape.window="openMenu = null">
                <div class="admin-header-menu-wrap" @click.outside="if (openMenu === 'messages') openMenu = null">
                <button type="button" class="admin-icon-btn admin-icon-btn--badged" aria-label="Messages"
                    aria-controls="admin-messages-menu" :aria-expanded="(openMenu === 'messages').toString()"
                    :class="{ 'is-active': openMenu === 'messages' }"
                    @click="openMenu = openMenu === 'messages' ? null : 'messages'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="21" viewBox="0 0 26.5 21.5" fill="none"
                        aria-hidden="true">
                        <path
                            d="M0.75 10.75C0.75 6.03625 0.75 3.67875 2.215 2.215C3.68 0.75125 6.03625 0.75 10.75 0.75H15.75C20.4637 0.75 22.8212 0.75 24.285 2.215C25.7487 3.68 25.75 6.03625 25.75 10.75C25.75 15.4637 25.75 17.8212 24.285 19.285C22.82 20.7487 20.4637 20.75 15.75 20.75H10.75C6.03625 20.75 3.67875 20.75 2.215 19.285C0.75125 17.82 0.75 15.4637 0.75 10.75Z"
                            stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M5.75 5.75L8.44875 8C10.745 9.9125 11.8925 10.8688 13.25 10.8688C14.6075 10.8688 15.7562 9.9125 18.0512 7.99875L20.75 5.75"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    <span class="admin-icon-dot" x-show="unreadMessages" x-cloak aria-hidden="true"></span>
                </button>

                <div id="admin-messages-menu" class="admin-header-menu" x-show="openMenu === 'messages'" x-cloak
                    x-transition.opacity.duration.150ms>
                    <div class="admin-header-menu-title">
                        <h2>Messages</h2>
                        <button type="button" @click="unreadMessages = false">Mark all read</button>
                    </div>
                    <div class="admin-header-menu-list">
                        <a href="{{ route('messages_notification.messages') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>Sarah&rsquo;s Grooming Studio</strong>
                                <span>Question about my payout showing as pen...</span>
                                <small>Provider</small>
                            </span>
                            <time>12m ago</time>
                        </a>
                        <a href="{{ route('messages_notification.messages') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>Emma R.</strong>
                                <span>My booking confirmation email never arrive...</span>
                                <small>Customer</small>
                            </span>
                            <time>1h ago</time>
                        </a>
                        <a href="{{ route('messages_notification.messages') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>Sarah&rsquo;s Grooming Studio</strong>
                                <span>Question about my payout showing as pen...</span>
                                <small>Provider</small>
                            </span>
                            <time>12m ago</time>
                        </a>
                    </div>
                    <a class="admin-header-menu-footer" href="{{ route('messages_notification.messages') }}" wire:navigate>
                        View all messages
                    </a>
                </div>
                </div>

                <div class="admin-header-menu-wrap" @click.outside="if (openMenu === 'notifications') openMenu = null">
                <button type="button" class="admin-icon-btn admin-icon-btn--badged admin-icon-btn--bell" aria-label="Notifications"
                    aria-controls="admin-notifications-menu" :aria-expanded="(openMenu === 'notifications').toString()"
                    :class="{ 'is-active': openMenu === 'notifications' }"
                    @click="openMenu = openMenu === 'notifications' ? null : 'notifications'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="65 1 20 21" fill="none"
                        aria-hidden="true">
                        <path
                            d="M80.884 10.75C81.465 16.125 83.75 17.75 83.75 17.75H65.75C65.75 17.75 68.75 15.617 68.75 8.15C68.75 6.453 69.382 4.825 70.507 3.625C71.632 2.425 73.16 1.75 74.75 1.75C75.088 1.75 75.4213 1.78 75.75 1.84M76.48 20.75C76.3042 21.0531 76.0518 21.3047 75.7482 21.4795C75.4446 21.6544 75.1004 21.7465 74.75 21.7465C74.3996 21.7465 74.0554 21.6544 73.7518 21.4795C73.4482 21.3047 73.1958 21.0531 73.02 20.75M81.75 7.75C82.5456 7.75 83.3087 7.43393 83.8713 6.87132C84.4339 6.30871 84.75 5.54565 84.75 4.75C84.75 3.95435 84.4339 3.19129 83.8713 2.62868C83.3087 2.06607 82.5456 1.75 81.75 1.75C80.9544 1.75 80.1913 2.06607 79.6287 2.62868C79.0661 3.19129 78.75 3.95435 78.75 4.75C78.75 5.54565 79.0661 6.30871 79.6287 6.87132C80.1913 7.43393 80.9544 7.75 81.75 7.75Z"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="admin-icon-dot" x-show="unreadNotifications" x-cloak aria-hidden="true"></span>
                </button>

                <div id="admin-notifications-menu" class="admin-header-menu" x-show="openMenu === 'notifications'" x-cloak
                    x-transition.opacity.duration.150ms>
                    <div class="admin-header-menu-title">
                        <h2>Notifications</h2>
                        <button type="button" @click="unreadNotifications = false">Mark all read</button>
                    </div>
                    <div class="admin-header-menu-list admin-header-menu-list--notifications">
                        <a href="{{ route('messages_notification.notifications') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>Payout run completed</strong>
                                <span>214 providers &middot; &pound;38,240.00</span>
                                <small>Payouts</small>
                            </span>
                            <time>12m ago</time>
                        </a>
                        <a href="{{ route('messages_notification.notifications') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>3 reviews flagged for moderation</strong>
                                <small>Support tickets</small>
                            </span>
                            <time>1h ago</time>
                        </a>
                        <a href="{{ route('messages_notification.notifications') }}" wire:navigate>
                            <span class="admin-menu-item-copy">
                                <strong>Tom R accepted admin invite</strong>
                                <span>Assigned as Content Editor</span>
                                <small>Admin</small>
                            </span>
                            <time>12m ago</time>
                        </a>
                    </div>
                    <a class="admin-header-menu-footer" href="{{ route('messages_notification.notifications') }}" wire:navigate>
                        View all notifications
                    </a>
                </div>
                </div>

                <div class="admin-account-menu-wrap" @click.outside="if (openMenu === 'account') openMenu = null">
                    <button
                        type="button"
                        class="admin-icon-btn"
                        aria-label="Account"
                        :aria-expanded="(openMenu === 'account').toString()"
                        aria-controls="admin-account-menu"
                        :class="{ 'is-active': openMenu === 'account' }"
                        @click="openMenu = openMenu === 'account' ? null : 'account'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="124 0 19.5 21.5" fill="none"
                            aria-hidden="true">
                            <path
                                d="M124.75 20.75V19.6389C124.75 15.9611 127.739 12.9722 131.417 12.9722H135.861C139.539 12.9722 142.528 15.9611 142.528 19.6389V20.75"
                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M133.639 9.63889C131.183 9.63889 129.194 7.65 129.194 5.19444C129.194 2.73889 131.183 0.75 133.639 0.75C136.094 0.75 138.083 2.73889 138.083 5.19444C138.083 7.65 136.094 9.63889 133.639 9.63889Z"
                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div id="admin-account-menu" class="admin-account-menu" x-show="openMenu === 'account'" x-cloak x-transition.opacity.duration.150ms>
                        <h2>Account</h2>
                        <div class="admin-account-summary">
                            <p class="admin-account-name">Name S.</p>
                            <p class="admin-account-email">name@fursgo.com</p>
                            <span class="admin-role-chip is-super">Super Admin</span>
                        </div>
                        <nav aria-label="Account menu">
                            <button type="button" @click="openAccountPage('profile'); openMenu = null">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none">
                                    <path d="M0.75 20.7499V19.6388C0.75 15.9611 3.73889 12.9722 7.41667 12.9722H11.8611C15.5389 12.9722 18.5278 15.9611 18.5278 19.6388V20.7499" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.63878 9.63889C7.18322 9.63889 5.19434 7.65 5.19434 5.19444C5.19434 2.73889 7.18322 0.75 9.63878 0.75C12.0943 0.75 14.0832 2.73889 14.0832 5.19444C14.0832 7.65 12.0943 9.63889 9.63878 9.63889Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                My Profile
                            </button>
                            <button type="button" @click="openAccountPage('notifications'); openMenu = null">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="22" viewBox="0 0 18 22" fill="none">
                                    <path d="M10.21 19.75C10.0342 20.0531 9.78184 20.3047 9.47823 20.4795C9.17461 20.6544 8.83038 20.7465 8.48 20.7465C8.12962 20.7465 7.78539 20.6544 7.48177 20.4795C7.17816 20.3047 6.92581 20.0531 6.75 19.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.75 16.75H0.75C1.41667 15.75 2.75 12.75 2.75 8.75C2.75 4.25 3.25 0.75 8.75 0.75C14.25 0.75 14.75 4.25 14.75 8.75C14.75 12.75 16.0833 15.5833 16.75 16.75H8.75Z" stroke="#3B3731" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                                Notification Preferences
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="admin-account-logout">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <path d="M12.5192 13.8269V16.4423C12.5192 16.7891 12.3815 17.1217 12.1362 17.367C11.891 17.6122 11.5584 17.75 11.2115 17.75H2.05769C1.71087 17.75 1.37825 17.6122 1.13301 17.367C0.887774 17.1217 0.75 16.7891 0.75 16.4423V2.05769C0.75 1.71087 0.887774 1.37825 1.13301 1.13301C1.37825 0.887774 1.71087 0.75 2.05769 0.75H11.2115C11.5584 0.75 11.891 0.887774 12.1362 1.13301C12.3815 1.37825 12.5192 1.71087 12.5192 2.05769V4.67308M8.59615 9.25H17.75M15.1346 11.8654L17.75 9.25L15.1346 6.63462" stroke="#FE6F56" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Log out
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
