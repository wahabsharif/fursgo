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
                        :class="{ 'is-active': tab === item.id }"
                        @click="setTab(item.id)"
                        x-text="item.label">
                    </button>
                </template>
            </nav>

            <div class="admin-header-actions">
                <button type="button" class="admin-icon-btn" aria-label="Messages">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="21" viewBox="0 0 26.5 21.5" fill="none"
                        aria-hidden="true">
                        <path
                            d="M0.75 10.75C0.75 6.03625 0.75 3.67875 2.215 2.215C3.68 0.75125 6.03625 0.75 10.75 0.75H15.75C20.4637 0.75 22.8212 0.75 24.285 2.215C25.7487 3.68 25.75 6.03625 25.75 10.75C25.75 15.4637 25.75 17.8212 24.285 19.285C22.82 20.7487 20.4637 20.75 15.75 20.75H10.75C6.03625 20.75 3.67875 20.75 2.215 19.285C0.75125 17.82 0.75 15.4637 0.75 10.75Z"
                            stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M5.75 5.75L8.44875 8C10.745 9.9125 11.8925 10.8688 13.25 10.8688C14.6075 10.8688 15.7562 9.9125 18.0512 7.99875L20.75 5.75"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </button>
                <button type="button" class="admin-icon-btn" aria-label="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="65 1 20 21" fill="none"
                        aria-hidden="true">
                        <path
                            d="M80.884 10.75C81.465 16.125 83.75 17.75 83.75 17.75H65.75C65.75 17.75 68.75 15.617 68.75 8.15C68.75 6.453 69.382 4.825 70.507 3.625C71.632 2.425 73.16 1.75 74.75 1.75C75.088 1.75 75.4213 1.78 75.75 1.84M76.48 20.75C76.3042 21.0531 76.0518 21.3047 75.7482 21.4795C75.4446 21.6544 75.1004 21.7465 74.75 21.7465C74.3996 21.7465 74.0554 21.6544 73.7518 21.4795C73.4482 21.3047 73.1958 21.0531 73.02 20.75M81.75 7.75C82.5456 7.75 83.3087 7.43393 83.8713 6.87132C84.4339 6.30871 84.75 5.54565 84.75 4.75C84.75 3.95435 84.4339 3.19129 83.8713 2.62868C83.3087 2.06607 82.5456 1.75 81.75 1.75C80.9544 1.75 80.1913 2.06607 79.6287 2.62868C79.0661 3.19129 78.75 3.95435 78.75 4.75C78.75 5.54565 79.0661 6.30871 79.6287 6.87132C80.1913 7.43393 80.9544 7.75 81.75 7.75Z"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="admin-icon-btn" aria-label="Account">
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
            </div>
        </div>
    </div>
</header>
