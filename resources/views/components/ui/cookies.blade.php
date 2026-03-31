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

        /* fs-consent-overlay */

        .fs-card-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            padding: 20px;
            color: #FFF;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-align: center;
            border-radius: 10px 10px 0 0;
            border: 1px solid #ffc97a;
            background: #ffc97a;
            z-index: 1000;
        }

        .fs-consent-modal {
            width: 400px;
            height: 526px;
            overflow: hidden;
            font-family: Arial;
            border-radius: 10px;
            background: #FFF;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        }

        /* modal */

        .fs-consent-modal {
            width: 400px;
            height: 526px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            font-family: Arial;
        }

        /* header */

        .fs-consent-header {
            background: #f4b86a;
            color: white;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        /* content */

        .fs-consent-content {
            padding: 17px;
        }

        .fs-consent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .fs-consent-item h4 {
            margin: 0;
            color: #3b3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .fs-consent-item p {
            margin: 5px 0 0;
            color: #9d9b98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* toggle */

        .toggle {
            width: 48px;
            height: 26px;
            background: #e0e0e0;
            border-radius: 20px;
            position: relative;
            display: flex;
            align-items: center;
            padding: 3px;
            cursor: pointer;
            transition: 0.3s;
        }

        /* circle */

        .toggle .circle {
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: 0.3s;
        }

        /* active */

        .toggle.active {
            background: #f4b86a;
            justify-content: flex-end;
        }

        .toggle.active .circle {
            color: #f4b86a;
            font-weight: bold;
        }

        /* disabled */

        .toggle.disabled {
            background: #ddd;
            cursor: not-allowed;
            justify-content: flex-end;
        }

        .toggle.disabled .circle {
            background: #ccc;
            font-size: 12px;
        }

        /* buttons */

        .fs-consent-buttons {
            text-align: center;
        }

        .fs-accept-all {
            width: 360px;
            height: 48px;
            padding: 12px;
            border: none;
            border-radius: 25px;
            background: #f4b86a;
            color: white;
            margin-bottom: 10px;
            cursor: pointer;
            color: #fff;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-save-btn {
            width: 360px;
            height: 48px;
            padding: 12px;
            border-radius: 25px;
            border: 1px solid #ccc;
            background: white;
            cursor: pointer;
            color: #3b3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .vertical-line-1 {
            width: 1px;
            height: 20px;
            background: #d4d4d4;
        }
    </style>
</head>

<body>

    <section class="fs-consent-overlay">

        <div class="fs-consent-modal">

            <div class="fs-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="16" viewBox="0 0 9 16" fill="none">
                    <path d="M7.74365 14.3735L1.00021 7.63009L7.63045 0.999853" stroke="white" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg></span>
                <span class="fs-header-title"> Manage Consent Preferences</span>
            </div>

            <!-- Content -->
            <div class="fs-consent-content">

                <!-- Item -->
                <div class="fs-consent-item">
                    <div>
                        <h4>Strictly Necessary Cookies</h4>
                        <p>Required for the website to function and cannot be switched off.</p>
                    </div>
                    <div class="disabled ">
                        <span><svg xmlns="http://www.w3.org/2000/svg" width="44" height="24" viewBox="0 0 44 24"
                                fill="none">
                                <rect width="44" height="24" rx="12" fill="#D4D4D4" />
                                <path
                                    d="M18.3333 18C17.9667 18 17.6529 17.8696 17.392 17.6087C17.1311 17.3478 17.0004 17.0338 17 16.6667V10C17 9.63333 17.1307 9.31956 17.392 9.05867C17.6533 8.79778 17.9671 8.66711 18.3333 8.66667H19V7.33333C19 6.41111 19.3251 5.62511 19.9753 4.97533C20.6256 4.32556 21.4116 4.00044 22.3333 4C23.2551 3.99956 24.0413 4.32467 24.692 4.97533C25.3427 5.626 25.6676 6.412 25.6667 7.33333V8.66667H26.3333C26.7 8.66667 27.014 8.79733 27.2753 9.05867C27.5367 9.32 27.6671 9.63378 27.6667 10V16.6667C27.6667 17.0333 27.5362 17.3473 27.2753 17.6087C27.0144 17.87 26.7004 18.0004 26.3333 18H18.3333ZM22.3333 14.6667C22.7 14.6667 23.014 14.5362 23.2753 14.2753C23.5367 14.0144 23.6671 13.7004 23.6667 13.3333C23.6662 12.9662 23.5358 12.6524 23.2753 12.392C23.0149 12.1316 22.7009 12.0009 22.3333 12C21.9658 11.9991 21.652 12.1298 21.392 12.392C21.132 12.6542 21.0013 12.968 21 13.3333C20.9987 13.6987 21.1293 14.0127 21.392 14.2753C21.6547 14.538 21.9684 14.6684 22.3333 14.6667ZM20.3333 8.66667H24.3333V7.33333C24.3333 6.77778 24.1389 6.30556 23.75 5.91667C23.3611 5.52778 22.8889 5.33333 22.3333 5.33333C21.7778 5.33333 21.3056 5.52778 20.9167 5.91667C20.5278 6.30556 20.3333 6.77778 20.3333 7.33333V8.66667Z"
                                    fill="white" />
                            </svg></span>
                    </div>
                </div>

                <div class="fs-consent-item">
                    <div>
                        <h4>Performance Cookies</h4>
                        <p>Help us understand how the site is used so we can improve performance and experience.</p>
                    </div>
                    <div class="active">
                        <div class="circle">
                            <span><svg xmlns="http://www.w3.org/2000/svg" width="44" height="24" viewBox="0 0 44 24"
                                    fill="none">
                                    <rect width="44" height="24" rx="12" fill="#FFC97A" />
                                    <path
                                        d="M32 2C26.5 2 22 6.5 22 12C22 17.5 26.5 22 32 22C37.5 22 42 17.5 42 12C42 6.5 37.5 2 32 2ZM30.4167 16.5833C30.1865 16.8135 29.8135 16.8135 29.5833 16.5833L25.7055 12.7055C25.3159 12.3159 25.3159 11.6841 25.7055 11.2945C26.0947 10.9053 26.7257 10.9048 27.1155 11.2935L30 14.17L36.88 7.29C37.2717 6.89829 37.9072 6.89941 38.2975 7.29251C38.6859 7.68365 38.6848 8.31524 38.295 8.70501L30.4167 16.5833Z"
                                        fill="white" />
                                </svg></span>
                        </div>
                    </div>
                </div>

                <div class="fs-consent-item">
                    <div>
                        <h4>Functional Cookies</h4>
                        <p>Enable extra features and remember your preferences.</p>
                    </div>
                    <div class="toggle-1">
                        <div>
                            <span><svg xmlns="http://www.w3.org/2000/svg" width="44" height="24" viewBox="0 0 44 24"
                                    fill="none">
                                    <rect width="44" height="24" rx="12" fill="#D4D4D4" />
                                    <path
                                        d="M12 2C6.5 2 2 6.5 2 12C2 17.5 6.5 22 12 22C17.5 22 22 17.5 22 12C22 6.5 17.5 2 12 2Z"
                                        fill="white" />
                                </svg></span>
                        </div>
                    </div>
                </div>

                <div class="fs-consent-item">
                    <div>
                        <h4>Marketing Cookies</h4>
                        <p>Used to show relevant content and measure marketing effectiveness.</p>
                    </div>

                    <div class="toggle-1 active">
                        <div>
                            <span><svg xmlns="http://www.w3.org/2000/svg" width="44" height="24" viewBox="0 0 44 24"
                                    fill="none">
                                    <rect width="44" height="24" rx="12" fill="#FFC97A" />
                                    <path
                                        d="M32 2C26.5 2 22 6.5 22 12C22 17.5 26.5 22 32 22C37.5 22 42 17.5 42 12C42 6.5 37.5 2 32 2ZM30.4167 16.5833C30.1865 16.8135 29.8135 16.8135 29.5833 16.5833L25.7055 12.7055C25.3159 12.3159 25.3159 11.6841 25.7055 11.2945C26.0947 10.9053 26.7257 10.9048 27.1155 11.2935L30 14.17L36.88 7.29C37.2717 6.89829 37.9072 6.89941 38.2975 7.29251C38.6859 7.68365 38.6848 8.31524 38.295 8.70501L30.4167 16.5833Z"
                                        fill="white" />
                                </svg></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Buttons -->
            <div class="fs-consent-buttons">
                <button class="fs-accept-all">Accept all cookies</button>
                <button class="fs-save-btn">Save settings</button>
            </div>

        </div>

    </section>
    <script>
        const toggles = document.querySelectorAll(".toggle");

        toggles.forEach(toggle => {

            if (!toggle.classList.contains("disabled")) {

                toggle.addEventListener("click", () => {
                    toggle.classList.toggle("active");
                });

            }

        });
    </script>

</body>

</html>
