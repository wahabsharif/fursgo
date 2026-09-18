@props(['profile'])

<aside class="admin-co-sidebar">
    <div class="admin-co-profile-card">
        <div class="admin-co-profile-card-top">
            @if (($profile['flagged'] ?? false))
            <span class="admin-co-profile-flag" aria-label="Flagged" title="Flagged">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 12" fill="none" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.435484 0C0.550981 0 0.661748 0.0441029 0.743418 0.122606C0.825087 0.20111 0.870968 0.307584 0.870968 0.418605V1.02419L1.7849 0.848372C2.82618 0.648431 3.90548 0.743839 4.89135 1.12298L5.00923 1.16819C5.83069 1.48397 6.73478 1.54295 7.5931 1.33674C7.76004 1.29662 7.9343 1.2936 8.10263 1.32792C8.27097 1.36223 8.42896 1.43298 8.56459 1.53478C8.70022 1.63658 8.80992 1.76676 8.88537 1.91543C8.96082 2.06409 9.00002 2.22733 9 2.39274V6.50456C9 7.056 8.60923 7.53712 8.05239 7.67107L7.92813 7.70065C6.81172 7.96882 5.6358 7.89207 4.56735 7.4813C3.73843 7.16272 2.83103 7.08263 1.95561 7.25079L0.870968 7.45954V11.5814C0.870968 11.6924 0.825087 11.7989 0.743418 11.8774C0.661748 11.9559 0.550981 12 0.435484 12C0.319986 12 0.209219 11.9559 0.12755 11.8774C0.0458812 11.7989 0 11.6924 0 11.5814V0.418605C0 0.307584 0.0458812 0.20111 0.12755 0.122606C0.209219 0.0441029 0.319986 0 0.435484 0Z" fill="#FF7F3C" />
                </svg>
            </span>
            @else
            <span></span>
            @endif
            <button type="button" class="admin-co-more-btn" aria-label="More actions">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="4" viewBox="0 0 16 4" fill="none" aria-hidden="true">
                    <circle cx="2" cy="2" r="1.5" fill="#3B3731" />
                    <circle cx="8" cy="2" r="1.5" fill="#3B3731" />
                    <circle cx="14" cy="2" r="1.5" fill="#3B3731" />
                </svg>
            </button>
        </div>

        <div class="admin-co-profile-identity">
            <div class="admin-co-avatar-wrap">
                <img src="{{ $profile['avatar'] }}" alt="{{ $profile['name'] }}" class="admin-co-avatar" width="96" height="96">
            </div>
            <h2 class="admin-co-name">{{ $profile['name'] }}</h2>

            <div class="admin-bp-badge-row">
                <span class="admin-po-status is-{{ $profile['status_class'] ?? $profile['status'] }}">{{ $profile['status_label'] }}</span>
                <span class="admin-po-status admin-bp-type is-{{ $profile['type'] }}">{{ $profile['type_label'] }}</span>
                <span class="admin-po-status admin-bp-insurance is-{{ $profile['insurance_class'] ?? 'active' }}">{{ $profile['insurance_badge'] }}</span>
            </div>

            <div class="admin-co-quick-actions">
                <button type="button" class="admin-co-quick-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                        <path d="M0.375 6.375C0.375 3.54675 0.375 2.13225 1.254 1.254C2.133 0.37575 3.54675 0.375 6.375 0.375H9.375C12.2032 0.375 13.6177 0.375 14.496 1.254C15.3742 2.133 15.375 3.54675 15.375 6.375C15.375 9.20325 15.375 10.6177 14.496 11.496C13.617 12.3742 12.2032 12.375 9.375 12.375H6.375C3.54675 12.375 2.13225 12.375 1.254 11.496C0.37575 10.617 0.375 9.20325 0.375 6.375Z" stroke="#787775" stroke-width="0.75" />
                        <path d="M3.375 3.375L4.99425 4.725C6.372 5.8725 7.0605 6.44625 7.875 6.44625C8.6895 6.44625 9.37875 5.8725 10.7557 4.72425L12.375 3.375" stroke="#787775" stroke-width="0.75" stroke-linecap="round" />
                    </svg>
                    Email
                </button>
                <button type="button" class="admin-co-quick-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                        <path d="M10.4862 11.3638C9.41728 12.0231 8.16058 12.375 6.875 12.375C5.87092 12.3755 4.88038 12.1612 3.98105 11.749C3.32311 11.4463 2.4355 11.8663 1.78767 12.0263L0.375 12.375L0.752722 11.071C0.926055 10.473 1.38106 9.65367 1.05389 9.04633C0.619111 8.24167 0.375 7.335 0.375 6.375C0.375 5.18831 0.756219 4.02828 1.47045 3.04158C2.18468 2.05489 3.19984 1.28585 4.38756 0.831725C5.57528 0.3776 6.88221 0.25878 8.14309 0.490291C9.40396 0.721802 10.5622 1.29325 11.4712 2.13236C12.3802 2.97148 12.9993 4.04057 13.2501 5.20446C13.5009 6.36835 13.3722 7.57474 12.8802 8.6711C12.3882 9.76746 11.5551 10.7045 10.4862 11.3638Z" stroke="#787775" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Message
                </button>
            </div>
        </div>
    </div>

    <div class="admin-co-contact">
        <p class="admin-co-contact-line">
            <span>{{ $profile['email'] }}</span>
            <span class="admin-co-contact-sep" aria-hidden="true">·</span>
            <span>{{ $profile['phone'] }}</span>
        </p>
        @if ($profile['verified'] ?? false)
        <p class="admin-co-verified-line">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                <path d="M4.196 9L0 4.734L1.049 3.668L4.196 6.867L10.951 0L12 1.066L4.196 9Z" fill="#A7C569" />
            </svg>
            Account verified
        </p>
        @endif
    </div>

    <div class="admin-co-stats">
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['earned'] }}</p>
            <p class="admin-co-stat-label">Total earned</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['bookings'] }}</p>
            <p class="admin-co-stat-label">Bookings</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">
                @if (($profile['stats']['rating'] ?? '—') !== '—')
                <span class="admin-bp-rating">
                    {{ $profile['stats']['rating'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                    </svg>
                </span>
                @else
                —
                @endif
            </p>
            <p class="admin-co-stat-label">Avg. rating</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['reviews'] }}</p>
            <p class="admin-co-stat-label">Reviews</p>
        </div>
    </div>

    <div class="admin-co-actions">
        <h3 class="admin-co-actions-title">Admin Actions</h3>
        <x-admin.customer.action-btn variant="password" label="Send password reset email" />
        <x-admin.customer.action-btn variant="suspend" label="Suspend account" />
        <x-admin.customer.action-btn variant="flag" label="Flag account for review" />
        <x-admin.customer.action-btn variant="delete" label="Remove from platform" />
    </div>
</aside>
