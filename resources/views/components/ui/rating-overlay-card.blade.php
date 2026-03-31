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

        .fs-card-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            padding: 20px;
            color: #fff;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-align: center;
            border-radius: 10px 10px 0 0;
            border: 1px solid #ffc97a;
            background: #ffc97a;
            z-index: 1000;
        }

        .fs-rating-modal {
            width: 400px;
            height: 550px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #ffc97a;
        }

        /* header */

        .fs-rating-header {
            background: #f4b86a;
            color: white;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .fs-back {
            cursor: pointer;
        }

        .fs-rating-content {
            text-align: center;
            padding: 10rem 20px;
        }

        .fs-rating-content h2 {
            color: #3b3731;
            text-align: center;
            font-family: Lato;
            font-size: 28px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        /* stars */

        .fs-stars {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 20px 0;
            font-size: 28px;
            color: #f4b86a;
        }

        .fs-stars .fs-inactive {
            color: #ddd;
        }

        /* text */

        .fs-ref {
            color: #9d9b98;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-top: 2rem;
        }

        .fs-end {
            margin-top: 5.5rem;
            color: #9d9b98;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-back-arrow {
            position: relative;
            right: 4.5rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <section class="fs-rating-overlay">

        <div class="fs-rating-modal">

            <div class="fs-card-header">
                <span class="fs-back-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="16" viewBox="0 0 9 16"
                        fill="none">
                        <path d="M7.74365 14.3737L1.00021 7.63022L7.63045 0.999976" stroke="white" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
                <span class="fs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="25" viewBox="0 0 34 25"
                        fill="none">
                        <path
                            d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                            stroke="#FFF8EE" stroke-width="2" stroke-linecap="round" />
                        <path
                            d="M13.0184 19.3179C11.9642 19.3179 10.7659 19.1159 9.7326 18.9188C9.66412 18.9064 9.59363 18.9117 9.52775 18.9341C9.46187 18.9565 9.40276 18.9952 9.35598 19.0467C8.33359 20.1998 7.20024 21.0923 6.26259 21.7256C5.63442 22.1499 4.82848 21.6768 4.82848 20.9188V17.7044C4.82845 17.6391 4.81254 17.5748 4.78211 17.5171C4.75169 17.4593 4.70767 17.4098 4.65385 17.3729C2.53093 15.9122 0 13.4594 0 10.0609C0 4.41886 5.54148 0 12.616 0C19.6463 0 24.9471 4.3247 24.9471 10.0593C24.9471 15.5976 20.1533 19.3179 13.0184 19.3179Z"
                            fill="#FFF8EE" />
                    </svg></span> FursGo Support
            </div>

            <div class="fs-rating-content">

                <h2>How helpful was<br>this conversation?</h2>

                <div class="fs-stars">
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="34" height="33" viewBox="0 0 34 33" fill="none">
                            <path
                                d="M14.9871 1.39799C15.5755 -0.466011 18.2133 -0.466007 18.8016 1.39799L21.3879 9.59177C21.6505 10.424 22.4225 10.9898 23.2951 10.9898L31.785 10.9898C33.7084 10.9898 34.5231 13.4394 32.9828 14.5914L26.0156 19.8022C25.3349 20.3113 25.0503 21.1952 25.3062 22.0058L27.9438 30.3621C28.5286 32.2149 26.3946 33.7294 24.8387 32.5657L18.0922 27.52C17.382 26.9888 16.4067 26.9888 15.6965 27.52L8.95006 32.5657C7.39412 33.7294 5.26012 32.2149 5.84496 30.3621L8.4825 22.0058C8.73837 21.1952 8.45385 20.3113 7.77311 19.8022L0.80589 14.5914C-0.734361 13.4394 0.0803623 10.9898 2.00374 10.9898L10.4936 10.9898C11.3663 10.9898 12.1382 10.424 12.4008 9.59177L14.9871 1.39799Z"
                                fill="#FFC97A" />
                        </svg></span>
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="34" height="33" viewBox="0 0 34 33" fill="none">
                            <path
                                d="M14.9871 1.39799C15.5755 -0.466011 18.2133 -0.466007 18.8016 1.39799L21.3879 9.59177C21.6505 10.424 22.4225 10.9898 23.2951 10.9898L31.785 10.9898C33.7084 10.9898 34.5231 13.4394 32.9828 14.5914L26.0156 19.8022C25.3349 20.3113 25.0503 21.1952 25.3062 22.0058L27.9438 30.3621C28.5286 32.2149 26.3946 33.7294 24.8387 32.5657L18.0922 27.52C17.382 26.9888 16.4067 26.9888 15.6965 27.52L8.95006 32.5657C7.39412 33.7294 5.26012 32.2149 5.84496 30.3621L8.4825 22.0058C8.73837 21.1952 8.45385 20.3113 7.77311 19.8022L0.80589 14.5914C-0.734361 13.4394 0.0803623 10.9898 2.00374 10.9898L10.4936 10.9898C11.3663 10.9898 12.1382 10.424 12.4008 9.59177L14.9871 1.39799Z"
                                fill="#FFC97A" />
                        </svg></span>
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="34" height="33" viewBox="0 0 34 33" fill="none">
                            <path
                                d="M14.9871 1.39799C15.5755 -0.466011 18.2133 -0.466007 18.8016 1.39799L21.3879 9.59177C21.6505 10.424 22.4225 10.9898 23.2951 10.9898L31.785 10.9898C33.7084 10.9898 34.5231 13.4394 32.9828 14.5914L26.0156 19.8022C25.3349 20.3113 25.0503 21.1952 25.3062 22.0058L27.9438 30.3621C28.5286 32.2149 26.3946 33.7294 24.8387 32.5657L18.0922 27.52C17.382 26.9888 16.4067 26.9888 15.6965 27.52L8.95006 32.5657C7.39412 33.7294 5.26012 32.2149 5.84496 30.3621L8.4825 22.0058C8.73837 21.1952 8.45385 20.3113 7.77311 19.8022L0.80589 14.5914C-0.734361 13.4394 0.0803623 10.9898 2.00374 10.9898L10.4936 10.9898C11.3663 10.9898 12.1382 10.424 12.4008 9.59177L14.9871 1.39799Z"
                                fill="#FFC97A" />
                        </svg></span>
                    <span><svg xmlns="http://www.w3.org/2000/svg" width="34" height="33" viewBox="0 0 34 33" fill="none">
                            <path
                                d="M14.9871 1.39799C15.5755 -0.466011 18.2133 -0.466007 18.8016 1.39799L21.3879 9.59177C21.6505 10.424 22.4225 10.9898 23.2951 10.9898L31.785 10.9898C33.7084 10.9898 34.5231 13.4394 32.9828 14.5914L26.0156 19.8022C25.3349 20.3113 25.0503 21.1952 25.3062 22.0058L27.9438 30.3621C28.5286 32.2149 26.3946 33.7294 24.8387 32.5657L18.0922 27.52C17.382 26.9888 16.4067 26.9888 15.6965 27.52L8.95006 32.5657C7.39412 33.7294 5.26012 32.2149 5.84496 30.3621L8.4825 22.0058C8.73837 21.1952 8.45385 20.3113 7.77311 19.8022L0.80589 14.5914C-0.734361 13.4394 0.0803623 10.9898 2.00374 10.9898L10.4936 10.9898C11.3663 10.9898 12.1382 10.424 12.4008 9.59177L14.9871 1.39799Z"
                                fill="#FFC97A" />
                        </svg></span>
                    <span class="fs-inactive"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="33"
                            viewBox="0 0 34 33" fill="none">
                            <path
                                d="M14.9871 1.39799C15.5755 -0.466011 18.2133 -0.466007 18.8016 1.39799L21.3879 9.59177C21.6505 10.424 22.4225 10.9898 23.2951 10.9898L31.785 10.9898C33.7084 10.9898 34.5231 13.4394 32.9828 14.5914L26.0156 19.8022C25.3349 20.3113 25.0503 21.1952 25.3062 22.0058L27.9438 30.3621C28.5286 32.2149 26.3946 33.7294 24.8387 32.5657L18.0922 27.52C17.382 26.9888 16.4067 26.9888 15.6965 27.52L8.95006 32.5657C7.39412 33.7294 5.26012 32.2149 5.84496 30.3621L8.4825 22.0058C8.73837 21.1952 8.45385 20.3113 7.77311 19.8022L0.80589 14.5914C-0.734361 13.4394 0.0803623 10.9898 2.00374 10.9898L10.4936 10.9898C11.3663 10.9898 12.1382 10.424 12.4008 9.59177L14.9871 1.39799Z"
                                fill="#EFEFEF" />
                        </svg></span>
                </div>

                <p class="fs-ref">Your reference ID: FG-48291</p>

                <p class="fs-end">End of conversation.</p>

            </div>

        </div>


    </section>

</body>

</html>
