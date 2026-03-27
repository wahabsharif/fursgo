<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fursgo - Cookies</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }


        /* fs-cookie-overlay */

        .fs-cookie-overlay {
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* modal */

        .fs-cookie-modal {
            width: 440px;
            height: 314px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            font-family: Arial;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* header */

        .fs-cookie-header {
            background: #ffc97a;
            color: white;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .fs-close-btn {
            cursor: pointer;
        }

        /* content */

        .fs-cookie-content {
            padding: 20px;
            /* text-align: center; */
            width: 360px;
        }

        .fs-cookie-content p {
            color: #3b3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* buttons row */

        .fs-cookie-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 40px;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .fs-allow-btn {
            background: #ffc97a;
            border: none;
            width: 118px;
            height: 48px;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            color: #fff;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-decline-btn {
            background: #ffc97a;
            border: none;
            width: 252px;
            height: 48px;
            /* padding: 5px 1px; */
            padding: 0.9px;
            border-radius: 25px;
            opacity: 0.85;
            cursor: pointer;
            color: #fff;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* manage button */

        .fs-manage-btn {
            width: 360px;
            height: 48px;
            border-radius: 25px;
            border: 1px solid #d4d4d4;
            background-color: #ffffff;
            cursor: pointer;
            margin-bottom: 15px;
            margin-left: 1rem;
        }

        .fs-manage-btn p {
            color: #3b3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* link */

        .fs-learn-more {
            margin-top: 1rem;
            margin-left: 5rem;
            text-align: center;
        }

        .fs-learn-more a {
            color: #3b3731;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: 4px;
            text-underline-position: from-font;
        }

        /* fs-consent-overlay */

        .fs-consent-overlay {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

</head>

<body>

    <section class="fs-cookie-overlay booking-container">

        <div class="fs-cookie-modal">


            <div class="fs-cookie-header">
                <span>Privacy Preference Center</span>
                <span class="fs-close-btn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" stroke="white" />
                        <path d="M7.11133 13.3331L13.3336 7.11084M7.11133 7.11084L13.3336 13.3331" stroke="white"
                            stroke-width="1.5" stroke-linecap="round" />
                    </svg></span>
            </div>


            <div class="fs-cookie-content">

                <p>
                    We use cookies to make our site work and improve your experience.
                    You can manage your cookie preferences at any time.
                </p>

                <div class="fs-cookie-buttons">

                    <button class="fs-allow-btn">Allow All</button>
                    <button class="fs-decline-btn">Decline unnecessary cookies</button>

                </div>

                <button class="fs-manage-btn">
                    <p>Manage consent preferences</p>
                </button>

                <div class="fs-learn-more">
                    <a href="#">Learn More</a>
                </div>



            </div>

        </div>

    </section>

</body>

</html>
