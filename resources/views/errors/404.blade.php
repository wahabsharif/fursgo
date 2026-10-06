<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>Fursgo - Page not found</title>
    <x-partials.head />
</head>

<body class="error-404-page">
    <x-common.header :force-business-public="true" />

    <main class="error-404-main" role="main">
        <div class="error-404-content">
            <img class="error-404-graphic" src="{{ asset('images/errors/404-graphic.svg') }}" alt="404"
                width="302" height="120">

            <h1 class="error-404-title">This page has wandered off</h1>
            <p class="error-404-copy">We followed the trail but couldn't track down the page you're after.</p>

            <div class="error-404-actions">
                <a href="{{ route('business-hub') }}" class="error-404-btn error-404-btn--primary">
                    <img src="{{ asset('images/errors/icon-hub-grid.svg') }}" alt="" width="12" height="12"
                        aria-hidden="true">
                    <span>Back to Business Hub</span>
                </a>

                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('business-homepage-groomer-space-owner') }}"
                    class="error-404-btn error-404-btn--secondary"
                    onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }">
                    <span class="error-404-btn__chevron" aria-hidden="true">
                        <img src="{{ asset('images/errors/icon-go-back.svg') }}" alt="" width="5" height="5">
                    </span>
                    <span>Go back</span>
                </a>
            </div>
        </div>
    </main>

    <style>
        html:has(body.error-404-page),
        body.error-404-page {
            margin: 0;
            height: 100%;
            overflow: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        html:has(body.error-404-page)::-webkit-scrollbar,
        body.error-404-page::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        body.error-404-page {
            min-height: 100vh;
            background: #fff;
        }

        .error-404-main {
            min-height: calc(100vh - 85px);
            height: calc(100vh - 85px);
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 80px 24px 120px;
            background: #fffbf4;
            border-radius: 10px 10px 0 0;
            overflow: hidden;
        }

        .error-404-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            max-width: 720px;
            width: 100%;
        }

        .error-404-graphic {
            width: 302px;
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 20px;
        }

        .error-404-title {
            margin: 0;
            color: #3b3731;
            font-family: "Playfair Display", serif;
            font-size: 50px;
            font-style: italic;
            font-weight: 600;
            line-height: normal;
        }

        .error-404-copy {
            margin: 24px 0 0;
            color: #3b3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            white-space: nowrap;
        }

        .error-404-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 40px;
        }

        .error-404-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            box-sizing: border-box;
            height: 48px;
            padding: 0 22px;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 500;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition: color 0.25s ease, background-color 0.25s ease, border-color 0.25s ease,
                transform 0.25s ease, box-shadow 0.25s ease;
        }

        .error-404-btn--primary {
            min-width: 229px;
            color: #fff;
            background: #ffc97a;
            border: none;
            border-radius: 96px;
        }

        .error-404-btn--primary:hover {
            color: #fff;
            background: #f5b56a;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 201, 122, 0.4);
        }

        .error-404-btn--primary img {
            width: 12px;
            height: 12px;
            display: block;
            flex-shrink: 0;
        }

        .error-404-btn--secondary {
            min-width: 115px;
            color: #3b3731;
            background: #fff;
            border: 1px solid #3b3731;
            border-radius: 75px;
        }

        .error-404-btn--secondary:hover {
            color: #fff;
            background: #3b3731;
            border-color: #3b3731;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 55, 49, 0.18);
        }

        .error-404-btn__chevron {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }

        .error-404-btn__chevron img {
            width: 5px;
            height: 5px;
            display: block;
            transform: rotate(-135deg);
        }

        .error-404-btn--secondary:hover .error-404-btn__chevron img {
            filter: brightness(0) invert(1);
        }

        @media (max-width: 767.98px) {

            html:has(body.error-404-page),
            body.error-404-page {
                overflow-y: auto;
            }

            .error-404-main {
                height: auto;
                min-height: calc(100vh - 85px);
                padding: 48px 16px 80px;
                overflow: visible;
            }

            .error-404-graphic {
                width: 240px;
            }

            .error-404-title {
                font-size: 36px;
            }

            .error-404-copy {
                font-size: 16px;
                margin-top: 16px;
                white-space: normal;
                max-width: 100%;
            }

            .error-404-actions {
                flex-direction: column;
                width: 100%;
                margin-top: 32px;
            }

            .error-404-btn {
                width: 100%;
                max-width: 320px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .error-404-btn {
                transition: none;
            }

            .error-404-btn--primary:hover,
            .error-404-btn--secondary:hover {
                transform: none;
                box-shadow: none;
            }
        }
    </style>

    <script src="{{ asset('js/custom.js') }}" defer></script>
</body>

</html>
