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


    .bg-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .business-groomer {
        width: 100%;
        margin: 40px auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #FBFBFB;
        border-radius: 10px;
        overflow: hidden;
    }

    /* LEFT */
    .bg-left {
        width: 50%;
        padding: 40px;
    }

    .subtitle {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 2rem
    }

    .bg-left h1 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: 110%;
        margin-bottom: 12px;
    }

    .desc {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-bottom: 22px;
    }

    /* BUTTONS */
    .buttons {
        display: flex;
        gap: 12px;
        margin-top: 4rem;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 25px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .primary {
        background: #FBAC83;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        width: 215px;
        height: 48px;
    }

    .secondary {
        background: #FBFBFB;
        border: 1px solid #3B3731;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }


    .bg-right img {
        width: 505px;
        height: 500px;
        aspect-ratio: 101/100;
        object-fit: cover;
    }

    /* SECTION */
    .gf-section {
        width: 100%;
        margin: 40px auto;
        display: flex;
        flex-direction: column;
    }

    .main-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .cards-container {
        display: flex;
        gap: 20px;
        width: 100%;
    }

    .card {
        border: 1px solid #e2e8f0;
        background: #fff;
        /* border-radius: 10px; */
        padding: 30px 25px;
        width: 295px;
        height: 349px;
        position: relative;
        /* border: 1px solid transparent; */
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }

    .card-orange {
        border: 1px solid #FCC1A2;
    }

    .card-blue {
        border-color: #d1e9ff;
    }

    .card-dark-orange {
        border: 1px solid #FFBFB3;
    }

    .card-green {
        border: 1px solid #DFEDC5;
    }


    .card.active {
        /* border: 2px solid #3498db; */
        box-shadow: 0 10px 20px rgba(52, 152, 219, 0.1);
    }

    .card h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 20px;
    }

    .card p {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .icon-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        bottom: 25px;
        right: 25px;
    }

    /* Safety Section */

    .safety-section {
        background: #F5F9ED;
        padding: 80px 10%;
        display: flex;
        justify-content: center;
    }

    .safety-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        max-width: 1100px;
        align-items: center;
    }

    /* Left Side Styles */
    .safety-left {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .safety-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .shield-img {
        max-width: 250px;
        height: auto;
    }

    /* Right Side Styles */
    .safety-right {
        display: flex;
        flex-direction: column;
    }

    .feature-item {
        border-top: 1px solid #3B3731;
        padding: 35px 0;
    }



    .feature-item h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        margin: 0 0 2px 0;
    }

    .feature-item p {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .crm-section {
        padding: 60px 20px;
        text-align: center;
        background-color: #ffffff;
    }

    .crm-main-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        margin-bottom: 40px;
    }

    .crm-cards-wrapper {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;

    }

    .crm-card {
        background-color: #eef4f8;
        padding: 40px 30px;
        border-radius: 12px;
        width: 610px;
        height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: transform 0.3s ease;
    }


    .crm-card:hover {
        transform: translateY(-5px);
    }

    .crm-card h3 {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        margin: 0 0 10px 0;
        width: 317px;
    }

    .crm-card p {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        width: 317px;
    }

    .works-section {
        background-color: #fff;
        /* Light cream background */
        padding: 80px 5%;
        width: 100%;
    }

    /* Header Alignment */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 50px;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }

    .main-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        margin: 0;
    }

    .sub-header-text {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    /* Cards Wrapper */
    .steps-wrapper {
        display: flex;
        justify-content: center;
        gap: 25px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Card Style */
    .step-card {
        background: white;
        /* border-radius: 15px; */
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 350px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .card-content {
        padding: 40px 30px;
        width: 400px;
        height: 350px;
    }

    .card-content h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-bottom: 4rem;
    }

    .card-content h3 strong {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .card-content p {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    /* Orange Footer Bar */
    .step-footer {
        background-color: #FFC97A;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        color: #FFF;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }
</style>
@endsection

@section('content')
<div class="container">

    <section class="business-groomer ">

        <!-- LEFT CONTENT -->
        <div class="bg-left">
            <p class="subtitle">→ Grow your grooming business</p>

            <h1>
                Set your own schedule,<br>
                pricing & preferences.
            </h1>

            <p class="desc">
                Join thousands of professionals earning more with FursGo.<br>
                You control when you work, what you charge, and who you serve.
            </p>

            <div class="buttons">
                <button class="btn primary">Complete your Profile</button>
                <button class="btn secondary">Learn more</button>
            </div>
        </div>

        <!-- RIGHT IMAGE -->
        <div>
            <img src="{{ asset('images/business-homepage-groomer-space-owner-hero-section.png') }}"
                alt="Hero Image">
        </div>

    </section>

    <section class="gf-section">
        <h2 class="main-title">Control & flexibility</h2>

        <div class="cards-container">
            <div class="card card-orange">
                <h3>Set your own schedule & prices.</h3>
                <p>Work on your terms, choose your availability
                    and rates.</p>
                <div class="icon-circle"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                        viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="40" fill="#FFC97A" />
                        <path
                            d="M19.2 39.5082C19.2 32.0824 19.2 28.3686 21.6109 26.0626C24.0219 23.7567 27.8995 23.7548 35.6568 23.7548H43.8853C51.6426 23.7548 55.5223 23.7548 57.9312 26.0626C60.34 28.3705 60.3421 32.0824 60.3421 39.5082V43.4466C60.3421 50.8724 60.3421 54.5863 57.9312 56.8922C55.5202 59.1981 51.6426 59.2001 43.8853 59.2001H35.6568C27.8995 59.2001 24.0198 59.2001 21.6109 56.8922C19.2021 54.5843 19.2 50.8724 19.2 43.4466V39.5082Z"
                            fill="white" stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M20.8484 35.1033C20.7645 36.6159 20.7645 38.4154 20.7645 40.5773V43.9969C20.7645 50.4445 20.7664 53.6674 22.9908 55.6713C25.2153 57.6751 28.798 57.6752 35.9614 57.6752H43.5599C50.7234 57.6752 54.3041 57.6734 56.5305 55.6713C58.7568 53.6691 58.7568 50.4445 58.7568 43.9969V40.5773C58.7568 38.4153 58.7566 36.6158 58.6725 35.1033H20.8484Z"
                            fill="#FFC97A" fill-opacity="0.125" />
                        <path
                            d="M33.727 46.5142L37.81 50.5972L45.6964 42.7108C46.0669 42.3402 46.068 41.7397 45.6988 41.3678C45.3276 40.9941 44.7235 40.993 44.3511 41.3655L37.81 47.9066L35.0675 45.1718C34.6969 44.8022 34.0971 44.8026 33.727 45.1727C33.3565 45.5431 33.3565 46.1437 33.727 46.5142Z"
                            fill="#FFC97A" />
                        <path d="M29.1744 23.7397V20.8M50.3466 23.7397V20.8M19.6469 33.5386H59.8741"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                    </svg></div>
            </div>

            <div class="card card-dark-orange">
                <h3>Choose the services you offer.</h3>
                <p>Offer full grooms, bath & brush, add-ons & more.</p>
                <div class="icon-circle"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                        viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="40" fill="#FFC97A" />
                        <path
                            d="M46.022 21.4602C49.0403 20.4586 52.0175 20.4859 53.8564 22.3166C53.8615 22.3217 53.8673 22.3272 53.8723 22.3324L56.3216 24.9001C56.3443 24.924 56.3648 24.9494 56.3843 24.975C56.4237 25.0028 56.4618 25.0339 56.4971 25.0691C58.3359 26.9 58.3633 29.8642 57.3573 32.8695C56.333 35.9288 54.1609 39.3177 51.1145 42.3503C48.0686 45.3835 44.6651 47.5463 41.5924 48.566C38.7796 49.4995 36.0035 49.5381 34.1501 48.0575C33.9685 48.0252 33.7953 47.936 33.6628 47.7886L31.0998 44.9388C29.2787 43.1065 29.2552 40.1522 30.258 37.1567C31.2823 34.0974 33.4537 30.7084 36.4999 27.6759C39.5458 24.6428 42.9494 22.4799 46.022 21.4602Z"
                            fill="white" />
                        <path
                            d="M33.6568 47.3306C36.7413 50.4151 44.2433 47.9151 50.4124 41.7451C56.5824 35.576 59.0825 28.074 55.9979 24.9894M42.7326 22.8943L44.1288 24.2914M37.8462 27.7817L39.2423 29.1779M33.6558 33.3673L35.0519 34.7634M32.2596 40.3489L33.6558 41.7451M50.4124 20.8L51.8085 22.1962M49.0162 29.1788L51.8085 31.9711M44.1298 34.0663L46.922 36.8586M38.5442 38.2547L41.3365 41.047"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M33.4689 51.5201C34.6257 50.3634 34.6257 48.4879 33.4689 47.3311C32.3121 46.1743 30.4366 46.1743 29.2799 47.3311L23.6945 52.9165C22.5377 54.0733 22.5377 55.9488 23.6945 57.1055C24.8512 58.2623 26.7267 58.2623 27.8835 57.1055L33.4689 51.5201Z"
                            fill="#FFF8EE" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg></div>
            </div>

            <div class="card card-blue">
                <h3>Select size, age & pet preferences.</h3>
                <p>Only accept pets you are comfortable handling.</p>
                <div class="icon-circle"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                        viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="40" fill="#FFC97A" />
                        <path
                            d="M40.0001 36.4591C43.8102 36.4591 46.6508 38.5267 48.4942 41.1749L48.4952 41.1769C50.3289 43.7961 51.2501 47.0561 51.2501 49.673C51.25 52.3721 49.768 54.323 47.7891 55.5929L47.3868 55.838C45.2308 57.0863 42.4884 57.6495 40.0001 57.6495C37.5125 57.6495 34.7692 57.0825 32.6124 55.837L32.21 55.5929C30.2301 54.3262 28.7501 52.3729 28.7501 49.673C28.7501 47.0561 29.6712 43.7961 31.5049 41.1769V41.1759C33.3525 38.5298 36.1938 36.4591 40.0001 36.4591Z"
                            fill="#FFF1DD" stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M21.5999 37.55C23.6891 37.55 25.6497 39.7357 25.6497 42.8C25.6497 45.8644 23.6891 48.05 21.5999 48.05C19.5107 48.0499 17.55 45.8643 17.55 42.8C17.55 39.7358 19.5107 37.5502 21.5999 37.55Z"
                            fill="white" stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M58.3998 37.55C60.4891 37.55 62.4496 39.7357 62.4496 42.8C62.4496 45.8644 60.4891 48.05 58.3998 48.05C56.3107 48.0499 54.35 45.8643 54.35 42.8C54.35 39.7358 56.3107 37.5502 58.3998 37.55Z"
                            fill="white" stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M31.9999 22.35C34.0891 22.35 36.0497 24.5357 36.0497 27.6C36.0497 30.6643 34.0891 32.85 31.9999 32.85C29.9107 32.8498 27.9501 30.6642 27.9501 27.6C27.9501 24.5358 29.9107 22.3501 31.9999 22.35Z"
                            fill="white" stroke="#3B3731" stroke-width="1.5" />
                        <path
                            d="M47.9999 22.35C50.0891 22.35 52.0497 24.5357 52.0497 27.6C52.0497 30.6643 50.0891 32.85 47.9999 32.85C45.9107 32.8498 43.9501 30.6642 43.9501 27.6C43.9501 24.5358 45.9107 22.3501 47.9999 22.35Z"
                            fill="white" stroke="#3B3731" stroke-width="1.5" />
                    </svg></div>
            </div>

            <div class="card card-green">
                <h3>All-in-one platform</h3>
                <p>Everything you need to run your business in one place.</p>
                <div class="icon-circle"><svg xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                        viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="40" fill="#FFC97A" />
                        <path d="M39.56 22L56.12 31.2V49.6L39.56 58.8L23 49.6V31.2L39.56 22Z" fill="#FFF1DD" />
                        <path
                            d="M39.56 40.4L55.2 32.12L39.56 40.4ZM39.56 40.4V57.88V40.4ZM39.56 40.4L23.92 32.12L39.56 40.4ZM39.56 22L56.12 31.2V49.6L39.56 58.8L23 49.6V31.2L39.56 22Z"
                            fill="#FFF1DD" />
                        <path
                            d="M39.56 40.4L55.2 32.12M39.56 40.4V57.88M39.56 40.4L23.92 32.12M39.56 22L56.12 31.2V49.6L39.56 58.8L23 49.6V31.2L39.56 22Z"
                            stroke="black" stroke-width="1.5" />
                    </svg></div>
            </div>
        </div>
    </section>

    <section class="safety-section">
        <div class="safety-container">

            <div class="safety-left">
                <h2 class="safety-title">Guaranteed<br>safety & support</h2>
                <div class="shield-image-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="274" height="268" viewBox="0 0 274 268"
                        fill="none">
                        <g filter="url(#filter0_d_12_365)">
                            <path
                                d="M144.246 35.6022C144.459 35.5276 144.69 35.5273 144.904 35.6011L224.712 63.2571C225.115 63.3966 225.385 63.7759 225.385 64.2019V145.58C225.385 190.511 151.349 227.285 145.381 230.176C145.112 230.306 144.888 230.306 144.619 230.176C138.651 227.285 64.6154 190.511 64.6154 145.58V64.1995C64.6154 63.7746 64.8839 63.3961 65.2849 63.2557L144.246 35.6022Z"
                                fill="#FFF8EE" />
                            <path
                                d="M238 51.0195V148.111C238 161.078 232.722 173.583 224.513 185.18C216.305 196.774 205.251 207.341 193.921 216.385C172.176 233.742 149.656 245.3 145 247.616C140.344 245.3 117.824 233.742 96.0791 216.385C84.7489 207.341 73.6948 196.774 65.4873 185.18C57.2778 173.583 52 161.078 52 148.111V51.0146L144.5 18.1211L238 51.0195Z"
                                stroke="#3B3731" stroke-width="4" />
                            <path
                                d="M112.546 139.157L137.41 164.022L185.437 115.995C187.693 113.739 187.7 110.082 185.451 107.817C183.191 105.541 179.512 105.535 177.244 107.803L137.41 147.636L120.71 130.982C118.453 128.732 114.8 128.734 112.546 130.988C110.29 133.244 110.29 136.901 112.546 139.157Z"
                                fill="#FFC97A" />
                            <rect x="25.0819" y="21.0818" width="45.2474" height="50.2749" rx="3"
                                fill="white" />
                            <path
                                d="M49.8115 6.45562C49.1517 6.15711 48.4447 6 47.7063 6C46.9679 6 46.2609 6.15711 45.601 6.45562L16.0174 19.0086C12.561 20.4698 9.98436 23.879 10.0001 27.9953C10.0786 43.5805 16.4887 72.0958 43.5586 85.0573C46.1823 86.3142 49.2302 86.3142 51.854 85.0573C78.9239 72.0958 85.3339 43.5805 85.4125 27.9953C85.4282 23.879 82.8516 20.4698 79.3952 19.0086L49.8115 6.45562ZM32.7652 50.9646C33.5193 51.1532 34.3206 51.2474 35.1375 51.2474C40.6835 51.2474 45.1925 46.7384 45.1925 41.1925V31.1375H52.1368C54.0378 31.1375 55.7817 32.2058 56.6301 33.9183L57.7613 36.165H67.8163C69.1988 36.165 70.33 37.2962 70.33 38.6787V43.7062C70.33 50.6504 64.7055 56.2749 57.7613 56.2749H50.22V64.2404C50.22 65.3873 49.2931 66.3299 48.1305 66.3299C47.8477 66.3299 47.5649 66.2671 47.3135 66.1571L31.8068 59.5114C30.7699 59.0715 30.11 58.0503 30.11 56.9348C30.11 56.4949 30.2043 56.0707 30.4086 55.6779L32.7652 50.9646ZM32.6238 31.1375H40.165V41.1925C40.165 43.9733 37.9184 46.22 35.1375 46.22C32.3567 46.22 30.11 43.9733 30.11 41.1925V33.6512C30.11 32.2687 31.2412 31.1375 32.6238 31.1375ZM52.7338 38.6787C52.7338 38.012 52.4689 37.3726 51.9975 36.9012C51.5261 36.4298 50.8867 36.165 50.22 36.165C49.5533 36.165 48.914 36.4298 48.4425 36.9012C47.9711 37.3726 47.7063 38.012 47.7063 38.6787C47.7063 39.3454 47.9711 39.9848 48.4425 40.4562C48.914 40.9276 49.5533 41.1925 50.22 41.1925C50.8867 41.1925 51.5261 40.9276 51.9975 40.4562C52.4689 39.9848 52.7338 39.3454 52.7338 38.6787Z"
                                fill="#C9DDA0" />
                            <path
                                d="M227.812 174.456C227.152 174.157 226.445 174 225.706 174C224.968 174 224.261 174.157 223.601 174.456L194.017 187.009C190.561 188.47 187.984 191.879 188 195.995C188.079 211.581 194.489 240.096 221.559 253.057C224.182 254.314 227.23 254.314 229.854 253.057C256.924 240.096 263.334 211.581 263.412 195.995C263.428 191.879 260.852 188.47 257.395 187.009L227.812 174.456Z"
                                fill="#CBDCE8" />
                            <path
                                d="M246.182 195.818L230.02 212.99L246.182 195.818ZM222.84 212.152C217.83 214.075 213.824 213.746 209.818 212.158C210.828 225.174 216.897 230.178 224.988 232.182C224.988 232.182 231.083 227.871 231.962 217.651C232.057 216.543 232.103 215.992 231.875 215.368C231.644 214.743 231.192 214.297 230.289 213.402C228.802 211.931 228.061 211.196 227.178 211.01C226.295 210.828 225.143 211.269 222.84 212.152Z"
                                fill="#CBDCE8" />
                            <path
                                d="M246.182 195.818L230.02 212.99M222.84 212.152C217.83 214.075 213.824 213.746 209.818 212.158C210.828 225.174 216.897 230.178 224.988 232.182C224.988 232.182 231.083 227.871 231.962 217.651C232.057 216.543 232.103 215.992 231.875 215.368C231.644 214.743 231.192 214.297 230.289 213.402C228.802 211.931 228.061 211.196 227.178 211.01C226.295 210.828 225.143 211.269 222.84 212.152Z"
                                stroke="white" stroke-width="4" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M212.848 222.983C212.848 222.983 217.899 223.961 222.949 220.062L212.848 222.983Z"
                                fill="#CBDCE8" />
                            <path d="M212.848 222.983C212.848 222.983 217.899 223.961 222.949 220.062"
                                stroke="white" stroke-width="4" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M220.929 204.405C220.929 205.075 220.663 205.717 220.19 206.191C219.716 206.664 219.074 206.931 218.404 206.931C217.734 206.931 217.092 206.664 216.619 206.191C216.145 205.717 215.879 205.075 215.879 204.405C215.879 203.736 216.145 203.093 216.619 202.62C217.092 202.146 217.734 201.88 218.404 201.88C219.074 201.88 219.716 202.146 220.19 202.62C220.663 203.093 220.929 203.736 220.929 204.405Z"
                                fill="#CBDCE8" stroke="white" stroke-width="4" />
                            <path d="M225.98 197.838V198.04V197.838Z" fill="#CBDCE8" />
                            <path d="M225.98 197.838V198.04" stroke="white" stroke-width="4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <filter id="filter0_d_12_365" x="0" y="0" width="273.413" height="268"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="5" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.28 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix"
                                    result="effect1_dropShadow_12_365" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_12_365"
                                    result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
            </div>

            <div class="safety-right">
                <div class="feature-item" style="border: none;">
                    <h3>Manage bookings, CRM, marketing & payments</h3>
                    <p>Automated scheduling, reminders, and secure payouts.</p>
                </div>

                <div class="feature-item">
                    <h3>Protected by FursGo Insurance</h3>
                    <p>Every booking is protected with industry-leading insurance.</p>
                </div>

                <div class="feature-item">
                    <h3>Secure payments & ID verification</h3>
                    <p>No cash handling. Payments are guaranteed and on time.</p>
                </div>

                <div class="feature-item">
                    <h3>Dedicated support team</h3>
                    <p>Real support from humans — whenever you need it.</p>
                </div>
            </div>

        </div>
    </section>

    <section class="crm-section">
        <h2 class="crm-main-title">Professional tools & CRM</h2>

        <div class="crm-cards-wrapper">
            <div class="crm-card">
                <h3>Professional CRM tools</h3>
                <p>Send follow-ups, manage clients, and recover abandoned bookings.</p>
            </div>

            <div class="crm-card">
                <h3>Real-time analytics dashboard</h3>
                <p>Track earnings, returning customers, and growth instantly.</p>
            </div>
        </div>
    </section>

    <section class="works-section">
        <div class="header-container">
            <h2 class="main-title">How it works</h2>
            <p class="sub-header-text">A simple 3-step process to start earning.</p>
        </div>

        <div class="steps-wrapper">
            <div class="step-card">
                <div class="card-content">
                    <h3><strong>Create your profile</strong> – Build your profile with FursGo’s onboarding flow.
                    </h3>
                    <p>Build a professional profile in minutes. Add photos, services, prices, and availability.</p>
                </div>
                <div class="step-footer">
                    <span>Step</span>
                    <span>01</span>
                </div>
            </div>

            <div class="step-card">
                <div class="card-content">
                    <h3><strong>Accept Bookings</strong> – Set your availability and choose clients.</h3>
                    <p>Fill your calendar with bookings from real customers. Decide when you want to work and who
                        you accept.</p>
                </div>
                <div class="step-footer">
                    <span>Step</span>
                    <span>02</span>
                </div>
            </div>

            <div class="step-card">
                <div class="card-content">
                    <h3><strong>Get paid</strong> – Secure payouts straight to your bank.</h3>
                    <p>Fast, secure payouts directly to your bank. Paid automatically after each completed booking.
                    </p>
                </div>
                <div class="step-footer">
                    <span>Step</span>
                    <span>03</span>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
