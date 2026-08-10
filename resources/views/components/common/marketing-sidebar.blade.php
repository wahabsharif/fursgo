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
    style="{{ $variant === 'dashboard' ? 'max-width: 190px; margin: 0; padding: 0; width: 100%; position: relative;' : 'position: relative;' }}">
    <style>
        :root {
            --sidebar-active-bg:
                {{ $activeBgColor }}
            ;
        }

        .dashboard-wrapper {
            display: flex;
            gap: 1.25rem;
            padding-top: 2rem;
            max-width: 1240px;
            width: min(1240px, calc(100% - 2rem));
            margin: 0 auto;
            box-sizing: border-box;
        }

        .aside {
            flex-shrink: 0;
            width: 190px;
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
            gap: 0;
            width: 100%;
            max-width: 190px;
        }

        .nav-section-label {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 0;
            margin: 0.5rem 0 0.5rem;
            list-style: none;
        }

        .nav-list>.nav-section-label:first-child {
            margin-top: 0;
        }

        .nav-item {
            position: relative;
            margin-bottom: 0.875rem;
        }

        .nav-item:last-child {
            margin-bottom: 0;
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
            gap: 0.625rem;
            padding: 0.8125rem 1.25rem;
            min-height: 48px;
            border-radius: 96px;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            box-sizing: border-box;
            width: 100%;
        }

        .nav-link svg {
            width: 12px;
            height: 12px;
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
            box-shadow: 0 0 10px 0 rgba(255, 216, 140, 0.7);
        }

        .nav-text {
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            flex: 1 1 auto;
            min-width: 0;
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
        <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <aside class="aside" :class="{ 'is-open': mobileOpen }">
        <ul class="nav-list">
            <li class="nav-section-label" aria-hidden="true">Overview</li>

            <li class="nav-item">
                <a href="{{ route('marketing-hub') }}"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'marketing-hub'; window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'marketing-hub' } }))"
                    :class="{ 'active': activeSection === 'marketing-hub' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none"
                        aria-hidden="true">
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

            <li class="nav-section-label" aria-hidden="true">Manage</li>

            <li class="nav-item">
                <a href="{{ route('marketing-hub') }}"
                    @click.prevent="window.dispatchEvent(new CustomEvent('nav-list-loading-start')); activeSection = 'promo-creation'; window.dispatchEvent(new CustomEvent('dashboard-nav-changed', { detail: { section: 'promo-creation' } }))"
                    :class="{ 'active': activeSection === 'promo-creation' }" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none"
                        aria-hidden="true">
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
        </ul>
    </aside>
</div>