@props(['variant' => 'default', 'dashboardNavView' => 'hub'])

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
    $isHelpCentreRoute = request()->routeIs('help-and-support');
    $isVerifyQualifyRoute = request()->routeIs('verify-qualify', 'verify-qualify.*');
    $isBusinessAuthRoute = request()->routeIs([
        'login-groomer-space',
        'signup-groomer-space',
        'verify-qualify',
        'verify-qualify.*',
    ]);
    $isBusinessSiteRoute = $isBusinessLandingRoute
        || $isBusinessHomepageRoute
        || $isBusinessAuthRoute
        || ($isHelpCentreRoute && (
            HelpCentre::audience() === HelpCentre::AUDIENCE_BUSINESS
            || BusinessPageShell::prefersBusinessChrome()
        ));
    $isForGroomersHostsActive = $isBusinessHomepageRoute || $isBusinessLandingRoute;
    $dashboardLogoHref = $isVerifyQualifyRoute
        ? route('verify-qualify')
        : ($isMarketingHubRoute
            ? route('marketing-hub')
            : route('business-hub'));
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

    $headerUserType = $isGroomerSpacerSession
        ? $gsp->user_type ?? 'groomer'
        : (auth()->check()
            ? auth()->user()->user_type ?? 'groomer'
            : 'groomer');

    $headerPersonName = $gsp instanceof GroomerSpacerProfile
        ? ($gsp->full_name ?: $displayName)
        : $displayName;
    $headerNameParts = preg_split('/\s+/', trim((string) $headerPersonName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $headerShortName = match (true) {
        count($headerNameParts) >= 2 => $headerNameParts[0] . ' ' . mb_strtoupper(mb_substr($headerNameParts[count($headerNameParts) - 1], 0, 1)) . '.',
        count($headerNameParts) === 1 => $headerNameParts[0],
        default => $displayName ?: 'Account',
    };
    $headerBusinessLabel = $welcomeBusinessName ?: $displayName;
    $headerEmail = $gsp instanceof GroomerSpacerProfile
        ? ($gsp->email ?? null)
        : (auth()->user()->email ?? $displaySubLabel);

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

    $headerAvatar = $isHeaderAuthenticated
        ? (auth()->check() && auth()->user()->profile_image
            ? asset('storage/' . auth()->user()->profile_image)
            : $headerProfileImage)
        : asset('images/user-placeholder.png');

    $headerSwitchBusinesses = [];
    if ($gsp instanceof GroomerSpacerProfile && filled($gsp->email)) {
        $headerSwitchBusinesses = GroomerSpacerProfile::query()
            ->where('email', $gsp->email)
            ->orderBy('id')
            ->get()
            ->map(function (GroomerSpacerProfile $profile) use ($gsp, $resolveHeaderProfileImage) {
                $bd = is_array($profile->business_details) ? $profile->business_details : [];
                $bb = is_array($profile->business_basics) ? $profile->business_basics : [];
                $name = trim((string) ($bb['display_name'] ?? $bd['business_name'] ?? $profile->full_name ?? 'Business'));
                $role = strtolower((string) ($profile->user_type ?? 'groomer')) === 'space' ? 'Space Host' : 'Groomer';
                $area = ServiceArea::query()->where('groomer_spacer_id', $profile->id)->value('name');
                $area = is_string($area) && trim($area) !== '' ? trim($area) : null;

                return [
                    'id' => $profile->id,
                    'name' => $name !== '' ? $name : 'Business',
                    'meta' => $area ? $role . ' · ' . $area : $role,
                    'image' => $resolveHeaderProfileImage($profile),
                    'active' => (int) $profile->id === (int) $gsp->id,
                ];
            })
            ->all();
    }
@endphp

@if ($variant === 'dashboard')
    {{-- Dashboard Header --}}
    <header
        class="dashboard-header dashboard-header--{{ strtolower((string) $headerUserType) === 'space' ? 'space' : 'groomer' }}{{ $isVerifyQualifyRoute ? ' dashboard-header--verify-qualify' : '' }}{{ $isBusinessHubRoute ? ' dashboard-header--business-hub' : '' }}{{ $isMarketingHubRoute ? ' dashboard-header--marketing-hub' : '' }}{{ $isAccountSettingsRoute ? ' dashboard-header--account-settings' : '' }}{{ $isHelpCentreRoute ? ' dashboard-header--help-centre' : '' }}">
        <div class="dashboard-header-container">
            {{-- Main Navigation Bar --}}
            <nav class="navbar dashboard-navbar">
                <div class="dashboard-header-inner">
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
                    <div class="align-items-center dash-menu-items">
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
                        <div class="session-login-signup-div dashboard-header-icons d-flex align-items-center">
                            <div class="messages-content-tab">
                                <a class="messages-btn header-icon-btn cursor" aria-label="Messages">
                                    <span class="header-icon-btn-glyph" aria-hidden="true">
                                        <img src="{{ asset('images/header/icon-header-message.svg') }}" alt="" width="24"
                                            height="20">
                                    </span>
                                    <span class="header-icon-badge">{{ $headerBadgeCount }}</span>
                                </a>
                                <div class="messages-notifications" style="display: none;">
                                    <div
                                        class="header-notifications-header d-flex align-items-center justify-content-between">
                                        <p class="medium-font-bold">Messages</p>
                                        <div class="cursor">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path
                                                    d="M7.57129 10.2499C8.81393 10.2499 9.82129 9.24258 9.82129 7.99994C9.82129 6.7573 8.81393 5.74994 7.57129 5.74994C6.32865 5.74994 5.32129 6.7573 5.32129 7.99994C5.32129 9.24258 6.32865 10.2499 7.57129 10.2499Z"
                                                    stroke="#3B3731" />
                                                <path
                                                    d="M8.89489 0.614C8.61964 0.5 8.27014 0.5 7.57114 0.5C6.87214 0.5 6.52264 0.5 6.24739 0.614C6.06527 0.689385 5.8998 0.799922 5.76043 0.939293C5.62106 1.07866 5.51052 1.24414 5.43514 1.42625C5.36614 1.5935 5.33839 1.78925 5.32789 2.0735C5.32301 2.27895 5.26609 2.4798 5.16247 2.65727C5.05885 2.83473 4.91191 2.98302 4.73539 3.08825C4.55599 3.18858 4.35408 3.24175 4.14854 3.2428C3.943 3.24385 3.74055 3.19274 3.56014 3.09425C3.30814 2.96075 3.12589 2.88725 2.94514 2.86325C2.55088 2.8114 2.15216 2.91823 1.83664 3.16025C1.60114 3.3425 1.42564 3.64475 1.07614 4.25C0.726639 4.85525 0.551139 5.1575 0.512889 5.45375C0.487115 5.64909 0.500079 5.84759 0.551039 6.03792C0.601999 6.22825 0.689957 6.40667 0.809889 6.563C0.920889 6.707 1.07614 6.82775 1.31689 6.97925C1.67164 7.202 1.89964 7.5815 1.89964 8C1.89964 8.4185 1.67164 8.798 1.31689 9.02C1.07614 9.17225 0.920139 9.293 0.809889 9.437C0.689957 9.59333 0.601999 9.77175 0.551039 9.96208C0.500079 10.1524 0.487115 10.3509 0.512889 10.5463C0.551889 10.8418 0.726639 11.1448 1.07539 11.75C1.42564 12.3552 1.60039 12.6575 1.83664 12.8397C1.99297 12.9597 2.17139 13.0476 2.36172 13.0986C2.55205 13.1496 2.75055 13.1625 2.94589 13.1368C3.12589 13.1128 3.30814 13.0393 3.56014 12.9058C3.74055 12.8073 3.943 12.7561 4.14854 12.7572C4.35408 12.7582 4.55599 12.8114 4.73539 12.9117C5.09764 13.1217 5.31289 13.508 5.32789 13.9265C5.33839 14.2115 5.36539 14.4065 5.43514 14.5737C5.51052 14.7559 5.62106 14.9213 5.76043 15.0607C5.8998 15.2001 6.06527 15.3106 6.24739 15.386C6.52264 15.5 6.87214 15.5 7.57114 15.5C8.27014 15.5 8.61964 15.5 8.89489 15.386C9.077 15.3106 9.24247 15.2001 9.38185 15.0607C9.52122 14.9213 9.63175 14.7559 9.70714 14.5737C9.77614 14.4065 9.80389 14.2115 9.81439 13.9265C9.82939 13.508 10.0446 13.121 10.4069 12.9117C10.5863 12.8114 10.7882 12.7582 10.9937 12.7572C11.1993 12.7561 11.4017 12.8073 11.5821 12.9058C11.8341 13.0393 12.0164 13.1128 12.1964 13.1368C12.3917 13.1625 12.5902 13.1496 12.7806 13.0986C12.9709 13.0476 13.1493 12.9597 13.3056 12.8397C13.5419 12.6582 13.7166 12.3552 14.0661 11.75C14.4156 11.1448 14.5911 10.8425 14.6294 10.5463C14.6552 10.3509 14.6422 10.1524 14.5912 9.96208C14.5403 9.77175 14.4523 9.59333 14.3324 9.437C14.2214 9.293 14.0661 9.17225 13.8254 9.02075C13.6498 8.9138 13.5043 8.76405 13.4024 8.58553C13.3004 8.40701 13.2455 8.20555 13.2426 8C13.2426 7.5815 13.4706 7.202 13.8254 6.98C14.0661 6.82775 14.2221 6.707 14.3324 6.563C14.4523 6.40667 14.5403 6.22825 14.5912 6.03792C14.6422 5.84759 14.6552 5.64909 14.6294 5.45375C14.5904 5.15825 14.4156 4.85525 14.0669 4.25C13.7166 3.64475 13.5419 3.3425 13.3056 3.16025C13.1493 3.04032 12.9709 2.95236 12.7806 2.9014C12.5902 2.85044 12.3917 2.83748 12.1964 2.86325C12.0164 2.88725 11.8341 2.96075 11.5814 3.09425C11.4011 3.19261 11.1988 3.24365 10.9934 3.2426C10.788 3.24155 10.5862 3.18844 10.4069 3.08825C10.2304 2.98302 10.0834 2.83473 9.97981 2.65727C9.87619 2.4798 9.81927 2.27895 9.81439 2.0735C9.80389 1.7885 9.77689 1.5935 9.70714 1.42625C9.63175 1.24414 9.52122 1.07866 9.38185 0.939293C9.24247 0.799922 9.077 0.689385 8.89489 0.614Z"
                                                    stroke="#3B3731" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="notification-tags d-flex align-items-center">
                                        <div class="tags active">
                                            <p class="dark-color-font cursor">All <span class="count active">13</span>
                                            </p>
                                        </div>
                                        <div class="tags">
                                            <p class="simple-font muted-color cursor">Unread <span class="count">5</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="header-notifications-div">
                                        <div class="chat-card cursor unread d-flex align-items-center gap-20 mt-4">

                                            <div class="profile-pic">
                                                <div class="profile-image-wrapper">

                                                    <img src="{{ asset('images/message_profile_1.png') }}" alt="Hero Image"
                                                        class="rounded-image">
                                                    <div class="top-left-svg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22"
                                                            viewBox="0 0 20 22" fill="none">
                                                            <path
                                                                d="M10.5114 0.120327C10.3372 0.0415119 10.1505 3.05176e-05 9.95555 3.05176e-05C9.76059 3.05176e-05 9.57393 0.0415119 9.3997 0.120327L1.58876 3.43469C0.676166 3.82047 -0.00412927 4.72061 1.88678e-05 5.80743C0.0207596 9.92238 1.7132 17.4513 8.86045 20.8735C9.55319 21.2053 10.3579 21.2053 11.0507 20.8735C18.1979 17.4513 19.8903 9.92238 19.9111 5.80743C19.9152 4.72061 19.2349 3.82047 18.3224 3.43469L10.5114 0.120327Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M15.3611 5.76004L11.094 10.2939L15.3611 5.76004ZM9.19828 10.0725C7.87547 10.5803 6.81775 10.4934 5.76003 10.0741C6.02673 13.5108 7.62904 14.832 9.76527 15.3611C9.76527 15.3611 11.3745 14.2228 11.6065 11.5244C11.6316 11.2321 11.6439 11.0865 11.5836 10.9217C11.5228 10.7569 11.4033 10.639 11.1649 10.4027C10.7723 10.0144 10.5766 9.82022 10.3435 9.77115C10.1104 9.72314 9.80635 9.83942 9.19828 10.0725Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M15.3611 5.76004L11.094 10.2939M9.19828 10.0725C7.87547 10.5803 6.81775 10.4934 5.76003 10.0741C6.02673 13.5108 7.62904 14.832 9.76527 15.3611C9.76527 15.3611 11.3745 14.2228 11.6065 11.5244C11.6316 11.2321 11.6439 11.0865 11.5836 10.9217C11.5228 10.7569 11.4033 10.639 11.1649 10.4027C10.7723 10.0144 10.5766 9.82022 10.3435 9.77115C10.1104 9.72314 9.80635 9.83942 9.19828 10.0725Z"
                                                                stroke="white" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M6.5603 12.9327C6.5603 12.9327 7.89378 13.1909 9.22726 12.1614L6.5603 12.9327Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M6.5603 12.9327C6.5603 12.9327 7.89378 13.1909 9.22726 12.1614"
                                                                stroke="white" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M8.69331 8.02663C8.69331 8.20346 8.62307 8.37305 8.49803 8.49809C8.37299 8.62313 8.2034 8.69337 8.02657 8.69337C7.84974 8.69337 7.68015 8.62313 7.55512 8.49809C7.43008 8.37305 7.35983 8.20346 7.35983 8.02663C7.35983 7.8498 7.43008 7.68022 7.55512 7.55518C7.68015 7.43014 7.84974 7.35989 8.02657 7.35989C8.2034 7.35989 8.37299 7.43014 8.49803 7.55518C8.62307 7.68022 8.69331 7.8498 8.69331 8.02663Z"
                                                                fill="#CBDCE8" stroke="white" />
                                                            <path d="M10.0268 6.2937V6.34704V6.2937Z" fill="#CBDCE8" />
                                                            <path d="M10.0268 6.2937V6.34704" stroke="white"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat-info">
                                                <div class="d-flex justify-content-between">
                                                    <div class="name">
                                                        <p class="dark-color-font">The Garden Grooming Spot
                                                        </p>
                                                        <p class="simple-light-font">Hosted by Chloe D.</p>
                                                    </div>
                                                    <p class="messages-count light-color-font">2</p>

                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-2">
                                                    <p class="simple-font">Hi, just a quick reminder
                                                        abou...
                                                    </p>
                                                    <p class="time light-bold-color-font">10m</p>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="chat-card cursor unread d-flex align-items-center gap-20 mt-4">

                                            <div class="profile-pic">
                                                <div class="profile-image-wrapper">
                                                    <img src="{{ asset('images/groomer-profile.png') }}"
                                                        class="rounded-image" alt="Hero Image">
                                                    <div class="top-left-svg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="23"
                                                            viewBox="0 0 21 23" fill="none">
                                                            <ellipse cx="10.9241" cy="11.3744" rx="6.44549" ry="6.06626"
                                                                fill="white" />
                                                            <path
                                                                d="M10.6272 0.127384C10.4511 0.0439255 10.2623 0 10.0652 0C9.86812 0 9.6794 0.0439255 9.50326 0.127384L1.60626 3.63703C0.683615 4.04554 -0.00417476 4.99872 1.90757e-05 6.14957C0.0209883 10.507 1.73207 18.4795 8.95806 22.1033C9.65843 22.4547 10.472 22.4547 11.1724 22.1033C18.3984 18.4795 20.1095 10.507 20.1304 6.14957C20.1346 4.99872 19.4469 4.04554 18.5242 3.63703L10.6272 0.127384ZM6.07689 12.5715C6.2782 12.6242 6.49208 12.6505 6.71016 12.6505C8.19059 12.6505 9.39422 11.3899 9.39422 9.83931V7.02808H11.2479C11.7554 7.02808 12.2209 7.32677 12.4473 7.80556L12.7493 8.43369H15.4333C15.8024 8.43369 16.1044 8.74996 16.1044 9.1365V10.5421C16.1044 12.4836 14.603 14.0562 12.7493 14.0562H10.7362V16.2832C10.7362 16.6038 10.4888 16.8674 10.1785 16.8674C10.103 16.8674 10.0275 16.8498 9.96039 16.8191L5.82107 14.961C5.54428 14.838 5.36813 14.5525 5.36813 14.2406C5.36813 14.1177 5.3933 13.9991 5.44782 13.8892L6.07689 12.5715ZM6.03915 7.02808H8.05219V9.83931C8.05219 10.6168 7.45247 11.2449 6.71016 11.2449C5.96785 11.2449 5.36813 10.6168 5.36813 9.83931V7.73089C5.36813 7.34434 5.67009 7.02808 6.03915 7.02808ZM11.4073 9.1365C11.4073 8.9501 11.3366 8.77134 11.2107 8.63954C11.0849 8.50774 10.9142 8.43369 10.7362 8.43369C10.5583 8.43369 10.3876 8.50774 10.2618 8.63954C10.1359 8.77134 10.0652 8.9501 10.0652 9.1365C10.0652 9.3229 10.1359 9.50166 10.2618 9.63346C10.3876 9.76526 10.5583 9.83931 10.7362 9.83931C10.9142 9.83931 11.0849 9.76526 11.2107 9.63346C11.3366 9.50166 11.4073 9.3229 11.4073 9.1365Z"
                                                                fill="#C9DDA0" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat-info">
                                                <div class="d-flex justify-content-between">
                                                    <div class="name">
                                                        <p class="dark-color-font">Sarah W.</p>
                                                        <p class="simple-light-font">Sarah’s Grooming
                                                            Studio
                                                        </p>
                                                    </div>
                                                    <p class="messages-count light-color-font">3</p>

                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-2">
                                                    <p class="simple-font">Thanks for booking! I’m looki...
                                                    </p>
                                                    <p class="time light-bold-color-font">10m</p>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="chat-card cursor d-flex align-items-center gap-20 mt-4">

                                            <div class="profile-pic">
                                                <div class="profile-image-wrapper">
                                                    <img src="{{ asset('images/space_card1.png') }}" class="rounded-image"
                                                        alt="Hero Image">
                                                    <div class="top-left-svg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22"
                                                            viewBox="0 0 20 22" fill="none">
                                                            <path
                                                                d="M10.5114 0.120357C10.3372 0.0415424 10.1505 6.10352e-05 9.95555 6.10352e-05C9.76059 6.10352e-05 9.57393 0.0415424 9.3997 0.120357L1.58876 3.43472C0.676166 3.8205 -0.00412927 4.72064 1.88678e-05 5.80746C0.0207596 9.92241 1.7132 17.4513 8.86045 20.8735C9.55319 21.2054 10.3579 21.2054 11.0507 20.8735C18.1979 17.4513 19.8903 9.92241 19.9111 5.80746C19.9152 4.72064 19.2349 3.8205 18.3224 3.43472L10.5114 0.120357Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M15.3611 5.76001L11.094 10.2938L15.3611 5.76001ZM9.19828 10.0725C7.87547 10.5803 6.81775 10.4933 5.76003 10.0741C6.02673 13.5107 7.62904 14.8319 9.76527 15.3611C9.76527 15.3611 11.3745 14.2228 11.6065 11.5244C11.6316 11.2321 11.6439 11.0865 11.5836 10.9216C11.5228 10.7568 11.4033 10.6389 11.1649 10.4027C10.7723 10.0143 10.5766 9.82019 10.3435 9.77112C10.1104 9.72311 9.80635 9.83939 9.19828 10.0725Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M15.3611 5.76001L11.094 10.2938M9.19828 10.0725C7.87547 10.5803 6.81775 10.4933 5.76003 10.0741C6.02673 13.5107 7.62904 14.8319 9.76527 15.3611C9.76527 15.3611 11.3745 14.2228 11.6065 11.5244C11.6316 11.2321 11.6439 11.0865 11.5836 10.9216C11.5228 10.7568 11.4033 10.6389 11.1649 10.4027C10.7723 10.0143 10.5766 9.82019 10.3435 9.77112C10.1104 9.72311 9.80635 9.83939 9.19828 10.0725Z"
                                                                stroke="white" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M6.5603 12.9327C6.5603 12.9327 7.89378 13.1909 9.22726 12.1614L6.5603 12.9327Z"
                                                                fill="#CBDCE8" />
                                                            <path
                                                                d="M6.5603 12.9327C6.5603 12.9327 7.89378 13.1909 9.22726 12.1614"
                                                                stroke="white" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M8.69331 8.0266C8.69331 8.20343 8.62307 8.37302 8.49803 8.49806C8.37299 8.6231 8.2034 8.69334 8.02657 8.69334C7.84974 8.69334 7.68015 8.6231 7.55512 8.49806C7.43008 8.37302 7.35983 8.20343 7.35983 8.0266C7.35983 7.84977 7.43008 7.68018 7.55512 7.55515C7.68015 7.43011 7.84974 7.35986 8.02657 7.35986C8.2034 7.35986 8.37299 7.43011 8.49803 7.55515C8.62307 7.68018 8.69331 7.84977 8.69331 8.0266Z"
                                                                fill="#CBDCE8" stroke="white" />
                                                            <path d="M10.0268 6.29364V6.34698V6.29364Z" fill="#CBDCE8" />
                                                            <path d="M10.0268 6.29364V6.34698" stroke="white"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chat-info">
                                                <div class="d-flex justify-content-between">
                                                    <div class="name">
                                                        <p class="dark-color-font">Furs & Co. Studio</p>
                                                        <p class="simple-light-font">Hosted by Dev É.</p>
                                                    </div>

                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mt-2">
                                                    <p class="simple-font">Just confirming our booking f...
                                                    </p>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="view-all-mark-read d-flex align-items-center justify-content-between mt-4">
                                        <a href="{{ url('/messages_notification/messages') }}"
                                            class="dark-color-font link-tag" wire:navigate>View
                                            All</a>

                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path d="M5.1875 7.0625L8 9.875L15.5 2.375" stroke="#3B3731"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M15.5 8V13.625C15.5 14.1223 15.3025 14.5992 14.9508 14.9508C14.5992 15.3025 14.1223 15.5 13.625 15.5H2.375C1.87772 15.5 1.40081 15.3025 1.04917 14.9508C0.697544 14.5992 0.5 14.1223 0.5 13.625V2.375C0.5 1.87772 0.697544 1.40081 1.04917 1.04917C1.40081 0.697544 1.87772 0.5 2.375 0.5H10.8125"
                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <a href="" class="dark-color-font link-tag" wire:navigate>Mark as
                                                Read</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="notification-content-tab">
                                <a class="notification-btn header-icon-btn cursor" aria-label="Notifications">
                                    <span class="header-icon-btn-glyph" aria-hidden="true">
                                        <img src="{{ asset('images/header/icon-header-bell.svg') }}" alt="" width="18"
                                            height="18">
                                    </span>
                                    <span class="header-icon-badge">{{ $headerBadgeCount }}</span>
                                </a>
                                <div class="header-notifications" style="display: none;">
                                    <div
                                        class="header-notifications-header d-flex align-items-center justify-content-between">
                                        <p class="medium-font-bold">Notifications</p>
                                        <div class="cursor">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path
                                                    d="M7.57129 10.2499C8.81393 10.2499 9.82129 9.24258 9.82129 7.99994C9.82129 6.7573 8.81393 5.74994 7.57129 5.74994C6.32865 5.74994 5.32129 6.7573 5.32129 7.99994C5.32129 9.24258 6.32865 10.2499 7.57129 10.2499Z"
                                                    stroke="#3B3731" />
                                                <path
                                                    d="M8.89489 0.614C8.61964 0.5 8.27014 0.5 7.57114 0.5C6.87214 0.5 6.52264 0.5 6.24739 0.614C6.06527 0.689385 5.8998 0.799922 5.76043 0.939293C5.62106 1.07866 5.51052 1.24414 5.43514 1.42625C5.36614 1.5935 5.33839 1.78925 5.32789 2.0735C5.32301 2.27895 5.26609 2.4798 5.16247 2.65727C5.05885 2.83473 4.91191 2.98302 4.73539 3.08825C4.55599 3.18858 4.35408 3.24175 4.14854 3.2428C3.943 3.24385 3.74055 3.19274 3.56014 3.09425C3.30814 2.96075 3.12589 2.88725 2.94514 2.86325C2.55088 2.8114 2.15216 2.91823 1.83664 3.16025C1.60114 3.3425 1.42564 3.64475 1.07614 4.25C0.726639 4.85525 0.551139 5.1575 0.512889 5.45375C0.487115 5.64909 0.500079 5.84759 0.551039 6.03792C0.601999 6.22825 0.689957 6.40667 0.809889 6.563C0.920889 6.707 1.07614 6.82775 1.31689 6.97925C1.67164 7.202 1.89964 7.5815 1.89964 8C1.89964 8.4185 1.67164 8.798 1.31689 9.02C1.07614 9.17225 0.920139 9.293 0.809889 9.437C0.689957 9.59333 0.601999 9.77175 0.551039 9.96208C0.500079 10.1524 0.487115 10.3509 0.512889 10.5463C0.551889 10.8418 0.726639 11.1448 1.07539 11.75C1.42564 12.3552 1.60039 12.6575 1.83664 12.8397C1.99297 12.9597 2.17139 13.0476 2.36172 13.0986C2.55205 13.1496 2.75055 13.1625 2.94589 13.1368C3.12589 13.1128 3.30814 13.0393 3.56014 12.9058C3.74055 12.8073 3.943 12.7561 4.14854 12.7572C4.35408 12.7582 4.55599 12.8114 4.73539 12.9117C5.09764 13.1217 5.31289 13.508 5.32789 13.9265C5.33839 14.2115 5.36539 14.4065 5.43514 14.5737C5.51052 14.7559 5.62106 14.9213 5.76043 15.0607C5.8998 15.2001 6.06527 15.3106 6.24739 15.386C6.52264 15.5 6.87214 15.5 7.57114 15.5C8.27014 15.5 8.61964 15.5 8.89489 15.386C9.077 15.3106 9.24247 15.2001 9.38185 15.0607C9.52122 14.9213 9.63175 14.7559 9.70714 14.5737C9.77614 14.4065 9.80389 14.2115 9.81439 13.9265C9.82939 13.508 10.0446 13.121 10.4069 12.9117C10.5863 12.8114 10.7882 12.7582 10.9937 12.7572C11.1993 12.7561 11.4017 12.8073 11.5821 12.9058C11.8341 13.0393 12.0164 13.1128 12.1964 13.1368C12.3917 13.1625 12.5902 13.1496 12.7806 13.0986C12.9709 13.0476 13.1493 12.9597 13.3056 12.8397C13.5419 12.6582 13.7166 12.3552 14.0661 11.75C14.4156 11.1448 14.5911 10.8425 14.6294 10.5463C14.6552 10.3509 14.6422 10.1524 14.5912 9.96208C14.5403 9.77175 14.4523 9.59333 14.3324 9.437C14.2214 9.293 14.0661 9.17225 13.8254 9.02075C13.6498 8.9138 13.5043 8.76405 13.4024 8.58553C13.3004 8.40701 13.2455 8.20555 13.2426 8C13.2426 7.5815 13.4706 7.202 13.8254 6.98C14.0661 6.82775 14.2221 6.707 14.3324 6.563C14.4523 6.40667 14.5403 6.22825 14.5912 6.03792C14.6422 5.84759 14.6552 5.64909 14.6294 5.45375C14.5904 5.15825 14.4156 4.85525 14.0669 4.25C13.7166 3.64475 13.5419 3.3425 13.3056 3.16025C13.1493 3.04032 12.9709 2.95236 12.7806 2.9014C12.5902 2.85044 12.3917 2.83748 12.1964 2.86325C12.0164 2.88725 11.8341 2.96075 11.5814 3.09425C11.4011 3.19261 11.1988 3.24365 10.9934 3.2426C10.788 3.24155 10.5862 3.18844 10.4069 3.08825C10.2304 2.98302 10.0834 2.83473 9.97981 2.65727C9.87619 2.4798 9.81927 2.27895 9.81439 2.0735C9.80389 1.7885 9.77689 1.5935 9.70714 1.42625C9.63175 1.24414 9.52122 1.07866 9.38185 0.939293C9.24247 0.799922 9.077 0.689385 8.89489 0.614Z"
                                                    stroke="#3B3731" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="notification-tags d-flex align-items-center">
                                        <div class="tags active">
                                            <p class="dark-color-font cursor">All <span class="count active">13</span>
                                            </p>
                                        </div>
                                        <div class="tags">
                                            <p class="simple-font muted-color cursor">Bookings <span class="count">5</span>
                                            </p>
                                        </div>
                                        <div class="tags">
                                            <p class="simple-font muted-color cursor">Payments <span class="count">3</span>
                                            </p>
                                        </div>
                                        <div class="tags">
                                            <p class="simple-font muted-color cursor">Reviews <span class="count">2</span>
                                            </p>
                                        </div>
                                        <div class="tags">
                                            <p class="simple-font muted-color cursor">Updates <span class="count">3</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="header-notifications-div">
                                        <div class="header-notifications-list booking-confirmed cursor mt-4">
                                            <div
                                                class="header-notification-list-item d-flex align-items-center justify-content-between">
                                                <div class="notification-list-item-inner-left d-flex align-items-center">
                                                    <div class="notification-list-item-inner-left-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86"
                                                            viewBox="0 0 86 86" fill="none">
                                                            <g opacity="0.8" filter="url(#filter0_d_10_2216)">
                                                                <circle cx="43" cy="39" r="28" fill="#C9DDA0" />
                                                            </g>
                                                            <path
                                                                d="M33 39.0948C33 35.4849 33 33.6795 34.172 32.5586C35.344 31.4376 37.229 31.4366 41 31.4366H45C48.771 31.4366 50.657 31.4366 51.828 32.5586C52.999 33.6805 53 35.4849 53 39.0948V41.0093C53 44.6191 53 46.4245 51.828 47.5455C50.656 48.6664 48.771 48.6674 45 48.6674H41C37.229 48.6674 35.343 48.6674 34.172 47.5455C33.001 46.4236 33 44.6191 33 41.0093V39.0948Z"
                                                                stroke="white" stroke-width="1.5" />
                                                            <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5"
                                                                stroke="white" stroke-width="1.5" stroke-linecap="round" />
                                                            <path
                                                                d="M49.0002 43.8804C49.0002 44.1343 48.8949 44.3778 48.7074 44.5573C48.5198 44.7368 48.2655 44.8377 48.0002 44.8377C47.735 44.8377 47.4807 44.7368 47.2931 44.5573C47.1056 44.3778 47.0002 44.1343 47.0002 43.8804C47.0002 43.6266 47.1056 43.3831 47.2931 43.2035C47.4807 43.024 47.735 42.9232 48.0002 42.9232C48.2655 42.9232 48.5198 43.024 48.7074 43.2035C48.8949 43.3831 49.0002 43.6266 49.0002 43.8804ZM49.0002 40.0514C49.0002 40.3053 48.8949 40.5487 48.7074 40.7283C48.5198 40.9078 48.2655 41.0086 48.0002 41.0086C47.735 41.0086 47.4807 40.9078 47.2931 40.7283C47.1056 40.5487 47.0002 40.3053 47.0002 40.0514C47.0002 39.7975 47.1056 39.554 47.2931 39.3745C47.4807 39.195 47.735 39.0941 48.0002 39.0941C48.2655 39.0941 48.5198 39.195 48.7074 39.3745C48.8949 39.554 49.0002 39.7975 49.0002 40.0514ZM44.0002 43.8804C44.0002 44.1343 43.8949 44.3778 43.7074 44.5573C43.5198 44.7368 43.2655 44.8377 43.0002 44.8377C42.735 44.8377 42.4807 44.7368 42.2931 44.5573C42.1056 44.3778 42.0002 44.1343 42.0002 43.8804C42.0002 43.6266 42.1056 43.3831 42.2931 43.2035C42.4807 43.024 42.735 42.9232 43.0002 42.9232C43.2655 42.9232 43.5198 43.024 43.7074 43.2035C43.8949 43.3831 44.0002 43.6266 44.0002 43.8804ZM44.0002 40.0514C44.0002 40.3053 43.8949 40.5487 43.7074 40.7283C43.5198 40.9078 43.2655 41.0086 43.0002 41.0086C42.735 41.0086 42.4807 40.9078 42.2931 40.7283C42.1056 40.5487 42.0002 40.3053 42.0002 40.0514C42.0002 39.7975 42.1056 39.554 42.2931 39.3745C42.4807 39.195 42.735 39.0941 43.0002 39.0941C43.2655 39.0941 43.5198 39.195 43.7074 39.3745C43.8949 39.554 44.0002 39.7975 44.0002 40.0514ZM39.0002 43.8804C39.0002 44.1343 38.8949 44.3778 38.7074 44.5573C38.5198 44.7368 38.2655 44.8377 38.0002 44.8377C37.735 44.8377 37.4807 44.7368 37.2931 44.5573C37.1056 44.3778 37.0002 44.1343 37.0002 43.8804C37.0002 43.6266 37.1056 43.3831 37.2931 43.2035C37.4807 43.024 37.735 42.9232 38.0002 42.9232C38.2655 42.9232 38.5198 43.024 38.7074 43.2035C38.8949 43.3831 39.0002 43.6266 39.0002 43.8804ZM39.0002 40.0514C39.0002 40.3053 38.8949 40.5487 38.7074 40.7283C38.5198 40.9078 38.2655 41.0086 38.0002 41.0086C37.735 41.0086 37.4807 40.9078 37.2931 40.7283C37.1056 40.5487 37.0002 40.3053 37.0002 40.0514C37.0002 39.7975 37.1056 39.554 37.2931 39.3745C37.4807 39.195 37.735 39.0941 38.0002 39.0941C38.2655 39.0941 38.5198 39.195 38.7074 39.3745C38.8949 39.554 39.0002 39.7975 39.0002 40.0514Z"
                                                                fill="white" />
                                                            <defs>
                                                                <filter id="filter0_d_10_2216" x="0" y="0" width="86"
                                                                    height="86" filterUnits="userSpaceOnUse"
                                                                    color-interpolation-filters="sRGB">
                                                                    <feFlood flood-opacity="0"
                                                                        result="BackgroundImageFix" />
                                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                                        result="hardAlpha" />
                                                                    <feMorphology radius="5" operator="dilate"
                                                                        in="SourceAlpha"
                                                                        result="effect1_dropShadow_10_2216" />
                                                                    <feOffset dy="4" />
                                                                    <feGaussianBlur stdDeviation="5" />
                                                                    <feComposite in2="hardAlpha" operator="out" />
                                                                    <feColorMatrix type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                                        result="effect1_dropShadow_10_2216" />
                                                                    <feBlend mode="normal" in="SourceGraphic"
                                                                        in2="effect1_dropShadow_10_2216" result="shape" />
                                                                </filter>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <div class="notification-list-item-inner-left-content">
                                                        <p class="small-bolder-color-font">Booking
                                                            Confirmed
                                                        </p>
                                                        <p class="dark-color-font mt-1">Your appointment
                                                            with
                                                            [Groomer Name] is confirmed.</p>
                                                        <p class="simple-font mt-1">20m ago | Bookings</p>
                                                    </div>
                                                </div>
                                                <div class="notification-list-item-inner-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                        viewBox="0 0 10 10" fill="none">
                                                        <circle cx="5" cy="5" r="5" fill="#C9DDA0" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header-notifications-list booking-reminder cursor mt-4">
                                            <div
                                                class="header-notification-list-item d-flex align-items-center justify-content-between">
                                                <div class="notification-list-item-inner-left d-flex align-items-center">
                                                    <div class="notification-list-item-inner-left-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82"
                                                            viewBox="0 0 82 82" fill="none">
                                                            <g filter="url(#filter0_d_10_1928)">
                                                                <circle cx="41" cy="37" r="28" fill="#FBAC83" />
                                                            </g>
                                                            <path
                                                                d="M41 28.2656C41.0605 28.2656 41.1132 28.2837 41.1504 28.3096C41.1824 28.3319 41.2169 28.3701 41.2393 28.4453L42.9014 34.0469C43.1217 34.7896 43.8044 35.2988 44.5791 35.2988H50.1377C50.3741 35.2991 50.4781 35.5978 50.293 35.7451L45.6445 39.4443C45.0864 39.8886 44.8539 40.6267 45.0566 41.3105L46.7939 47.1699C46.8631 47.404 46.5906 47.5881 46.3994 47.4365L42.0898 44.0068C41.452 43.4993 40.548 43.4993 39.9102 44.0068L35.6006 47.4365C35.4094 47.5881 35.1369 47.404 35.2061 47.1699L36.9434 41.3105C37.1461 40.6267 36.9135 39.8886 36.3555 39.4443L31.707 35.7451C31.5219 35.5978 31.6259 35.2991 31.8623 35.2988H37.4209C38.1956 35.2988 38.8783 34.7896 39.0986 34.0469L40.7607 28.4453C40.7831 28.3701 40.8176 28.3319 40.8496 28.3096C40.8868 28.2837 40.9395 28.2656 41 28.2656Z"
                                                                stroke="white" stroke-width="1.5" />
                                                            <defs>
                                                                <filter id="filter0_d_10_1928" x="0" y="0" width="82"
                                                                    height="82" filterUnits="userSpaceOnUse"
                                                                    color-interpolation-filters="sRGB">
                                                                    <feFlood flood-opacity="0"
                                                                        result="BackgroundImageFix" />
                                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                                        result="hardAlpha" />
                                                                    <feMorphology radius="5" operator="dilate"
                                                                        in="SourceAlpha"
                                                                        result="effect1_dropShadow_10_1928" />
                                                                    <feOffset dy="4" />
                                                                    <feGaussianBlur stdDeviation="4" />
                                                                    <feComposite in2="hardAlpha" operator="out" />
                                                                    <feColorMatrix type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                                        result="effect1_dropShadow_10_1928" />
                                                                    <feBlend mode="normal" in="SourceGraphic"
                                                                        in2="effect1_dropShadow_10_1928" result="shape" />
                                                                </filter>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <div class="notification-list-item-inner-left-content">
                                                        <p class="small-bolder-color-font">Review reminder
                                                        </p>
                                                        <p class="dark-color-font mt-1">How was your
                                                            booking
                                                            with
                                                            Claire Smith? Leave a review.</p>
                                                        <p class="simple-font mt-1">1d ago | Reviews</p>
                                                    </div>
                                                </div>
                                                <div class="notification-list-item-inner-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                        viewBox="0 0 10 10" fill="none">
                                                        <circle cx="5" cy="5" r="5" fill="#FBAC83" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header-notifications-list payment-failed cursor mt-4">
                                            <div
                                                class="header-notification-list-item d-flex align-items-center justify-content-between">
                                                <div class="notification-list-item-inner-left d-flex align-items-center">
                                                    <div class="notification-list-item-inner-left-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82"
                                                            viewBox="0 0 82 82" fill="none">
                                                            <g filter="url(#filter0_d_10_1934)">
                                                                <circle cx="41" cy="37" r="28" fill="#FFA899" />
                                                            </g>
                                                            <path
                                                                d="M31 33.5714V32.6571C31 31.3771 31 30.7371 31.2422 30.248C31.4556 29.8171 31.7944 29.4686 32.2133 29.2491C32.6889 29 33.3111 29 34.5556 29H47.4444C48.6889 29 49.3111 29 49.7856 29.2491C50.2044 29.4686 50.5444 29.8171 50.7578 30.248C51 30.736 51 31.376 51 32.6537V33.5714M31 33.5714H51H31ZM31 33.5714V41.3429C31 42.6229 31 43.2629 31.2422 43.752C31.4553 44.1821 31.7952 44.5317 32.2133 44.7509C32.6878 45 33.31 45 34.5522 45H47.4478C48.69 45 49.3111 45 49.7856 44.7509C50.2044 44.5314 50.5444 44.1817 50.7578 43.752C51 43.2629 51 42.6251 51 41.3474V33.5714M34.3333 40.4286H38.7778H34.3333Z"
                                                                fill="#FFA899" />
                                                            <path
                                                                d="M31 33.5714V32.6571C31 31.3771 31 30.7371 31.2422 30.248C31.4556 29.8171 31.7944 29.4686 32.2133 29.2491C32.6889 29 33.3111 29 34.5556 29H47.4444C48.6889 29 49.3111 29 49.7856 29.2491C50.2044 29.4686 50.5444 29.8171 50.7578 30.248C51 30.736 51 31.376 51 32.6537V33.5714M31 33.5714H51M31 33.5714V41.3429C31 42.6229 31 43.2629 31.2422 43.752C31.4553 44.1821 31.7952 44.5317 32.2133 44.7509C32.6878 45 33.31 45 34.5522 45H47.4478C48.69 45 49.3111 45 49.7856 44.7509C50.2044 44.5314 50.5444 44.1817 50.7578 43.752C51 43.2629 51 42.6251 51 41.3474V33.5714M34.3333 40.4286H38.7778"
                                                                stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <defs>
                                                                <filter id="filter0_d_10_1934" x="0" y="0" width="82"
                                                                    height="82" filterUnits="userSpaceOnUse"
                                                                    color-interpolation-filters="sRGB">
                                                                    <feFlood flood-opacity="0"
                                                                        result="BackgroundImageFix" />
                                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                                        result="hardAlpha" />
                                                                    <feMorphology radius="5" operator="dilate"
                                                                        in="SourceAlpha"
                                                                        result="effect1_dropShadow_10_1934" />
                                                                    <feOffset dy="4" />
                                                                    <feGaussianBlur stdDeviation="4" />
                                                                    <feComposite in2="hardAlpha" operator="out" />
                                                                    <feColorMatrix type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                                        result="effect1_dropShadow_10_1934" />
                                                                    <feBlend mode="normal" in="SourceGraphic"
                                                                        in2="effect1_dropShadow_10_1934" result="shape" />
                                                                </filter>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <div class="notification-list-item-inner-left-content">
                                                        <p class="small-bolder-color-font">Payment failed
                                                        </p>
                                                        <p class="dark-color-font mt-1">There was an issue
                                                            processing your payment.</p>
                                                        <p class="simple-font mt-1">1h ago | Payments</p>
                                                    </div>
                                                </div>
                                                <div class="notification-list-item-inner-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"
                                                        viewBox="0 0 10 10" fill="none">
                                                        <circle cx="5" cy="5" r="5" fill="#FFA899" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="header-notifications-list cursor mt-4">
                                            <div
                                                class="header-notification-list-item d-flex align-items-center justify-content-between">
                                                <div class="notification-list-item-inner-left d-flex align-items-center">
                                                    <div class="notification-list-item-inner-left-icon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82"
                                                            viewBox="0 0 82 82" fill="none">
                                                            <g filter="url(#filter0_d_10_1940)">
                                                                <circle cx="41" cy="37" r="28" fill="white" />
                                                            </g>
                                                            <path
                                                                d="M46.9305 35.4737C47.5421 41.1316 49.9474 42.8421 49.9474 42.8421H31C31 42.8421 34.1579 40.5968 34.1579 32.7368C34.1579 30.9505 34.8232 29.2368 36.0074 27.9737C37.1916 26.7105 38.8 26 40.4737 26C40.8295 26 41.1804 26.0316 41.5263 26.0947M42.2947 46C42.1097 46.319 41.844 46.5838 41.5244 46.7679C41.2049 46.952 40.8425 47.0489 40.4737 47.0489C40.1049 47.0489 39.7425 46.952 39.4229 46.7679C39.1033 46.5838 38.8377 46.319 38.6526 46M47.8421 32.3158C48.6796 32.3158 49.4829 31.9831 50.0751 31.3909C50.6673 30.7986 51 29.9954 51 29.1579C51 28.3204 50.6673 27.5171 50.0751 26.9249C49.4829 26.3327 48.6796 26 47.8421 26C47.0046 26 46.2014 26.3327 45.6091 26.9249C45.0169 27.5171 44.6842 28.3204 44.6842 29.1579C44.6842 29.9954 45.0169 30.7986 45.6091 31.3909C46.2014 31.9831 47.0046 32.3158 47.8421 32.3158Z"
                                                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <defs>
                                                                <filter id="filter0_d_10_1940" x="0" y="0" width="82"
                                                                    height="82" filterUnits="userSpaceOnUse"
                                                                    color-interpolation-filters="sRGB">
                                                                    <feFlood flood-opacity="0"
                                                                        result="BackgroundImageFix" />
                                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                                        result="hardAlpha" />
                                                                    <feMorphology radius="5" operator="dilate"
                                                                        in="SourceAlpha"
                                                                        result="effect1_dropShadow_10_1940" />
                                                                    <feOffset dy="4" />
                                                                    <feGaussianBlur stdDeviation="4" />
                                                                    <feComposite in2="hardAlpha" operator="out" />
                                                                    <feColorMatrix type="matrix"
                                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                                        result="effect1_dropShadow_10_1940" />
                                                                    <feBlend mode="normal" in="SourceGraphic"
                                                                        in2="effect1_dropShadow_10_1940" result="shape" />
                                                                </filter>
                                                            </defs>
                                                        </svg>
                                                    </div>
                                                    <div class="notification-list-item-inner-left-content">
                                                        <p class="small-bolder-color-font">Promotion</p>
                                                        <p class="dark-color-font mt-1">🎉 Get 10% off your
                                                            next
                                                            booking!</p>
                                                        <p class="simple-font mt-1">1h ago | Updates</p>
                                                    </div>
                                                </div>
                                                <div class="notification-list-item-inner-right">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-all-mark-read d-flex align-items-center justify-content-between mt-4">
                                        <a href="{{ url('/messages_notification/notifications') }}"
                                            class="dark-color-font link-tag" wire:navigate>View
                                            All</a>

                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none">
                                                <path d="M5.1875 7.0625L8 9.875L15.5 2.375" stroke="#3B3731"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M15.5 8V13.625C15.5 14.1223 15.3025 14.5992 14.9508 14.9508C14.5992 15.3025 14.1223 15.5 13.625 15.5H2.375C1.87772 15.5 1.40081 15.3025 1.04917 14.9508C0.697544 14.5992 0.5 14.1223 0.5 13.625V2.375C0.5 1.87772 0.697544 1.40081 1.04917 1.04917C1.40081 0.697544 1.87772 0.5 2.375 0.5H10.8125"
                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <a href="" class="dark-color-font link-tag" wire:navigate>Mark as
                                                Read</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span class="dashboard-header-divider" aria-hidden="true"></span>
                            <div class="user-content-tab">
                                <button type="button" class="user-btn header-user-chip cursor" aria-label="Account menu"
                                    aria-haspopup="true">
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
                                            <span>Help &amp; Support</span>
                                        </a>
                                    </div>
                                    @if ($isHeaderAuthenticated)
                                        <div class="logout-option">
                                            <form method="POST" action="{{ $logoutUrl }}">
                                                @csrf
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
                --dashboard-sidebar-col: 14rem;
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
                padding: 0 50px;
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
                height: 27px;
                padding: 0 12px;
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
                font-family: Lato, sans-serif;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                text-decoration: none;
                white-space: nowrap;
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
                font-family: Lato, sans-serif;
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
                color: #3b3731;
                text-decoration: none;
            }

            .dashboard-hub-tab.is-active:hover {
                color: #ffc97a;
            }

            .dashboard-header--space .dashboard-hub-tab.is-active:hover {
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
            }

            .header-icon-btn-glyph {
                width: 24px;
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
                font-family: Lato, sans-serif;
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
                font-family: Lato, sans-serif;
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

            .dashboard-header-icons .messages-notifications {
                position: absolute;
                right: 0;
                top: 55px;
                z-index: 1001;
            }

            .dashboard-header-icons .header-notifications {
                position: absolute;
                right: 0;
                top: 55px;
                z-index: 1001;
            }

            .dashboard-header .user-profile-options {
                position: absolute;
                right: 0;
                top: 5.5rem;
                z-index: 1001;
                width: 270px;
                height: auto;
                border-radius: 10px;
                border: 1px solid #f0ead9;
                background: #fff;
                display: none;
                overflow: hidden;
                box-sizing: border-box;
                padding: 1rem 1.5rem 0.5rem;
            }

            .dashboard-header .user-profile-options.is-open {
                display: block;
            }

            .dashboard-header .user-profile-options .user-profile-image {
                display: flex;
                align-items: center;
                gap: 12px;
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
                gap: 4px;
            }

            .dashboard-header .user-profile-options .name-email p {
                margin: 0;
                line-height: normal;
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
                font-family: Lato, sans-serif;
                font-size: 16px;
                font-weight: 400;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .profile-switch-business {
                padding: 1rem 0 0.5rem;
                border-bottom: 1px solid #e2e2e2;
            }

            .profile-switch-label {
                margin: 0 0 12px;
                color: #9d9b98;
                font-family: Lato, sans-serif;
                font-size: 12px;
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
                padding: 0 10px;
                border: 0;
                border-radius: 10px;
                background: transparent;
                text-align: left;
                cursor: pointer;
                appearance: none;
                box-sizing: border-box;
            }

            .profile-business-item.is-active,
            .profile-business-item:hover {
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
                font-family: Lato, sans-serif;
                font-size: 12px;
                font-weight: 400;
                line-height: 1.2;
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
                height: 43px;
                padding: 0 10px;
                color: #ffc46e;
                font-family: Lato, sans-serif;
                font-size: 12px;
                font-weight: 600;
                text-decoration: none;
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
                padding: 8px 0 0;
            }

            .dashboard-header .user-profile-options .profile-item {
                display: flex;
                align-items: center;
                gap: 10px;
                height: 37px;
                padding: 0 10px;
                border-radius: 10px;
                color: #3b3731;
                font-family: Lato, sans-serif;
                font-size: 14px;
                font-weight: 400;
                text-decoration: none;
                background: transparent;
                border: 0;
                width: 100%;
                text-align: left;
                cursor: pointer;
                box-sizing: border-box;
                appearance: none;
            }

            .dashboard-header .user-profile-options .profile-item-icon {
                flex-shrink: 0;
                width: 15px;
                height: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .dashboard-header .user-profile-options .profile-item-icon img {
                width: 100%;
                height: 100%;
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
                margin-top: 8px;
                padding: 8px 0 0;
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

            .dashboard-header.dashboard-header--verify-qualify .dashboard-header-container {
                max-width: 1440px;
                padding-left: 50px;
                padding-right: 50px;
            }

            .dashboard-header .menu-toggle {
                display: none !important;
            }

            @media (max-width: 1199.98px) {

                .dashboard-header .dashboard-header-container,
                .dashboard-header.dashboard-header--verify-qualify .dashboard-header-container {
                    padding-left: 24px;
                    padding-right: 24px;
                }

                .header-user-business {
                    display: none;
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

                .dashboard-header .dashboard-header-container,
                .dashboard-header.dashboard-header--verify-qualify .dashboard-header-container {
                    padding-left: 16px;
                    padding-right: 16px;
                }

                .header-user-copy {
                    display: none;
                }

                .header-user-chevron {
                    display: none;
                }

                .header-icon-btn {
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

            @media (prefers-reduced-motion: reduce) {
                .dashboard-shell.dashboard-content-loading::after {
                    backdrop-filter: none;
                    -webkit-backdrop-filter: none;
                    background: rgba(255, 255, 255, 0.72);
                }
            }
        </style>
    </header>
@else
    @include($headerPublicView)
@endif
<script>
    function initHeaderDropdowns() {
        function closeDropdown(dropdown) {
            if (dropdown.classList.contains('user-profile-options')) {
                dropdown.classList.remove('is-open');

                return;
            }

            dropdown.style.display = 'none';
        }

        function openDropdown(dropdown) {
            if (dropdown.classList.contains('user-profile-options')) {
                dropdown.classList.add('is-open');

                return;
            }

            dropdown.style.display = 'block';
        }

        function isDropdownOpen(dropdown) {
            if (dropdown.classList.contains('user-profile-options')) {
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
            msgsBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.messages-notifications');
            });
        }

        if (notifBtn && !notifBtn._dropdownAttached) {
            notifBtn._dropdownAttached = true;
            notifBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.header-notifications');
            });
        }

        if (userBtn && !userBtn._dropdownAttached) {
            userBtn._dropdownAttached = true;
            userBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleDropdown('.user-profile-options');
            });
        }
    }

    // Close on outside click
    document.addEventListener('click', function (e) {
        const target = e.target;
        const isButton = target.closest('.messages-btn') || target.closest('.notification-btn') || target
            .closest('.user-btn');
        const isInsideDropdown = target.closest('.header-notifications') ||
            target.closest('.messages-notifications') ||
            target.closest('.user-profile-options');

        if (!isButton && !isInsideDropdown) {
            document.querySelectorAll('.header-notifications, .messages-notifications, .user-profile-options')
                .forEach(el => {
                    if (el.classList.contains('user-profile-options')) {
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
                    if (el.classList.contains('user-profile-options')) {
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
    document.addEventListener('livewire:navigated', function () {
        setTimeout(initHeaderDropdowns, 100);
    });
</script>
<script>
    (function () {
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

        function contentRoots() {
            return Array.from(document.querySelectorAll('.dashboard-wrapper, .dashboard-info-main'));
        }

        function startLoading() {
            if (!isDashboard()) {
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
            const imageWait = images.length
                ? Promise.all(images.map((img) => new Promise((resolve) => {
                    img.addEventListener('load', resolve, { once: true });
                    img.addEventListener('error', resolve, { once: true });
                })))
                : Promise.resolve();
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

            if (force) {
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