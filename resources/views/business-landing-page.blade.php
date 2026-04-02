@extends('layouts.app')

@section('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #FFF;
        }

        .blp-hero {
            height: 100vh;
            width: 100%;
            height: 472px;
            background: linear-gradient(rgba(173, 216, 230, 0.4), rgba(173, 216, 230, 0.4)),
                url('/images/blp-hero-section-imagerectangle-293.png');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            border-radius: 10px
        }

        .blp-hero-content {
            /* max-width: 900px; */
            color: #ffffff;
        }

        .blp-sub-text {
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 12px;
            opacity: 0.9;
        }

        .blp-main-title {
            color: #FFF;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 10px;
            text-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .blp-cta-text {
            color: #FFF;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: 250%;
            margin-bottom: 15px;
        }

        .blp-btn {
            display: inline-block;
            padding: 11px 0px;
            width: 223px;
            height: 48px;
            background-color: #FFC97A;
            text-decoration: none;
            border-radius: 50px;
            transition: transform 0.3s ease, background-color 0.3s ease;
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .blp-btn:hover {
            transform: translateY(-3px);
        }

        .blp-features-section {
            padding: 80px 2px;
        }

        .blp-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 60px;
            align-items: start;
            width: 100%;
            margin-top: 3rem
        }

        .blp-feature-title {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-bottom: 10px;
        }

        .blp-feature-description {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* Right Side Grid Styling */
        .blp-features-grid {
            display: grid;
            grid-template-columns: 256px 256px;
            gap: 4rem;
            justify-content: center;
        }

        .blp-feature-item {
            display: flex;
            flex-direction: column;
        }

        .blp-feature-item .blp-icon {
            margin-bottom: 15px;
        }

        .blp-feature-item h3 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 15px;
        }

        .blp-des {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-top: 10px;
        }

        .blp-signup-process {
            /* background-color: #fff; */
            text-align: center;
            cursor: default;
            padding: 80px 28px;
        }

        .blp-process-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 70px;
            color: #3d3d3d;
        }

        .blp-process-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .blp-step-card {
            background: #FBFBFB;
            width: 295px;
            /* height: 266px; */
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        /* Zigzag Effect */
        .offset-down {
            margin-top: 60px;

        }

        .blp-card-content {
            padding: 2px 16px;
            text-align: left;
        }

        .blp-card-content h3 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 15px;
            margin-top: 1rem
        }

        .blp-card-content p {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin: 2rem 0;
        }

        .blp-card-footer {
            background-color: #FFC97A;
            padding: 20px 20px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            width: 295px;
            height: 70px;
        }

        .blp-card-footer span {
            color: #FFF;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        /* Hover effect */
        .blp-step-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection


@section('content')

    <section class="container">
        <div class="blp-hero">
            <div class="blp-hero-content">
                <p class="blp-sub-text">→ Create something that's yours — structured, flexible, and built on your terms.</p>
                <h1 class="blp-main-title">Step into a new opportunity — built around your love for animals.</h1>
                <p class="blp-cta-text">Begin your application.</p>
                <a href="#" class="blp-btn">Get Started</a>
            </div>
        </div>

    </section>

    <section class="container">
        <div class="blp-features-section">
            <div class="blp-container">
                <div class="blp-feature-header">
                    <h2 class="blp-feature-title">Build It Around <br><span>Your Life</span></h2>
                    <p class="blp-feature-description">Choose your availability, decide who you work with,<br /> and grow at
                        a
                        pace
                        that fits you.
                    </p>
                </div>



                <div class="blp-features-grid">
                    <div class="blp-feature-item">
                        <div class="blp-icon"><svg xmlns="http://www.w3.org/2000/svg" width="36" height="33"
                                viewBox="0 0 36 33" fill="none">
                                <path
                                    d="M1.87365 12.4194C1.80371 13.6799 1.80371 15.1796 1.80371 16.9812V19.8308C1.80371 25.2038 1.80529 27.8895 3.659 29.5594C5.51271 31.2293 8.49828 31.2293 14.4678 31.2293H20.7999C26.7694 31.2293 29.7534 31.2279 31.6087 29.5594C33.464 27.891 33.464 25.2038 33.464 19.8308V16.9812C33.464 15.1794 33.4638 13.6799 33.3937 12.4194H1.87365Z"
                                    fill="#FFC97A" fill-opacity="0.125" />
                                <path
                                    d="M12.6058 21.9283L16.0083 25.3308L22.5803 18.7588C22.8891 18.45 22.89 17.9496 22.5823 17.6397C22.273 17.3283 21.7696 17.3274 21.4592 17.6377L16.0083 23.0886L13.7229 20.8096C13.4141 20.5017 12.9142 20.502 12.6058 20.8104C12.2971 21.1191 12.2971 21.6196 12.6058 21.9283Z"
                                    fill="#FFC97A" />
                                <path
                                    d="M0.5 16.0901C0.5 9.90189 0.5 6.80699 2.5091 4.88539C4.51821 2.9638 7.74957 2.96216 14.214 2.96216H21.071C27.5355 2.96216 30.7686 2.96216 32.7759 4.88539C34.7833 6.80863 34.785 9.90189 34.785 16.0901V19.372C34.785 25.5602 34.785 28.6551 32.7759 30.5767C30.7668 32.4983 27.5355 32.4999 21.071 32.4999H14.214C7.74957 32.4999 4.51649 32.4999 2.5091 30.5767C0.501714 28.6534 0.5 25.5602 0.5 19.372V16.0901Z"
                                    stroke="#3B3731" />
                                <path d="M8.81195 2.94973V0.5M26.4555 2.94973V0.5M0.872375 11.1155H34.395" stroke="#3B3731"
                                    stroke-linecap="round" />
                            </svg></div>
                        <h3>Set Your Schedule & Your Rates</h3>
                        <p class="blp-des">Decide when you're available and what your services are worth.</p>
                    </div>

                    <div class="blp-feature-item">
                        <div class="blp-icon"><svg xmlns="http://www.w3.org/2000/svg" width="31" height="32"
                                viewBox="0 0 31 32" fill="none">
                                <ellipse cx="18.2383" cy="12.5802" rx="6.89039" ry="10.8278"
                                    transform="rotate(43.8908 18.2383 12.5802)" fill="#FFF8EE" />
                                <path
                                    d="M27.3415 5.37256C28.8048 6.83588 28.7829 9.15223 28.0407 11.3789C27.2787 13.6646 25.6723 16.1762 23.434 18.4145C21.1956 20.6529 18.684 22.2593 16.3983 23.0212C14.1716 23.7635 11.8553 23.7854 10.392 22.322"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M16.742 3.90709C17.1061 3.54302 17.6963 3.54302 18.0603 3.90709L19.0375 4.88426C19.4013 5.24835 19.4015 5.83855 19.0375 6.20254C18.6735 6.56654 18.0833 6.56639 17.7192 6.20254L16.742 5.22537C16.378 4.86129 16.378 4.27116 16.742 3.90709Z"
                                    fill="#3B3731" />
                                <path
                                    d="M13.323 7.32652C13.6871 6.96245 14.2772 6.96245 14.6413 7.32652L15.6184 8.3037C15.9823 8.66779 15.9824 9.25798 15.6184 9.62198C15.2544 9.98597 14.6642 9.98581 14.3002 9.62198L13.323 8.6448C12.9589 8.28073 12.9589 7.69059 13.323 7.32652Z"
                                    fill="#3B3731" />
                                <path
                                    d="M10.3917 11.2347C10.7558 10.8707 11.3459 10.8707 11.71 11.2347L12.6872 12.2119C13.051 12.576 13.0512 13.1662 12.6872 13.5302C12.3232 13.8942 11.733 13.894 11.3689 13.5302L10.3917 12.553C10.0276 12.1889 10.0276 11.5988 10.3917 11.2347Z"
                                    fill="#3B3731" />
                                <path
                                    d="M9.41502 16.119C9.77909 15.7549 10.3692 15.7549 10.7333 16.119L11.7105 17.0962C12.0743 17.4603 12.0745 18.0505 11.7105 18.4145C11.3465 18.7784 10.7563 18.7783 10.3922 18.4145L9.41502 17.4373C9.05095 17.0732 9.05095 16.4831 9.41502 16.119Z"
                                    fill="#3B3731" />
                                <path
                                    d="M22.1153 2.44175C22.4794 2.07768 23.0695 2.07768 23.4336 2.44175L24.4108 3.41893C24.7746 3.78302 24.7748 4.37322 24.4108 4.73721C24.0468 5.10121 23.4566 5.10105 23.0925 4.73721L22.1153 3.76003C21.7513 3.39596 21.7513 2.80583 22.1153 2.44175Z"
                                    fill="#3B3731" />
                                <path
                                    d="M21.1384 8.30356C21.5025 7.9395 22.0926 7.9395 22.4567 8.30356L24.411 10.2579C24.7747 10.622 24.775 11.2123 24.411 11.5762C24.0471 11.9401 23.4569 11.9398 23.0928 11.5762L21.1384 9.62184C20.7743 9.25778 20.7743 8.66764 21.1384 8.30356Z"
                                    fill="#3B3731" />
                                <path
                                    d="M17.7195 11.723C18.0835 11.3589 18.6737 11.3589 19.0377 11.723L20.9921 13.6774C21.3557 14.0415 21.356 14.6317 20.9921 14.9956C20.6282 15.3596 20.0379 15.3592 19.6738 14.9956L17.7195 13.0413C17.3554 12.6772 17.3554 12.0871 17.7195 11.723Z"
                                    fill="#3B3731" />
                                <path
                                    d="M13.8113 14.6539C14.1753 14.2898 14.7655 14.2898 15.1295 14.6539L17.0839 16.6083C17.4475 16.9724 17.4478 17.5626 17.0839 17.9265C16.72 18.2905 16.1297 18.2902 15.7656 17.9265L13.8113 15.9722C13.4472 15.6081 13.4472 15.018 13.8113 14.6539Z"
                                    fill="#3B3731" />
                                <path
                                    d="M6.37407 29.8756C5.20066 31.049 3.29779 31.0482 2.12438 29.8748C0.951208 28.7013 0.951056 26.7993 2.12438 25.626L6.03245 21.7179C7.2058 20.5448 9.10792 20.5448 10.2813 21.7179C11.4546 22.8912 11.4553 24.7942 10.2821 25.9676L6.37407 29.8756Z"
                                    fill="#FFC97A" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></div>
                        <h3>Offer the Services That Suit You</h3>
                        <p class="blp-des">From full grooming to bath & brush and add-ons — shape your service menu
                            around
                            your skills.</p>
                    </div>

                    <div class="blp-feature-item">
                        <div class="blp-icon"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="32"
                                viewBox="0 0 40 32" fill="none">
                                <path
                                    d="M20 13.5908C23.2263 13.5908 25.6277 15.3434 27.1807 17.5742L27.1816 17.5752C28.7247 19.7792 29.5 22.5216 29.5 24.7275C29.4999 27.1842 28.0583 28.9093 26.2188 29.9727H26.2178C24.3976 31.0265 22.0897 31.5 20 31.5C17.9107 31.5 15.602 31.0241 13.7812 29.9727L13.4404 29.7656C11.7653 28.6935 10.5001 27.0312 10.5 24.7275C10.5 22.6595 11.1815 20.1205 12.5381 17.9951L12.8184 17.5752C14.3746 15.3462 16.7768 13.5908 20 13.5908ZM4.28613 13.5908C5.47515 13.591 6.44702 14.3301 7.07617 15.291C7.70959 16.2571 8.07129 17.5405 8.07129 18.9092C8.07127 20.2778 7.70962 21.5612 7.07617 22.5273L7.0752 22.5283C6.44657 23.4909 5.47547 24.2274 4.28613 24.2275C3.17122 24.2275 2.24678 23.5781 1.61719 22.7051L1.49512 22.5273C0.861688 21.5612 0.500015 20.2778 0.5 18.9092C0.5 17.5406 0.861724 16.2571 1.49512 15.291L1.49609 15.29C2.12479 14.3273 3.09653 13.5908 4.28613 13.5908ZM35.7139 13.5908C36.9031 13.5908 37.8757 14.33 38.5049 15.291C39.1383 16.2571 39.5 17.5406 39.5 18.9092C39.5 20.2778 39.1383 21.5612 38.5049 22.5273L38.5039 22.5283C37.8752 23.491 36.9034 24.2275 35.7139 24.2275C34.5992 24.2274 33.6754 23.578 33.0459 22.7051L32.9238 22.5273C32.2904 21.5612 31.9287 20.2778 31.9287 18.9092C31.9287 17.5405 32.2904 16.2571 32.9238 15.291L32.9248 15.29C33.5534 14.3274 34.5245 13.591 35.7139 13.5908ZM12.8574 0.5C13.9722 0.500108 14.8959 1.14949 15.5254 2.02246L15.6475 2.2002C16.2809 3.16632 16.6426 4.44971 16.6426 5.81836C16.6425 7.18699 16.2809 8.47042 15.6475 9.43652V9.4375C15.0188 10.4003 14.047 11.1366 12.8574 11.1367C11.7425 11.1367 10.8181 10.4873 10.1885 9.61426L10.0664 9.43652C9.43299 8.47043 9.07132 7.18696 9.07129 5.81836C9.07129 4.44981 9.43306 3.1663 10.0664 2.2002L10.0674 2.19922C10.6961 1.23639 11.6678 0.5 12.8574 0.5ZM27.1426 0.5C28.3318 0.5 29.3044 1.2392 29.9336 2.2002C30.5669 3.1663 30.9287 4.44981 30.9287 5.81836C30.9287 7.18696 30.567 8.47043 29.9336 9.43652L29.9326 9.4375C29.3039 10.4002 28.3321 11.1367 27.1426 11.1367C25.9535 11.1366 24.9817 10.3974 24.3525 9.43652L24.2373 9.25195C23.6754 8.30984 23.3575 7.10166 23.3574 5.81836C23.3574 4.44971 23.7191 3.16632 24.3525 2.2002V2.19922C24.9812 1.23644 25.953 0.500115 27.1426 0.5Z"
                                    fill="#FFF8EE" stroke="#3B3731" />
                            </svg></div>
                        <h3>Choose the Pets You're Comfortable With</h3>
                        <p class="blp-des">Set size, age and handling preferences so you always work within your confidence.
                        </p>
                    </div>

                    <div class="blp-feature-item">
                        <div class="blp-icon"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="32"
                                viewBox="0 0 26 32" fill="none">
                                <path
                                    d="M12.607 2.5144C12.8235 2.43702 13.0601 2.43663 13.2769 2.51328L23.3334 6.06921C23.7329 6.21048 24 6.58824 24 7.01201V17.7674C24 23.5313 15.4487 28.3339 13.4254 29.3895C13.1555 29.5303 12.8445 29.5303 12.5746 29.3895C10.5513 28.3339 2 23.5313 2 17.7674V7.0095C2 6.58696 2.26558 6.21002 2.66348 6.06783L12.607 2.5144Z"
                                    fill="#FFF8EE" />
                                <path
                                    d="M12.7656 0.589844C12.8733 0.551578 12.9908 0.551003 13.0986 0.588867L25.166 4.83496C25.3661 4.90539 25.5 5.09449 25.5 5.30664V18.0781C25.5 21.2679 23.0761 24.278 20.2246 26.6924C17.4015 29.0827 14.2953 30.7754 13.1924 31.3438C13.0667 31.4085 12.9343 31.4084 12.8086 31.3438H12.8076C11.7047 30.7754 8.59854 29.0827 5.77539 26.6924C2.92386 24.278 0.5 21.2679 0.5 18.0781V5.30371C0.50018 5.09246 0.633049 4.90395 0.832031 4.83301L12.7656 0.589844Z"
                                    stroke="#3B3731" />
                                <path
                                    d="M8.55895 16.8531L11.9614 20.2556L18.5334 13.6836C18.8423 13.3748 18.8431 12.8744 18.5354 12.5645C18.2262 12.2531 17.7227 12.2522 17.4124 12.5625L11.9614 18.0134L9.67607 15.7345C9.36725 15.4265 8.86735 15.4268 8.55895 15.7352C8.25025 16.0439 8.25025 16.5444 8.55895 16.8531Z"
                                    fill="#FFC97A" />
                            </svg></div>
                        <h3>Everything You Need, In One Place</h3>
                        <p class="blp-des">Manage bookings, availability, communication and payouts through a single, simple
                            platform.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="blp-signup-process">
            <h2 class="blp-process-title">Sign-Up Process Overview</h2>

            <div class="blp-process-container">
                <div class="blp-step-card">
                    <div class="blp-card-content">
                        <h3>Set Up Your Services</h3>
                        <p>Choose what you offer, set your rates, and define your availability.</p>
                    </div>
                    <div class="blp-card-footer">
                        <span>Step</span>
                        <span>01</span>
                    </div>
                </div>

                <div class="blp-step-card offset-down">
                    <div class="blp-card-content">
                        <h3>Complete Verification</h3>
                        <p>Finish screening and safety checks to maintain platform standards.</p>
                    </div>
                    <div class="blp-card-footer">
                        <span>Step</span>
                        <span>02</span>
                    </div>
                </div>

                <div class="blp-step-card">
                    <div class="blp-card-content">
                        <h3>Create Your Profile</h3>
                        <p>Share your experience,<br> upload photos of your work, and introduce yourself professionally.</p>
                    </div>
                    <div class="blp-card-footer">
                        <span>Step</span>
                        <span>03</span>
                    </div>
                </div>

                <div class="blp-step-card offset-down">
                    <div class="blp-card-content">
                        <h3>Review & Go Live</h3>
                        <p>Once approved, you'll start receiving booking opportunities.</p>
                    </div>
                    <div class="blp-card-footer">
                        <span>Step</span>
                        <span>04</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection