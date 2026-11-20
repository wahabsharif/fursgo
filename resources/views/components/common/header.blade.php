@props(['variant' => 'default', 'dashboardNavView' => 'hub', 'forceBusinessPublic' => false])

@php
    use App\Models\GroomerSpacerProfile;
    use App\Models\ServiceArea;
    use App\Support\BusinessPageShell;
    use App\Support\HelpCentre;
    use Illuminate\Support\Facades\Auth;

    $gsp = Auth::guard('groomer_spacer')->user();
    $isGroomerSpacerSession = $gsp instanceof GroomerSpacerProfile;
    $displayName = null;
    $displaySubLabel = null;
    $welcomeBusinessName = null;
    $headerProfileImage = asset('images/user-placeholder.png');
    $isHeaderAuthenticated = $isGroomerSpacerSession || auth()->check();
    $isBusinessLandingRoute = request()->routeIs('business-landing-page');
    $isBusinessHomepageRoute = request()->routeIs('business-homepage-groomer-space-owner');
    $isBusinessHubRoute = request()->routeIs('business-hub');
    $isMarketingHubRoute = request()->routeIs('marketing-hub', 'marketing-hub.*');
    $isAccountSettingsRoute = request()->routeIs('account-settings');
    $isHelpCentreRoute = request()->routeIs('help-and-support', 'help-and-support-search-legacy');
    $isBusinessVerificationRoute = request()->routeIs('business-verification', 'business-verification.*');
    $isBusinessAuthRoute = request()->routeIs(['login-groomer-space', 'signup-groomer-space', 'business-verification', 'business-verification.*']);
    $isBusinessSiteRoute = (bool) $forceBusinessPublic || $isBusinessLandingRoute || $isBusinessHomepageRoute || $isBusinessAuthRoute || ($isHelpCentreRoute && (HelpCentre::audience() === HelpCentre::AUDIENCE_BUSINESS || BusinessPageShell::prefersBusinessChrome()));
    $isForGroomersHostsActive = $isBusinessHomepageRoute || $isBusinessLandingRoute;
    $dashboardLogoHref = $isBusinessVerificationRoute ? route('business-verification') : ($isMarketingHubRoute ? route('marketing-hub') : route('business-hub'));
    $businessHomepageUrl = route('business-homepage-groomer-space-owner');
    $businessHomepageHubUrl = route('business-homepage-groomer-space-owner', ['shell' => 'business-hub']);
    $helpCentreUrl = route('help-and-support');
    $helpCentreBusinessUrl = route('help-and-support', ['chrome' => 'business']);
    $helpCentreHubUrl = route('help-and-support', ['shell' => 'business-hub', 'chrome' => 'business']);
    $businessHubUrl = route('business-hub');
    $marketingHubUrl = route('marketing-hub');
    $accountSettingsUrl = route('account-settings');
    $logoutUrl = route('logout');
    $loginGroomerSpaceUrl = route('login-groomer-space');
    $signupGroomerSpaceUrl = route('signup-groomer-space');
    $switchBusinessUrl = route('business-hub.switch');
    $headerPublicView = 'components.common.header-public';
    $headerBadgeCount = 3;

    if (!$isGroomerSpacerSession && auth()->check()) {
        $authUser = auth()->user();
        $authEmail = (string) ($authUser->email ?? '');
        $emailColumn = 'email';
        $gsp = $authEmail !== '' ? GroomerSpacerProfile::where($emailColumn, $authEmail)->first() : null;
    }

    if ($gsp instanceof GroomerSpacerProfile) {
        $bd = is_array($gsp->business_details) ? $gsp->business_details : [];
        $welcomeBusinessName = $bd['business_name'] ?? null;

        if ($gsp->account_type === 'registered_business') {
            $displayName = $bd['business_name'] ?? $gsp->full_name;
            $displaySubLabel = 'Business Account';
        } elseif ($gsp->account_type === 'freelance') {
            $fd = is_array($gsp->freelance_details) ? $gsp->freelance_details : [];
            $displayName = $gsp->full_name ?: $gsp->email;
            $displaySubLabel = $fd['contact_email'] ?? $gsp->email;
        }

        $bb = is_array($gsp->business_basics) ? $gsp->business_basics : [];
        $avatarPath = trim((string) ($bb['profile_photo_path'] ?? ''));
        if ($avatarPath !== '') {
            $avatarAssetPath = ltrim(str_replace('\\', '/', $avatarPath), '/');
            if (str_starts_with($avatarAssetPath, 'public/')) {
                $avatarAssetPath = substr($avatarAssetPath, strlen('public/'));
            }

            $headerProfileImage = match (true) {
                (bool) filter_var($avatarPath, FILTER_VALIDATE_URL) => $avatarPath,
                file_exists(public_path($avatarAssetPath)) => asset($avatarAssetPath),
                str_starts_with($avatarAssetPath, 'storage/') => asset($avatarAssetPath),
                default => asset('storage/' . $avatarAssetPath),
            };
        }
    }

    if ($isHeaderAuthenticated && !$displayName) {
        if ($gsp instanceof GroomerSpacerProfile) {
            $displayName = $gsp->full_name ?: $gsp->email;
            $displaySubLabel = $displaySubLabel ?? 'Business Account';
        } else {
            $displayName = auth()->user()->name ?? null;
            $displaySubLabel = $displaySubLabel ?? (auth()->user()->email ?? null);
        }
    }

    $headerUserType = $isGroomerSpacerSession ? $gsp->user_type ?? 'groomer' : (auth()->check() ? auth()->user()->user_type ?? 'groomer' : 'groomer');

    $headerPersonName = $gsp instanceof GroomerSpacerProfile ? ($gsp->full_name ?: $displayName) : $displayName;
    $headerNameParts = preg_split('/\s+/', trim((string) $headerPersonName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $headerShortName = match (true) {
        count($headerNameParts) >= 2 => $headerNameParts[0] . ' ' . mb_strtoupper(mb_substr($headerNameParts[count($headerNameParts) - 1], 0, 1)) . '.',
        count($headerNameParts) === 1 => $headerNameParts[0],
        default => $displayName ?: 'Account',
    };
    $headerBusinessLabel = $welcomeBusinessName ?: $displayName;
    $headerEmail = $gsp instanceof GroomerSpacerProfile ? $gsp->email ?? null : auth()->user()->email ?? $displaySubLabel;

    $resolveHeaderProfileImage = static function (?GroomerSpacerProfile $profile): string {
        $fallback = asset('images/user-placeholder.png');
        if (!$profile instanceof GroomerSpacerProfile) {
            return $fallback;
        }

        $bb = is_array($profile->business_basics) ? $profile->business_basics : [];
        $avatarPath = trim((string) ($bb['profile_photo_path'] ?? ''));
        if ($avatarPath === '') {
            return $fallback;
        }

        $avatarAssetPath = ltrim(str_replace('\\', '/', $avatarPath), '/');
        if (str_starts_with($avatarAssetPath, 'public/')) {
            $avatarAssetPath = substr($avatarAssetPath, strlen('public/'));
        }

        return match (true) {
            (bool) filter_var($avatarPath, FILTER_VALIDATE_URL) => $avatarPath,
            file_exists(public_path($avatarAssetPath)) => asset($avatarAssetPath),
            str_starts_with($avatarAssetPath, 'storage/') => asset($avatarAssetPath),
            default => asset('storage/' . $avatarAssetPath),
        };
    };

    $headerAvatar = $isHeaderAuthenticated ? (auth()->check() && auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : $headerProfileImage) : asset('images/user-placeholder.png');

    $headerSwitchBusinesses = [];
    if (!$isBusinessVerificationRoute && $gsp instanceof GroomerSpacerProfile && filled($gsp->email)) {
        $switchProfiles = GroomerSpacerProfile::query()->where('email', $gsp->email)->orderBy('id')->get();

        foreach ($switchProfiles as $profile) {
            if (!$profile instanceof GroomerSpacerProfile) {
                continue;
            }

            $bd = is_array($profile->business_details) ? $profile->business_details : [];
            $bb = is_array($profile->business_basics) ? $profile->business_basics : [];
            $name = trim((string) ($bb['display_name'] ?? ($bd['business_name'] ?? ($profile->full_name ?? 'Business'))));
            $role = strtolower((string) ($profile->user_type ?? 'groomer')) === 'space' ? 'Space Host' : 'Groomer';
            $area = ServiceArea::query()->where('groomer_spacer_id', $profile->id)->value('name');
            $area = is_string($area) && trim($area) !== '' ? trim($area) : null;

            $headerSwitchBusinesses[] = [
                'id' => $profile->id,
                'name' => $name !== '' ? $name : 'Business',
                'meta' => $area ? $role . ' · ' . $area : $role,
                'image' => $resolveHeaderProfileImage($profile),
                'active' => (int) $profile->id === (int) $gsp->id,
            ];
        }
    }
@endphp

@if ($variant === 'dashboard')
    {{-- Dashboard Header --}}
    <header
        class="dashboard-header dashboard-header--{{ strtolower((string) $headerUserType) === 'space' ? 'space' : 'groomer' }}{{ $isBusinessVerificationRoute ? ' dashboard-header--business-verification' : '' }}{{ $isBusinessHubRoute ? ' dashboard-header--business-hub' : '' }}{{ $isMarketingHubRoute ? ' dashboard-header--marketing-hub' : '' }}{{ $isAccountSettingsRoute ? ' dashboard-header--account-settings' : '' }}{{ $isHelpCentreRoute ? ' dashboard-header--help-centre' : '' }}">
        <div class="dashboard-header-container">
            {{-- Main Navigation Bar --}}
            <nav class="navbar dashboard-navbar">
                <div class="dashboard-header-inner">
                    @unless ($isBusinessVerificationRoute)
                        <div class="dashboard-header-brand">
                            <div class="logo-toggle-button d-flex justify-content-between align-items-center">
                                <a href="{{ $dashboardLogoHref }}" wire:navigate
                                    @click="activeSection = @js($isMarketingHubRoute ? 'marketing-hub' : 'business-hub')"
                                    class="dashboard-logo" aria-label="FursGo Business dashboard">
                                    <span class="dashboard-logo-mark" aria-hidden="true">
                                        <img src="{{ asset('images/header/logo-fursgo.svg') }}" alt="" width="98" height="27">
                                    </span>
                                    <span class="dashboard-logo-pill">Business</span>
                                </a>
                                <button type="button" class="menu-toggle" aria-label="Toggle menu">&#9776;</button>
                            </div>
                        </div>
                        <span class="dashboard-header-divider dashboard-header-divider--rail" aria-hidden="true"></span>
                    @endunless
                    <div class="align-items-center dash-menu-items">
                        @if ($isBusinessVerificationRoute)
                            <div
                                class="logo-toggle-button dashboard-vq-brand d-flex justify-content-between align-items-center">
                                <a href="{{ $dashboardLogoHref }}" wire:navigate class="d-inline-flex align-items-end gap-10"
                                    aria-label="FursGo Business">
                                    <img src="{{ asset('images/logo/logo.svg') }}" alt="FursGo" width="145" height="40"
                                        fetchpriority="high">
                                    <span class="logo-b-text">Business</span>
                                </a>
                                <button type="button" class="menu-toggle" aria-label="Toggle menu">&#9776;</button>
                            </div>
                            <div class="dashboard-vq-links">
                                <a href="{{ $businessHomepageUrl }}" class="dashboard-vq-link dashboard-vq-link--hosts"
                                    wire:navigate>For Groomers &amp; Hosts</a>
                                <a href="{{ $helpCentreBusinessUrl }}" class="dashboard-vq-link" wire:navigate>Help Centre</a>
                            </div>
                        @else
                            <div class="dashboard-hub-tabs">
                                <a href="{{ $businessHubUrl }}" wire:navigate @click="activeSection = 'business-hub'"
                                    class="dashboard-hub-tab{{ $isBusinessHubRoute ? ' is-active' : '' }}">
                                    <span class="dashboard-hub-tab-icon dashboard-hub-tab-icon--business"
                                        aria-hidden="true"></span>
                                    Business Hub
                                </a>
                                <a href="{{ $marketingHubUrl }}" wire:navigate @click="activeSection = 'marketing-hub'"
                                    class="dashboard-hub-tab{{ $isMarketingHubRoute ? ' is-active' : '' }}">
                                    <span class="dashboard-hub-tab-icon dashboard-hub-tab-icon--marketing"
                                        aria-hidden="true"></span>
                                    Marketing Hub
                                </a>
                            </div>
                        @endif
                        <div class="session-login-signup-div dashboard-header-icons d-flex align-items-center">
                            <div class="messages-content-tab">
                                <a class="messages-btn header-icon-btn cursor" aria-label="Messages">
                                    @if ($isBusinessVerificationRoute)
                                        <span class="vq-icon vq-icon--mail" aria-hidden="true">
                                            <img src="{{ asset('images/header/vq/icon-mail.svg') }}" alt="" class="vq-icon-mail"
                                                width="25" height="20">
                                            <img src="{{ asset('images/header/vq/icon-mail-flap.svg') }}" alt=""
                                                class="vq-icon-mail-flap" width="15" height="5">
                                        </span>
                                    @else
                                        <span class="header-icon-btn-glyph" aria-hidden="true">
                                            <img src="{{ asset('images/header/icon-header-message.svg') }}" alt="" width="25"
                                                height="20">
                                        </span>
                                        <span class="header-icon-badge">{{ $headerBadgeCount }}</span>
                                    @endif
                                </a>
                                @unless ($isBusinessVerificationRoute)
                                    <div class="messages-notifications">
                                        <div class="dash-drop-head">
                                            <p>Messages</p>
                                            <button type="button" class="dash-drop-mark">Mark all as read</button>
                                        </div>
                                        <div class="dash-drop-list">
                                            <a href="{{ url('/messages_notification/messages') }}" class="dash-msg is-unread" wire:navigate>
                                                <span class="dash-msg-avatar">
                                                    <img src="{{ asset('images/header/dropdown/msg-ring.svg') }}" alt="" class="dash-msg-ring" width="50" height="50">
                                                    <img src="{{ asset('images/header/dropdown/msg-jane.png') }}" alt="" class="dash-msg-photo" width="45" height="45">
                                                </span>
                                                <span class="dash-msg-copy">
                                                    <span class="dash-msg-name">Jane Doe</span>
                                                    <span class="dash-msg-preview">Hi, just a quick reminder about...</span>
                                                </span>
                                                <span class="dash-msg-meta">
                                                    <span class="dash-msg-time">2m</span>
                                                    <img src="{{ asset('images/header/dropdown/msg-dot.svg') }}" alt="" class="dash-msg-dot" width="6" height="6">
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/messages') }}" class="dash-msg is-unread" wire:navigate>
                                                <span class="dash-msg-avatar">
                                                    <img src="{{ asset('images/header/dropdown/msg-ring.svg') }}" alt="" class="dash-msg-ring" width="50" height="50">
                                                    <img src="{{ asset('images/header/dropdown/msg-jamie.png') }}" alt="" class="dash-msg-photo" width="45" height="45">
                                                </span>
                                                <span class="dash-msg-copy">
                                                    <span class="dash-msg-name">Jamie Dalton</span>
                                                    <span class="dash-msg-preview">Is there parking near the studio?</span>
                                                </span>
                                                <span class="dash-msg-meta">
                                                    <span class="dash-msg-time">2m</span>
                                                    <img src="{{ asset('images/header/dropdown/msg-dot.svg') }}" alt="" class="dash-msg-dot" width="6" height="6">
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/messages') }}" class="dash-msg is-unread" wire:navigate>
                                                <span class="dash-msg-initials">
                                                    <img src="{{ asset('images/header/dropdown/msg-avatar-jd.svg') }}" alt="" width="50" height="50">
                                                </span>
                                                <span class="dash-msg-copy">
                                                    <span class="dash-msg-name">Elliot Ng</span>
                                                    <span class="dash-msg-preview">Can I reschedule my appoint...</span>
                                                </span>
                                                <span class="dash-msg-meta">
                                                    <span class="dash-msg-time">2m</span>
                                                    <img src="{{ asset('images/header/dropdown/msg-dot.svg') }}" alt="" class="dash-msg-dot" width="6" height="6">
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/messages') }}" class="dash-msg" wire:navigate>
                                                <span class="dash-msg-initials">
                                                    <img src="{{ asset('images/header/dropdown/msg-avatar-dc.svg') }}" alt="" width="50" height="50">
                                                    <span>DC</span>
                                                </span>
                                                <span class="dash-msg-copy">
                                                    <span class="dash-msg-name">Cheryl Sommer</span>
                                                    <span class="dash-msg-preview">Lorem ipsum dolot sit amet.</span>
                                                </span>
                                                <span class="dash-msg-meta">
                                                    <span class="dash-msg-time">Yesterday</span>
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/messages') }}" class="dash-msg" wire:navigate>
                                                <span class="dash-msg-initials">
                                                    <img src="{{ asset('images/header/dropdown/msg-avatar-cs.svg') }}" alt="" width="50" height="50">
                                                    <span>CS</span>
                                                </span>
                                                <span class="dash-msg-copy">
                                                    <span class="dash-msg-name">Ramesh K.</span>
                                                    <span class="dash-msg-preview">Lorem ipsum dolot sit amet.</span>
                                                </span>
                                                <span class="dash-msg-meta">
                                                    <span class="dash-msg-time">2d</span>
                                                </span>
                                            </a>
                                        </div>
                                        <a href="{{ url('/messages_notification/messages') }}" class="dash-drop-foot" wire:navigate>View all messages →</a>
                                    </div>
                                @endunless
                            </div>

                            <div class="notification-content-tab">
                                <a class="notification-btn header-icon-btn cursor" aria-label="Notifications">
                                    @if ($isBusinessVerificationRoute)
                                        <span class="vq-icon vq-icon--bell" aria-hidden="true">
                                            <img src="{{ asset('images/header/vq/icon-bell.svg') }}" alt="" width="19"
                                                height="20">
                                        </span>
                                    @else
                                        <span class="header-icon-btn-glyph" aria-hidden="true">
                                            <img src="{{ asset('images/header/icon-header-bell.svg') }}" alt="" width="18"
                                                height="18">
                                        </span>
                                        <span class="header-icon-badge">{{ $headerBadgeCount }}</span>
                                    @endif
                                </a>
                                @unless ($isBusinessVerificationRoute)
                                    <div class="header-notifications">
                                        <div class="dash-drop-head">
                                            <p>Notifications</p>
                                        </div>
                                        <div class="dash-drop-list">
                                            <a href="{{ url('/messages_notification/notifications') }}" class="dash-note is-unread" wire:navigate>
                                                <span class="dash-note-icon dash-note-icon--booking">
                                                    <img src="{{ asset('images/header/dropdown/note-calendar.svg') }}" alt="" width="22" height="20">
                                                </span>
                                                <span class="dash-note-copy">
                                                    <p class="dash-note-text"><strong>Booking Confirmed</strong> Your rescheduled booking with Jane Smith</p>
                                                    <p class="dash-note-time">8 minutes ago</p>
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/notifications') }}" class="dash-note is-unread" wire:navigate>
                                                <img src="{{ asset('images/header/dropdown/note-review.svg') }}" alt="" class="dash-note-tile" width="50" height="50">
                                                <span class="dash-note-copy">
                                                    <p class="dash-note-text"><strong>New Review</strong> Isla Marketing left you a 5-star review</p>
                                                    <p class="dash-note-time">1 hour ago</p>
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/notifications') }}" class="dash-note is-unread" wire:navigate>
                                                <span class="dash-note-icon dash-note-icon--settings">
                                                    <img src="{{ asset('images/header/dropdown/note-settings.svg') }}" alt="" width="21" height="19">
                                                </span>
                                                <span class="dash-note-copy">
                                                    <p class="dash-note-text"><strong>Settings</strong> System update scheduled for Monday 12 Oct at 02:00...</p>
                                                    <p class="dash-note-time">5 hours ago</p>
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/notifications') }}" class="dash-note" wire:navigate>
                                                <img src="{{ asset('images/header/dropdown/note-payout.svg') }}" alt="" class="dash-note-tile" width="50" height="50">
                                                <span class="dash-note-copy">
                                                    <p class="dash-note-text"><strong>Pay-out</strong> £425.00 is on its way to your bank</p>
                                                    <p class="dash-note-time">Yesterday, 16:35</p>
                                                </span>
                                            </a>
                                            <a href="{{ url('/messages_notification/notifications') }}" class="dash-note" wire:navigate>
                                                <span class="dash-note-icon dash-note-icon--account">
                                                    <img src="{{ asset('images/header/dropdown/note-bell.svg') }}" alt="" width="19" height="20">
                                                </span>
                                                <span class="dash-note-copy">
                                                    <p class="dash-note-text"><strong>Account update</strong> Your profile has been updated successfully.</p>
                                                    <p class="dash-note-time">Last month</p>
                                                </span>
                                            </a>
                                        </div>
                                        <a href="{{ url('/messages_notification/notifications') }}" class="dash-drop-foot" wire:navigate>View all notifications →</a>
                                    </div>
                                @endunless
                            </div>

                            @unless ($isBusinessVerificationRoute)
                                <span class="dashboard-header-divider" aria-hidden="true"></span>
                            @endunless
                            <div class="user-content-tab">
                                <button type="button"
                                    class="user-btn {{ $isBusinessVerificationRoute ? 'header-icon-btn' : 'header-user-chip' }} cursor"
                                    aria-label="Account menu" aria-haspopup="true">
                                    @if ($isBusinessVerificationRoute)
                                        <span class="vq-icon vq-icon--user" aria-hidden="true">
                                            <img src="{{ asset('images/header/vq/icon-user-head.svg') }}" alt=""
                                                class="vq-icon-user-head" width="9" height="9">
                                            <img src="{{ asset('images/header/vq/icon-user-body.svg') }}" alt=""
                                                class="vq-icon-user-body" width="18" height="8">
                                        </span>
                                    @else
                                        <span class="header-user-avatar">
                                            <img src="{{ $headerAvatar }}" alt="" class="header-user-avatar-img" width="45"
                                                height="45">
                                            @if ($isHeaderAuthenticated)
                                                <img src="{{ asset('images/header/icon-verified-badge.svg') }}" alt=""
                                                    class="header-user-verified" width="16" height="18">
                                            @endif
                                        </span>
                                        <span class="header-user-copy">
                                            <span
                                                class="header-user-name">{{ $isHeaderAuthenticated ? $headerShortName : 'Guest' }}</span>
                                            <span
                                                class="header-user-business">{{ $isHeaderAuthenticated ? ($headerBusinessLabel ?: 'Business account') : 'Please log in' }}</span>
                                        </span>
                                        <span class="header-user-chevron" aria-hidden="true">
                                            <img src="{{ asset('images/header/icon-chevron-down.svg') }}" alt="" width="12"
                                                height="8">
                                        </span>
                                    @endif
                                </button>
                                <div class="user-profile-options">
                                    <div class="user-profile-image">
                                        <div class="user-profile-avatar">
                                            <img src="{{ $headerAvatar }}" alt="" class="user-profile-avatar-img">
                                            @if ($isHeaderAuthenticated)
                                                <img src="{{ asset('images/header/icon-verified-badge.svg') }}" alt=""
                                                    class="user-profile-verified" width="16" height="18">
                                            @endif
                                        </div>
                                        <div class="name-email">
                                            <p class="user-profile-name"
                                                title="{{ $isHeaderAuthenticated ? $headerShortName : 'Guest' }}">
                                                {{ $isHeaderAuthenticated ? $headerShortName : 'Guest' }}
                                            </p>
                                            <p class="user-profile-email">
                                                {{ $isHeaderAuthenticated ? $headerEmail : 'Please log in' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($isHeaderAuthenticated && count($headerSwitchBusinesses) > 0)
                                        <div class="profile-switch-business">
                                            <p class="profile-switch-label">Switch business</p>
                                            @foreach ($headerSwitchBusinesses as $business)
                                                @if ($business['active'])
                                                    <div class="profile-business-item is-active">
                                                        <img src="{{ $business['image'] }}" alt="" class="profile-business-avatar"
                                                            width="23" height="23">
                                                        <span class="profile-business-copy">
                                                            <span class="profile-business-name">{{ $business['name'] }}</span>
                                                            <span class="profile-business-meta">{{ $business['meta'] }}</span>
                                                        </span>
                                                        <svg class="profile-business-check" xmlns="http://www.w3.org/2000/svg"
                                                            width="10" height="8" viewBox="0 0 10 8" fill="none" aria-hidden="true">
                                                            <path d="M1 4.5L3.66667 7L9 1" stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <form method="POST" action="{{ $switchBusinessUrl }}"
                                                        class="profile-business-switch-form">
                                                        @csrf
                                                        <input type="hidden" name="profile_id" value="{{ $business['id'] }}">
                                                        <button type="submit" class="profile-business-item">
                                                            <img src="{{ $business['image'] }}" alt="" class="profile-business-avatar"
                                                                width="23" height="23">
                                                            <span class="profile-business-copy">
                                                                <span class="profile-business-name">{{ $business['name'] }}</span>
                                                                <span class="profile-business-meta">{{ $business['meta'] }}</span>
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach
                                            <a href="{{ $signupGroomerSpaceUrl }}" class="profile-add-business" wire:navigate>
                                                <span class="profile-add-business-icon" aria-hidden="true">
                                                    <img src="{{ asset('images/header/icon-add-business.svg') }}" alt=""
                                                        width="23" height="23">
                                                </span>
                                                Add a business
                                            </a>
                                        </div>
                                    @endif
                                    <div class="profile-menu">
                                        <a href="{{ $accountSettingsUrl }}" class="profile-item"
                                            :class="{ 'profile-item--active': @js($isAccountSettingsRoute) }"
                                            @click="activeSection = 'account-settings'" wire:navigate>
                                            <span class="profile-item-icon">
                                                <img src="{{ asset('images/header/icon-account-settings-sm.svg') }}" alt=""
                                                    width="15" height="16">
                                            </span>
                                            <span>Account Settings</span>
                                        </a>
                                        <a href="{{ $helpCentreHubUrl }}" class="profile-item"
                                            :class="{ 'profile-item--active': @js($isHelpCentreRoute) }"
                                            @click="activeSection = 'help-and-support'" wire:navigate>
                                            <span class="profile-item-icon">
                                                <img src="{{ asset('images/header/icon-help-support-sm.svg') }}" alt=""
                                                    width="13" height="15">
                                            </span>
                                            <span>Help Centre</span>
                                        </a>
                                    </div>
                                    @if ($isHeaderAuthenticated)
                                        <div class="logout-option">
                                            <form method="POST" action="{{ $logoutUrl }}">
                                                @csrf
                                                <input type="hidden" name="redirect" value="login-groomer-space">
                                                <button type="submit" class="profile-item profile-item--logout">
                                                    <span class="profile-item-icon">
                                                        <img src="{{ asset('images/header/icon-log-out-sm.svg') }}" alt=""
                                                            width="12" height="16">
                                                    </span>
                                                    <span>Log out</span>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="logout-option">
                                            <a href="{{ $loginGroomerSpaceUrl }}" class="profile-item profile-item--logout"
                                                wire:navigate>
                                                <span class="profile-item-icon">
                                                    <img src="{{ asset('images/header/icon-log-out-sm.svg') }}" alt=""
                                                        width="12" height="16">
                                                </span>
                                                <span>Log in</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        </div>

        <style>
            .dashboard-header {
                --dashboard-sidebar-col: 261px;
                --profile-item-active-bg: #fffbf4;
                --profile-check-color: #ffc46e;
                position: sticky;
                top: 0;
                z-index: 1020;
                width: 100%;
                background: #fff;
                overflow: visible;
                border-bottom: 1px solid #e2e2e2;
            }

            .dashboard-header--space {
                --profile-item-active-bg: #ffeeeb;
                --profile-check-color: #ffa899;
            }

            .dashboard-header .dashboard-header-container {
                position: relative;
                z-index: 2;
                width: 100%;
                max-width: 1440px;
                margin: 0 auto;
                padding: 0 1rem;
                box-sizing: border-box;
                overflow: visible;
            }

            .dashboard-header .dashboard-header-container .dashboard-header-inner {
                display: grid;
                grid-template-columns: var(--dashboard-sidebar-col) 1px minmax(0, 1fr);
                align-items: stretch;
                width: 100%;
                max-width: 100%;
                min-height: 85px;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            .dashboard-navbar {
                position: relative;
                z-index: 1000;
                background: transparent;
                min-height: 85px;
                width: 100%;
                padding: 0 !important;
                display: flex;
                align-items: stretch;
            }

            .dash-menu-items {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                min-width: 0;
                gap: 24px;
                padding-left: 24px;
                box-sizing: border-box;
            }

            .dashboard-header-brand {
                display: flex;
                align-items: center;
                min-width: 0;
            }

            .dashboard-header-left {
                display: flex;
                align-items: center;
                gap: 3rem;
                min-width: 0;
            }

            .dashboard-header .logo-toggle-button {
                flex-shrink: 0;
                gap: 12px;
            }

            .dashboard-logo {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                text-decoration: none;
            }

            .dashboard-logo-mark {
                width: 98px;
                height: 27px;
                overflow: clip;
                flex-shrink: 0;
            }

            .dashboard-logo-mark img {
                width: 100%;
                height: 100%;
                display: block;
            }

            .dashboard-logo-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 74px;
                height: 27px;
                padding: 0;
                border-radius: 10px;
                background: #f7f4ec;
                color: #706a62;
                font-family: Lato;
                font-size: 14px;
                font-style: normal;
                font-weight: 600;
                line-height: normal;
                white-space: nowrap;
            }

            .dashboard-header-divider {
                width: 1px;
                height: 35px;
                background: #e2e2e2;
                flex-shrink: 0;
            }

            .dashboard-header-divider--rail {
                height: 35px;
                align-self: center;
            }

            .dashboard-hub-tabs {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .dashboard-hub-tab {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                height: 35px;
                padding: 0 12px;
                border-radius: 10px;
                background: #fff;
                color: #3b3731;
                font-family: Lato;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                text-decoration: none;
                white-space: nowrap;
                transition: color 0.25s ease, background-color 0.25s ease, font-weight 0.25s ease;
            }

            .dashboard-hub-tab-icon {
                display: inline-block;
                flex-shrink: 0;
                width: 12px;
                height: 12px;
                background: currentColor;
                -webkit-mask-repeat: no-repeat;
                mask-repeat: no-repeat;
                -webkit-mask-position: center;
                mask-position: center;
                -webkit-mask-size: contain;
                mask-size: contain;
            }

            .dashboard-hub-tab-icon--business {
                -webkit-mask-image: url('{{ asset('images/header/icon-hub-business.svg') }}');
                mask-image: url('{{ asset('images/header/icon-hub-business.svg') }}');
            }

            .dashboard-hub-tab-icon--marketing {
                width: 13px;
                height: 13px;
                -webkit-mask-image: url('{{ asset('images/header/icon-hub-marketing.svg') }}');
                mask-image: url('{{ asset('images/header/icon-hub-marketing.svg') }}');
            }

            .dashboard-hub-tab.is-active {
                border-radius: 10px;
                background: #fffbf4;
                color: #ffc97a;
                font-family: Lato;
                font-size: 14px;
                font-style: normal;
                font-weight: 600;
                line-height: normal;
            }

            .dashboard-header--space .dashboard-hub-tab.is-active {
                background: #fff7f5;
                color: #ffa899;
            }

            .dashboard-hub-tab:hover {
                background: #fffbf4;
                color: #ffc97a;
                text-decoration: none;
            }

            .dashboard-hub-tab.is-active:hover {
                background: #fffbf4;
                color: #ffc97a;
            }

            .dashboard-header--space .dashboard-hub-tab:hover,
            .dashboard-header--space .dashboard-hub-tab.is-active:hover {
                background: #fff7f5;
                color: #ffa899;
            }

            .dashboard-header-icons {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
            }

            .dashboard-header-icons .dashboard-header-divider {
                margin: 0 6px;
            }

            .dashboard-header-icons .messages-content-tab,
            .dashboard-header-icons .notification-content-tab,
            .dashboard-header-icons .user-content-tab {
                position: relative;
                z-index: 1000;
            }

            .dashboard-header-icons .header-icon,
            .dashboard-header-icons .messages-btn,
            .dashboard-header-icons .notification-btn,
            .dashboard-header-icons .user-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                pointer-events: auto;
                z-index: 11;
                position: relative;
                user-select: none;
                background: none;
                border: none;
                padding: 0;
                appearance: none;
            }

            .header-icon-btn {
                width: 45px;
                height: 45px;
                border: 1px solid #f0ead9 !important;
                border-radius: 5px;
                background: #fff !important;
                transition: border-color 0.25s ease, background-color 0.25s ease, transform 0.25s ease;
            }

            .dashboard-header:not(.dashboard-header--business-verification) .header-icon-btn:hover {
                border-color: #ffc97a !important;
                background: #fffbf4 !important;
                transform: translateY(-1px);
            }

            .header-icon-btn-glyph {
                width: 25px;
                height: 20px;
                overflow: clip;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .notification-btn .header-icon-btn-glyph {
                width: 18px;
                height: 18px;
            }

            .header-icon-btn-glyph img {
                width: 100%;
                height: 100%;
                display: block;
            }

            .header-icon-badge {
                position: absolute;
                top: -3px;
                right: -3px;
                min-width: 14px;
                height: 14px;
                padding: 0 3px;
                border-radius: 50%;
                background: #fe6f56;
                color: #fff;
                font-family: Lato;
                font-size: 10px;
                font-weight: 600;
                line-height: 14px;
                text-align: center;
                box-sizing: border-box;
            }

            .header-user-chip {
                gap: 10px;
                padding: 0 !important;
                background: transparent;
                max-width: 260px;
                transition: opacity 0.25s ease;
            }

            .header-user-chip:hover {
                opacity: 0.82;
            }

            .header-user-avatar {
                position: relative;
                width: 45px;
                height: 45px;
                flex-shrink: 0;
            }

            .header-user-avatar-img {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
                display: block;
            }

            .header-user-verified {
                position: absolute;
                top: 0;
                left: -1px;
                width: 16px;
                height: 18px;
                pointer-events: none;
            }

            .header-user-copy {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                min-width: 0;
                text-align: left;
            }

            .header-user-name {
                color: #3b3731;
                font-family: "Playfair Display", serif;
                font-size: 16px;
                font-weight: 700;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 180px;
            }

            .header-user-business {
                color: #9d9b98;
                font-family: Lato;
                font-size: 16px;
                font-weight: 400;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 180px;
            }

            .header-user-chevron {
                width: 12px;
                height: 8px;
                overflow: clip;
                flex-shrink: 0;
                transform: rotate(180deg);
            }

            .header-user-chevron img {
                width: 100%;
                height: 100%;
                display: block;
            }

            .dashboard-header .messages-notifications,
            .dashboard-header .header-notifications {
                position: absolute;
                right: 0;
                top: calc(100% + 18px);
                z-index: 1001;
                width: 339px;
                max-width: calc(100vw - 32px);
                height: auto;
                margin: 0;
                border-radius: 0 0 10px 10px;
                border: 1px solid #e2e2e2;
                background: #fff;
                box-sizing: border-box;
                box-shadow: 0 2px 30px rgba(59, 55, 49, 0.2);
                overflow: visible;
                padding: 0;
                display: block;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform: translateY(-8px) scale(0.98);
                transform-origin: top right;
                transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0.22s;
            }

            .dashboard-header .messages-notifications.is-open,
            .dashboard-header .header-notifications.is-open {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
                transform: translateY(0) scale(1);
                transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0s;
            }

            .dashboard-header .dash-drop-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 20px 20px 16px;
            }

            .dashboard-header .dash-drop-head p {
                margin: 0;
                color: #3b3731;
                font-family: Lato, sans-serif;
                font-size: 16px;
                font-weight: 700;
                line-height: 1;
            }

            .dashboard-header .messages-notifications .dash-drop-head p {
                font-weight: 600;
            }

            .dashboard-header .dash-drop-mark {
                border: 0;
                background: transparent;
                padding: 0;
                color: #f9c45c;
                font-family: Lato, sans-serif;
                font-size: 14px;
                font-weight: 600;
                line-height: 1;
                cursor: pointer;
            }

            .dashboard-header .dash-drop-list {
                display: flex;
                flex-direction: column;
            }

            .dashboard-header .header-notifications .dash-drop-list {
                gap: 10px;
                padding: 0 19px 20px;
            }

            .dashboard-header .dash-msg {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                min-height: 70px;
                padding: 10px 20px;
                box-sizing: border-box;
                text-decoration: none;
                color: inherit;
            }

            .dashboard-header .dash-msg.is-unread {
                background: #fff4e4;
            }

            .dashboard-header .dash-msg-avatar,
            .dashboard-header .dash-msg-initials {
                position: relative;
                width: 50px;
                height: 50px;
                flex-shrink: 0;
            }

            .dashboard-header .dash-msg-ring,
            .dashboard-header .dash-msg-initials img {
                display: block;
                width: 50px;
                height: 50px;
            }

            .dashboard-header .dash-msg-photo {
                position: absolute;
                left: 2.5px;
                top: 2.5px;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
                display: block;
            }

            .dashboard-header .dash-msg-initials span {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fdfdfd;
                font-family: Lato, sans-serif;
                font-size: 20px;
                font-weight: 800;
                line-height: 1;
            }

            .dashboard-header .dash-msg-copy,
            .dashboard-header .dash-note-copy {
                min-width: 0;
                flex: 1;
            }

            .dashboard-header .dash-msg-name,
            .dashboard-header .dash-msg-preview {
                display: block;
                margin: 0;
                color: #3b3731;
                font-family: Lato, sans-serif;
                line-height: 1.15;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .dashboard-header .dash-msg-name {
                font-size: 16px;
                font-weight: 600;
            }

            .dashboard-header .dash-msg-preview {
                font-size: 14px;
                font-weight: 400;
            }

            .dashboard-header .dash-msg-meta {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
                flex-shrink: 0;
                margin-left: 8px;
            }

            .dashboard-header .dash-msg-time,
            .dashboard-header .dash-note-time {
                margin: 0;
                color: #9d9b98;
                font-family: Lato, sans-serif;
                font-size: 12px;
                font-weight: 400;
                line-height: 20px;
                white-space: nowrap;
            }

            .dashboard-header .dash-msg-dot {
                display: block;
                width: 6px;
                height: 6px;
                flex: 0 0 6px;
            }

            .dashboard-header .dash-note {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                text-decoration: none;
                color: inherit;
            }

            .dashboard-header .dash-note-icon {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .dashboard-header .dash-note-icon--booking {
                background: #f7faf1;
            }

            .dashboard-header .dash-note-icon--settings {
                background: #ffeeeb;
            }

            .dashboard-header .dash-note-icon--account {
                background: #fbfaf6;
            }

            .dashboard-header .dash-note-icon img {
                display: block;
                width: auto;
                height: auto;
                max-width: 22px;
                max-height: 21px;
            }

            .dashboard-header .dash-note-tile {
                display: block;
                width: 50px;
                height: 50px;
                flex: 0 0 50px;
            }

            .dashboard-header .dash-note-text {
                margin: 0;
                color: #3b3731;
                font-family: Lato, sans-serif;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.15;
            }

            .dashboard-header .dash-note-text strong {
                font-size: 16px;
                font-weight: 600;
            }

            .dashboard-header .dash-drop-foot {
                display: block;
                margin: 0 20px;
                border-top: 1px solid #e2e2e2;
                padding: 20px 0 18px;
                text-align: center;
                color: #3b3731;
                font-family: Lato, sans-serif;
                font-size: 14px;
                font-weight: 600;
                line-height: 1;
                text-decoration: none;
            }

            .dashboard-header .user-profile-options {
                position: absolute;
                right: 0;
                top: calc(100% + 18px);
                z-index: 1001;
                width: 339px;
                max-width: calc(100vw - 32px);
                height: auto;
                border-radius: 0 0 10px 10px;
                border: 1px solid #e2e2e2;
                background: #fff;
                display: block;
                overflow: visible;
                box-sizing: border-box;
                padding: 19px 19px 15px;
                box-shadow: 0 2px 30px rgba(59, 55, 49, 0.2);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform: translateY(-8px) scale(0.98);
                transform-origin: top right;
                transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0.22s;
            }

            .dashboard-header .user-profile-options.is-open {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
                transform: translateY(0) scale(1);
                transition: opacity 0.22s ease, transform 0.22s ease, visibility 0s linear 0s;
            }

            .dashboard-header .user-profile-options .user-profile-image {
                display: flex;
                align-items: center;
                gap: 20px;
                border-bottom: 1px solid #e2e2e2;
                padding: 0 0 20px;
                min-height: 0;
            }

            .dashboard-header .user-profile-options .user-profile-avatar {
                position: relative;
                width: 45px;
                height: 45px;
                flex-shrink: 0;
            }

            .dashboard-header .user-profile-options .user-profile-avatar-img {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                object-fit: cover;
                display: block;
            }

            .dashboard-header .user-profile-options .user-profile-verified {
                position: absolute;
                top: 0;
                left: -1px;
                width: 16px;
                height: 18px;
                pointer-events: none;
            }

            .dashboard-header .user-profile-options .name-email {
                min-width: 0;
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .dashboard-header .user-profile-options .name-email p {
                margin: 0;
                line-height: 1.15;
            }

            .dashboard-header .user-profile-options .user-profile-name {
                color: #3b3731;
                font-family: "Playfair Display", serif;
                font-size: 16px;
                font-weight: 700;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .dashboard-header .user-profile-options .user-profile-email {
                color: #9d9b98;
                font-family: Lato;
                font-size: 16px;
                font-weight: 400;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .profile-switch-business {
                padding: 16px 0 10px;
                border-bottom: 1px solid #e2e2e2;
            }

            .profile-switch-label {
                margin: 0 0 8px;
                color: #9d9b98;
                font-family: Lato;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                text-transform: uppercase;
            }

            .profile-business-switch-form {
                margin: 0;
            }

            .profile-business-item {
                display: flex;
                align-items: center;
                gap: 10px;
                width: 100%;
                height: 43px;
                padding: 0 20px 0 10px;
                border: 0;
                border-radius: 10px;
                background: transparent;
                text-align: left;
                cursor: pointer;
                appearance: none;
                box-sizing: border-box;
                transition: background-color 0.2s ease;
            }

            .profile-business-item.is-active {
                background: var(--profile-item-active-bg);
            }

            .profile-business-item:hover,
            .profile-business-item:focus-visible {
                border-radius: 10px;
                background: var(--profile-item-active-bg);
            }

            .profile-business-avatar {
                width: 23px;
                height: 23px;
                border-radius: 50%;
                object-fit: cover;
                flex-shrink: 0;
            }

            .profile-business-copy {
                min-width: 0;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .profile-business-name,
            .profile-business-meta {
                font-family: Lato;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .profile-business-name {
                color: #3b3731;
            }

            .profile-business-meta {
                color: #9d9b98;
            }

            .profile-business-check {
                width: 10px;
                height: 8px;
                flex-shrink: 0;
                color: var(--profile-check-color);
                display: block;
            }

            .profile-add-business {
                display: flex;
                align-items: center;
                gap: 10px;
                width: 100%;
                height: 43px;
                padding: 0 10px;
                color: #ffc46e;
                font-family: Lato;
                font-size: 14px;
                font-weight: 600;
                line-height: 1;
                text-decoration: none;
                box-sizing: border-box;
            }

            .profile-add-business-icon {
                width: 23px;
                height: 23px;
                overflow: clip;
                flex-shrink: 0;
            }

            .profile-add-business-icon img {
                width: 100%;
                height: 100%;
                display: block;
            }

            .dashboard-header .user-profile-options .profile-menu {
                padding: 10px 0 0;
            }

            .dashboard-header .user-profile-options .profile-item {
                display: flex;
                align-items: center;
                gap: 10px;
                height: 27px;
                padding: 0;
                border-radius: 10px;
                color: #3b3731;
                font-family: Lato;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                text-decoration: none;
                background: transparent;
                border: 0;
                width: 100%;
                text-align: left;
                cursor: pointer;
                box-sizing: border-box;
                appearance: none;
                transition: background-color 0.2s ease;
            }

            .dashboard-header .user-profile-options .profile-item-icon {
                flex-shrink: 0;
                width: 15px;
                height: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .dashboard-header .user-profile-options .profile-item-icon img {
                width: auto;
                height: auto;
                max-width: 15px;
                max-height: 16px;
                object-fit: contain;
                display: block;
            }

            .dashboard-header .user-profile-options .profile-item:hover,
            .dashboard-header .user-profile-options .profile-item--active {
                border-radius: 10px;
                background: var(--profile-item-active-bg);
            }

            .dashboard-header .user-profile-options .profile-item--logout:hover,
            .dashboard-header .user-profile-options .profile-item--logout.profile-item--active {
                background: transparent;
            }

            .dashboard-header .user-profile-options .logout-option {
                border-top: 1px solid #e2e2e2;
                margin-top: 0;
                padding: 10px 0 0;
            }

            .dashboard-header .user-profile-options .logout-option form {
                margin: 0;
            }

            .dashboard-header .user-profile-options .profile-item--logout,
            .dashboard-header .user-profile-options .profile-item--logout span {
                color: #fe6f56;
            }

            .dashboard-header.scrolled nav.navbar {
                padding: 0 !important;
            }

            .dashboard-header.dashboard-header--business-verification.scrolled nav.navbar {
                padding: 50px 0 40px !important;
            }

            .dashboard-header.dashboard-header--business-verification {
                border-bottom: none;
            }

            .dashboard-header.dashboard-header--business-verification .dashboard-header-container {
                max-width: 1400px;
            }

            .dashboard-header.dashboard-header--business-verification .dashboard-navbar {
                min-height: 0;
                padding: 50px 0 40px !important;
                align-items: center;
            }

            .dashboard-header.dashboard-header--business-verification .dashboard-header-inner {
                display: flex;
                align-items: center;
                min-height: 40px;
            }

            .dashboard-header.dashboard-header--business-verification .dash-menu-items {
                position: relative;
                padding-left: 0;
                gap: 24px;
            }

            .dashboard-vq-brand {
                flex-shrink: 0;
                z-index: 1;
            }

            .dashboard-vq-links {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                align-items: center;
                gap: 50px;
                z-index: 1;
            }

            .dashboard-vq-links a {
                color: #3b3731;
                text-decoration: none;
                font-family: Lato;
                font-size: 18px;
                line-height: 22px;
                font-weight: 500;
                white-space: nowrap;
                transition: color 0.25s ease;
            }

            .dashboard-vq-links a.dashboard-vq-link--hosts {
                font-weight: 600;
            }

            .dashboard-vq-links a:hover {
                color: #ffc97a;
            }

            .dashboard-header--business-verification .dashboard-header-icons {
                gap: 40px;
                margin-left: auto;
                z-index: 1;
            }

            .dashboard-header--business-verification .header-icon-btn {
                width: auto;
                height: auto;
                min-width: 0;
                min-height: 0;
                padding: 0;
                border: none !important;
                border-radius: 0;
                background: transparent !important;
            }

            .vq-icon {
                position: relative;
                display: block;
                overflow: visible;
            }

            .vq-icon img {
                display: block;
            }

            .vq-icon--bell img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .vq-icon--mail {
                width: 25px;
                height: 20px;
            }

            .vq-icon-mail {
                position: absolute;
                inset: 0;
                width: 25px;
                height: 20px;
            }

            .vq-icon-mail-flap {
                position: absolute;
                left: 5px;
                top: 5px;
                width: 15px;
                height: 5px;
            }

            .vq-icon--bell {
                width: 19px;
                height: 20px;
            }

            .vq-icon--user {
                width: 18px;
                height: 21px;
            }

            .vq-icon-user-head {
                position: absolute;
                left: 4px;
                top: 0;
                width: 9px;
                height: 9px;
            }

            .vq-icon-user-body {
                position: absolute;
                left: 0;
                top: 12px;
                width: 18px;
                height: 8px;
            }

            .dashboard-header .menu-toggle {
                display: none !important;
            }

            @media (max-width: 1199.98px) {
                .dashboard-header:not(.dashboard-header--business-verification) .dashboard-header-container {
                    padding-left: 24px;
                    padding-right: 24px;
                }

                .dashboard-header.dashboard-header--business-verification .dashboard-header-container {
                    padding-left: 24px;
                    padding-right: 24px;
                }

                .header-user-business {
                    display: none;
                }

                .dashboard-vq-links {
                    position: static;
                    transform: none;
                    gap: 1.5rem;
                }

                .dashboard-header--business-verification .dashboard-header-icons {
                    gap: 24px;
                }
            }

            @media (max-width: 991.98px) {
                .dashboard-header .dashboard-header-container .dashboard-header-inner {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 12px;
                }

                .dashboard-header-brand {
                    flex: 0 0 auto;
                }

                .dash-menu-items {
                    padding-left: 0;
                    flex: 1 1 auto;
                }

                .dashboard-header-left {
                    flex-wrap: wrap;
                    gap: 12px;
                }

                .dashboard-header-divider {
                    display: none;
                }
            }

            @media (max-width: 767.98px) {
                .dashboard-header:not(.dashboard-header--business-verification) .dashboard-header-container {
                    padding-left: 16px;
                    padding-right: 16px;
                }

                .dashboard-header.dashboard-header--business-verification .dashboard-header-container {
                    padding-left: 16px;
                    padding-right: 16px;
                }

                .dashboard-header.dashboard-header--business-verification .dashboard-navbar,
                .dashboard-header.dashboard-header--business-verification.scrolled nav.navbar {
                    padding: 24px 0 !important;
                }

                .dashboard-vq-links {
                    display: none;
                }

                .header-user-copy {
                    display: none;
                }

                .header-user-chevron {
                    display: none;
                }

                .dashboard-header:not(.dashboard-header--business-verification) .header-icon-btn {
                    width: 40px;
                    height: 40px;
                }

                .header-user-avatar,
                .header-user-avatar-img {
                    width: 40px;
                    height: 40px;
                }
            }

            .dashboard-shell.dashboard-content-loading::after {
                content: '';
                position: fixed;
                top: 85px;
                left: 0;
                right: 0;
                bottom: 0;
                width: 100%;
                z-index: 40;
                background: rgba(255, 255, 255, 0.42);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                cursor: wait;
                pointer-events: auto;
            }

            .dashboard-shell--business-verification.dashboard-content-loading::after {
                display: none;
            }

            @media (prefers-reduced-motion: reduce) {
                .dashboard-shell.dashboard-content-loading::after {
                    backdrop-filter: none;
                    -webkit-backdrop-filter: none;
                    background: rgba(255, 255, 255, 0.72);
                }

                .dashboard-hub-tab,
                .header-icon-btn,
                .header-user-chip,
                .profile-business-item,
                .dashboard-header .user-profile-options,
                .dashboard-header .user-profile-options.is-open,
                .dashboard-header .messages-notifications,
                .dashboard-header .messages-notifications.is-open,
                .dashboard-header .header-notifications,
                .dashboard-header .header-notifications.is-open,
                .dashboard-header .user-profile-options .profile-item,
                .dashboard-vq-links a {
                    transition: none;
                }

                .dashboard-header:not(.dashboard-header--business-verification) .header-icon-btn:hover {
                    transform: none;
                }
            }
        </style>
    </header>
@else
    @include($headerPublicView)
@endif
<script>
    function initHeaderDropdowns() {
        function usesOpenClass(dropdown) {
            return dropdown.classList.contains('user-profile-options') ||
                !!dropdown.closest('.dashboard-header');
        }

        function closeDropdown(dropdown) {
            if (usesOpenClass(dropdown)) {
                dropdown.classList.remove('is-open');

                return;
            }

            dropdown.style.display = 'none';
        }

        function openDropdown(dropdown) {
            if (usesOpenClass(dropdown)) {
                dropdown.classList.add('is-open');

                return;
            }

            dropdown.style.display = 'block';
        }

        function isDropdownOpen(dropdown) {
            if (usesOpenClass(dropdown)) {
                return dropdown.classList.contains('is-open');
            }

            return window.getComputedStyle(dropdown).display !== 'none';
        }

        function toggleDropdown(dropdownClass) {
            const dropdown = document.querySelector(dropdownClass);
            if (!dropdown) {
                return;
            }

            // Check visibility BEFORE closing others
            const isVisible = isDropdownOpen(dropdown);

            // Close all dropdowns first
            document.querySelectorAll('.header-notifications, .messages-notifications, .user-profile-options')
                .forEach(closeDropdown);

            // Toggle current - only open if it was previously closed
            if (!isVisible) {
                openDropdown(dropdown);
            }
        }

        // Attach directly to buttons
        const msgsBtn = document.querySelector('.messages-btn');
        const notifBtn = document.querySelector('.notification-btn');
        const userBtn = document.querySelector('.user-btn');

        if (msgsBtn && !msgsBtn._dropdownAttached) {
            msgsBtn._dropdownAttached = true;
            msgsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.messages-notifications');
            });
        }

        if (notifBtn && !notifBtn._dropdownAttached) {
            notifBtn._dropdownAttached = true;
            notifBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.header-notifications');
            });
        }

        if (userBtn && !userBtn._dropdownAttached) {
            userBtn._dropdownAttached = true;
            userBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.user-profile-options');
            });
        }
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        const target = e.target;
        const isButton = target.closest('.messages-btn') || target.closest('.notification-btn') || target
            .closest('.user-btn');
        const isInsideDropdown = target.closest('.header-notifications') ||
            target.closest('.messages-notifications') ||
            target.closest('.user-profile-options');

        if (!isButton && !isInsideDropdown) {
            document.querySelectorAll('.header-notifications, .messages-notifications, .user-profile-options')
                .forEach(el => {
                    if (el.classList.contains('user-profile-options') || el.closest('.dashboard-header')) {
                        el.classList.remove('is-open');

                        return;
                    }

                    el.style.display = 'none';
                });
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") {
            document.querySelectorAll('.header-notifications, .messages-notifications, .user-profile-options')
                .forEach(el => {
                    if (el.classList.contains('user-profile-options') || el.closest('.dashboard-header')) {
                        el.classList.remove('is-open');

                        return;
                    }

                    el.style.display = 'none';
                });
        }
    });

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeaderDropdowns);
    } else {
        initHeaderDropdowns();
    }

    // Re-initialize after Livewire
    document.addEventListener('livewire:navigated', function() {
        setTimeout(initHeaderDropdowns, 100);
    });
</script>
<script>
    (function() {
        if (window.__fursgoDashboardContentLoading) {
            return;
        }
        window.__fursgoDashboardContentLoading = true;

        const LOADING_CLASS = 'dashboard-content-loading';
        const MAX_WAIT_MS = 6000;
        let generation = 0;
        let failsafeTimer = null;

        function isDashboard() {
            return document.body.classList.contains('dashboard-shell');
        }

        function isBusinessVerification() {
            return document.body.classList.contains('dashboard-shell--business-verification');
        }

        function isBusinessVerificationUrl(url) {
            try {
                return new URL(url, window.location.origin).pathname.includes('/business-verification');
            } catch (e) {
                return false;
            }
        }

        function contentRoots() {
            return Array.from(document.querySelectorAll('.dashboard-wrapper, .dashboard-info-main'));
        }

        function startLoading(event) {
            if (!isDashboard() || isBusinessVerification()) {
                return;
            }

            if (event?.detail?.url && isBusinessVerificationUrl(event.detail.url)) {
                return;
            }

            generation += 1;
            document.body.classList.add(LOADING_CLASS);
            clearTimeout(failsafeTimer);
            failsafeTimer = setTimeout(() => stopLoading(true), MAX_WAIT_MS);
        }

        function waitForContentReady() {
            const roots = contentRoots();
            const images = roots.flatMap((root) =>
                Array.from(root.querySelectorAll('img')).filter((img) => !img.complete)
            );
            const imageWait = images.length ?
                Promise.all(images.map((img) => new Promise((resolve) => {
                    img.addEventListener('load', resolve, {
                        once: true
                    });
                    img.addEventListener('error', resolve, {
                        once: true
                    });
                }))) :
                Promise.resolve();
            const fontsWait = document.fonts?.ready ?? Promise.resolve();
            const paintWait = new Promise((resolve) => {
                requestAnimationFrame(() => requestAnimationFrame(resolve));
            });
            const settleWait = new Promise((resolve) => setTimeout(resolve, 200));

            return Promise.all([imageWait, fontsWait, paintWait, settleWait]);
        }

        function stopLoading(force = false) {
            if (!isDashboard() && !document.body.classList.contains(LOADING_CLASS)) {
                return;
            }
            const current = generation;
            const finish = () => {
                if (!force && current !== generation) {
                    return;
                }
                clearTimeout(failsafeTimer);
                failsafeTimer = null;
                document.body.classList.remove(LOADING_CLASS);
            };

            if (force || isBusinessVerification()) {
                finish();
                return;
            }

            waitForContentReady().then(finish).catch(finish);
        }

        document.addEventListener('livewire:navigate', startLoading);
        document.addEventListener('livewire:navigated', () => stopLoading(false));
        document.addEventListener('nav-list-loading-start', (event) => {
            startLoading();
            if (!event.detail?.persistent) {
                stopLoading(false);
            }
        });
        document.addEventListener('nav-list-loading-end', () => stopLoading(false));
    })();
</script>
