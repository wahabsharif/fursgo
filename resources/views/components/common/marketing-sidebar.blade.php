@props(['variant' => null])

@php
    use App\Support\MarketingHubNav;

    $dashboardNav = MarketingHubNav::fromSession();
    $dashboardActiveSection = $dashboardNav['active_section'];

    $activeBgColor = '#FFC97A';
    if (auth()->check() && strtolower((string) auth()->user()->user_type) === 'space') {
        $activeBgColor = '#FFA899';
    }
@endphp

<div x-data="{
    mobileOpen: false,
}"
    style="{{ $variant === 'dashboard' ? 'max-width: 16rem; margin: 0; padding: 0; width: 100%; position: relative;' : 'position: relative;' }}">
    <style>
        :root {
            --sidebar-active-bg: {{ $activeBgColor }};
        }

        .dashboard-wrapper {
            display: flex;
            gap: 4rem;
            padding-top: 2rem;
            max-width: 110rem;
            margin: 0 auto;
            width: 100%;
        }

        .aside {
            flex-shrink: 0;
            position: sticky;
            top: 2rem;
            align-self: flex-start;
            height: fit-content;
        }

        .mobile-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 50;
            padding: 0.5rem;
            background: var(--sidebar-active-bg);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            display: none;
            color: #5a3d2b;
        }

        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-width: 240px;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 9999px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .nav-link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background: var(--sidebar-active-bg);
            color: #FFF;
        }

        .nav-link:hover svg [stroke]:not([stroke="none"]),
        .nav-link.active svg [stroke]:not([stroke="none"]) {
            stroke: #FFF;
        }

        .nav-link:hover svg [fill]:not([fill="none"]),
        .nav-link.active svg [fill]:not([fill="none"]) {
            fill: #FFF;
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: #FFF;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 700 !important;
            line-height: normal;
            border: none;
            outline: none;
        }

        .nav-text {
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        @media (max-width: 991.98px) {
            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .aside {
                display: none;
            }

            .aside.is-open {
                display: block;
                position: absolute;
                z-index: 40;
                background: #fff;
                width: 16rem;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            }
        }
    </style>

    <button @click="mobileOpen = !mobileOpen" class="mobile-toggle" aria-label="Toggle Sidebar">
        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <aside class="aside" :class="{ 'is-open': mobileOpen }">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('marketing-hub') }}"
                    @click.prevent="activeSection = 'marketing-hub'; window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'marketing-hub' } }))"
                    :class="{ 'active': activeSection === 'marketing-hub' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13"
                        fill="none" aria-hidden="true">
                        <path
                            d="M0.961537 6.49898C0.961537 6.49898 0.5 6.32591 0.5 5.57591C0.5 4.82591 0.961537 4.65284 0.961537 4.65284M12.0384 6.21052C12.0384 6.21052 12.5 6.08562 12.5 5.57591C12.5 5.0662 12.0384 4.9413 12.0384 4.9413M6.49998 3.72976V7.42206M2.34615 3.72976V7.42206M10.8828 0.620733C10.8828 0.620733 8.38363 3.72976 6.03844 3.72976H1.42307C1.30067 3.72976 1.18327 3.77839 1.09672 3.86494C1.01016 3.9515 0.961537 4.06889 0.961537 4.1913V6.96052C0.961537 7.08293 1.01016 7.20032 1.09672 7.28688C1.18327 7.37343 1.30067 7.42206 1.42307 7.42206H6.03844C8.38363 7.42206 10.8828 10.5441 10.8828 10.5441C11.0577 10.7748 11.5769 10.6168 11.5769 10.2605V0.902847C11.5769 0.547752 11.0865 0.359964 10.8828 0.620733Z"
                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M3.26922 7.42206V12.2682C3.26922 12.3294 3.29353 12.3881 3.33681 12.4314C3.38009 12.4747 3.43878 12.499 3.49999 12.499H5.02883C5.10113 12.499 5.17242 12.482 5.23696 12.4494C5.30151 12.4169 5.35749 12.3696 5.40041 12.3114C5.44332 12.2532 5.47197 12.1857 5.48404 12.1145C5.49611 12.0432 5.49126 11.9701 5.46988 11.901C5.22815 11.1256 4.65383 10.2513 4.65383 8.80667H5.11537C5.23777 8.80667 5.35517 8.75804 5.44172 8.67149C5.52828 8.58493 5.5769 8.46754 5.5769 8.34513V7.8836C5.5769 7.76119 5.52828 7.64379 5.44172 7.55724C5.35517 7.47068 5.23777 7.42206 5.11537 7.42206H4.65383"
                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-text">Marketing Hub</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('marketing-hub') }}"
                    @click.prevent="activeSection = 'promo-creation'; window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'promo-creation' } }))"
                    :class="{ 'active': activeSection === 'promo-creation' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14"
                        fill="none" aria-hidden="true">
                        <path
                            d="M4.29615 6.92731C4.38027 6.94933 4.46964 6.96035 4.56076 6.96035C5.17935 6.96035 5.68228 6.43358 5.68228 5.78567V4.61099H6.45683C6.66887 4.61099 6.86339 4.7358 6.95801 4.93586L7.08418 5.19833H8.20571C8.35992 5.19833 8.48609 5.33048 8.48609 5.492V6.07934C8.48609 6.8906 7.85874 7.54769 7.08418 7.54769H6.24304V8.47825C6.24304 8.61224 6.13965 8.72236 6.00998 8.72236C5.97843 8.72236 5.94689 8.71502 5.91885 8.70217L4.18926 7.92579C4.0736 7.87439 4 7.75509 4 7.62477C4 7.57338 4.01051 7.52383 4.0333 7.47794L4.29615 6.92731ZM4.28038 4.61099H5.12152V5.78567C5.12152 6.11054 4.87093 6.37301 4.56076 6.37301C4.25059 6.37301 4 6.11054 4 5.78567V4.90466C4 4.74314 4.12617 4.61099 4.28038 4.61099Z"
                            fill="#3B3731" />
                        <path
                            d="M6 0.625C6.02228 0.625 6.04363 0.629375 6.06738 0.640625L6.07422 0.643555L6.08105 0.647461L10.7891 2.73926C11.1208 2.88613 11.3764 3.23484 11.375 3.66309C11.3632 6.0941 10.4324 10.3399 6.74512 12.4229L6.37988 12.6172C6.13876 12.7382 5.86124 12.7382 5.62012 12.6172C1.87814 10.7404 0.7732 6.71839 0.639648 4.15527L0.625 3.66309C0.623815 3.28842 0.818847 2.97465 1.08984 2.80371L1.21094 2.73926L5.91895 0.647461L5.92578 0.643555L5.93262 0.640625C5.95637 0.629375 5.97772 0.625 6 0.625Z"
                            stroke="#3B3731" stroke-width="1.25" />
                    </svg>
                    <span class="nav-text">Promo Creation</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('marketing-hub') }}"
                    @click.prevent="activeSection = 'settings'; window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'settings' } }))"
                    :class="{ 'active': activeSection === 'settings' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                        fill="none" aria-hidden="true">
                        <path
                            d="M6.62511 8.89842C7.67952 8.89842 8.53428 8.04365 8.53428 6.98925C8.53428 5.93484 7.67952 5.08008 6.62511 5.08008C5.57071 5.08008 4.71594 5.93484 4.71594 6.98925C4.71594 8.04365 5.57071 8.89842 6.62511 8.89842Z"
                            stroke="#3B3731" stroke-width="1.25" />
                        <path
                            d="M7.74823 0.721731C7.51467 0.625 7.21812 0.625 6.625 0.625C6.03188 0.625 5.73533 0.625 5.50177 0.721731C5.34724 0.785697 5.20684 0.879489 5.08858 0.997748C4.97032 1.11601 4.87653 1.25641 4.81256 1.41094C4.75401 1.55286 4.73047 1.71895 4.72156 1.96015C4.71741 2.13447 4.66912 2.3049 4.5812 2.45548C4.49327 2.60607 4.36859 2.73189 4.21881 2.82118C4.06659 2.90631 3.89526 2.95143 3.72086 2.95232C3.54645 2.95321 3.37467 2.90984 3.22159 2.82627C3.00776 2.71299 2.85312 2.65063 2.69975 2.63026C2.36521 2.58627 2.02689 2.67692 1.75916 2.88227C1.55934 3.03692 1.41042 3.29338 1.11386 3.80695C0.817307 4.32051 0.668392 4.57698 0.635936 4.82835C0.614067 4.9941 0.625067 5.16254 0.668307 5.32403C0.711548 5.48553 0.786182 5.63693 0.887947 5.76957C0.982132 5.89176 1.11387 5.99422 1.31815 6.12277C1.61916 6.31178 1.81262 6.63379 1.81262 6.9889C1.81262 7.344 1.61916 7.66602 1.31815 7.85439C1.11387 7.98357 0.981496 8.08603 0.887947 8.20822C0.786182 8.34087 0.711548 8.49226 0.668307 8.65376C0.625067 8.81526 0.614067 8.98369 0.635936 9.14944C0.669029 9.40018 0.817307 9.65728 1.11323 10.1708C1.41042 10.6844 1.5587 10.9409 1.75916 11.0955C1.89181 11.1973 2.04321 11.2719 2.2047 11.3152C2.3662 11.3584 2.53464 11.3694 2.70038 11.3475C2.85312 11.3272 3.00776 11.2648 3.22159 11.1515C3.37467 11.068 3.54645 11.0246 3.72086 11.0255C3.89526 11.0264 4.06659 11.0715 4.21881 11.1566C4.52619 11.3348 4.70883 11.6625 4.72156 12.0176C4.73047 12.2595 4.75338 12.4249 4.81256 12.5669C4.87653 12.7214 4.97032 12.8618 5.08858 12.98C5.20684 13.0983 5.34724 13.1921 5.50177 13.2561C5.73533 13.3528 6.03188 13.3528 6.625 13.3528C7.21812 13.3528 7.51467 13.3528 7.74823 13.2561C7.90276 13.1921 8.04316 13.0983 8.16142 12.98C8.27968 12.8618 8.37347 12.7214 8.43744 12.5669C8.49599 12.4249 8.51953 12.2595 8.52844 12.0176C8.54117 11.6625 8.72381 11.3342 9.03119 11.1566C9.18341 11.0715 9.35474 11.0264 9.52914 11.0255C9.70355 11.0246 9.87533 11.068 10.0284 11.1515C10.2422 11.2648 10.3969 11.3272 10.5496 11.3475C10.7154 11.3694 10.8838 11.3584 11.0453 11.3152C11.2068 11.2719 11.3582 11.1973 11.4908 11.0955C11.6913 10.9415 11.8396 10.6844 12.1361 10.1708C12.4327 9.65728 12.5816 9.40081 12.6141 9.14944C12.6359 8.98369 12.6249 8.81526 12.5817 8.65376C12.5385 8.49226 12.4638 8.34087 12.3621 8.20822C12.2679 8.08603 12.1361 7.98357 11.9319 7.85502C11.7829 7.76427 11.6594 7.63721 11.5729 7.48573C11.4864 7.33425 11.4398 7.16331 11.4374 6.9889C11.4374 6.63379 11.6308 6.31178 11.9319 6.12341C12.1361 5.99422 12.2685 5.89176 12.3621 5.76957C12.4638 5.63693 12.5385 5.48553 12.5817 5.32403C12.6249 5.16254 12.6359 4.9941 12.6141 4.82835C12.581 4.57762 12.4327 4.32051 12.1368 3.80695C11.8396 3.29338 11.6913 3.03692 11.4908 2.88227C11.3582 2.78051 11.2068 2.70588 11.0453 2.66263C10.8838 2.61939 10.7154 2.60839 10.5496 2.63026C10.3969 2.65063 10.2422 2.71299 10.0278 2.82627C9.87477 2.90973 9.70311 2.95304 9.52883 2.95215C9.35454 2.95126 9.18333 2.9062 9.03119 2.82118C8.88141 2.73189 8.75673 2.60607 8.6688 2.45548C8.58088 2.3049 8.53259 2.13447 8.52844 1.96015C8.51953 1.71832 8.49662 1.55286 8.43744 1.41094C8.37347 1.25641 8.27968 1.11601 8.16142 0.997748C8.04316 0.879489 7.90276 0.785697 7.74823 0.721731Z"
                            stroke="#3B3731" stroke-width="1.25" />
                    </svg>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </aside>
</div>
