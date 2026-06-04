    <style>
        @if (!empty($scoped))
            .dashboard-info-panel--for-groomers,
            .dashboard-info-panel--for-groomers * {
                box-sizing: border-box;
            }

            .dashboard-info-panel--for-groomers .bgs-safety-section,
            .dashboard-info-panel--for-groomers .works-section {
                width: 100vw;
                max-width: 100vw;
                margin-left: calc(50% - 50vw);
                margin-right: calc(50% - 50vw);
            }

        @else
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background: #FFFFFF;
            }
        @endif


        .bg-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bgs-business-groomer {
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
        .bgs-left {
            width: 50%;
            padding: 40px;
        }

        .bgs-subtitle {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-bottom: 2rem
        }

        .bgs-left h1 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: 110%;
            margin-bottom: 12px;
        }

        .bgs-description {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-bottom: 22px;
        }

        /* BUTTONS */
        .bgs-buttons {
            display: flex;
            gap: 12px;
            margin-top: 4rem;
        }

        .bgs-btn {
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


        .bgs-right img {
            width: 505px;
            height: 500px;
            aspect-ratio: 101/100;
            object-fit: cover;
        }

        /* SECTION */
        .bgs-gf-section {
            width: 100%;
            margin: 40px auto;
            display: flex;
            flex-direction: column;
        }

        .bgs-main-title {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin: 2.5rem 0;
        }

        .bgs-cards-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .bgs-card {
            border: 1px solid #e2e8f0;
            background: #fff;
            /* border-radius: 10px; */
            padding: 40px;
            width: 300px;
            height: 349px;
            position: relative;
            /* border: 1px solid transparent; */
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .card-orange {
            border: 1px solid #FCC1A2;
            background: #FDFCF8;
            border-radius: 10px;
        }

        .card-blue {
            border-color: #d1e9ff;
            background: #FDFCF8;
            border-radius: 10px;
        }

        .card-dark-orange {
            border: 1px solid #FFBFB3;
            background: #FDFCF8;
            border-radius: 10px;
        }

        .card-green {
            border: 1px solid #DFEDC5;
            background: #FDFCF8;
            border-radius: 10px;
        }


        .card.active {
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.1);
        }

        .bgs-card h3 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-bottom: 20px;
        }

        .bgs-card p {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .bgs-icon-circle {
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

        .bgs-safety-section {
            background: #F5F9ED;
            padding: 80px 10%;
            display: flex;
            justify-content: center;
            margin-top: 7rem;
        }

        .bgs-safety-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            max-width: 1100px;
            align-items: center;
        }

        /* Left Side Styles */
        .bgs-safety-left {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .bgs-safety-title {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .bgs-shield-img {
            max-width: 250px;
            height: auto;
        }

        /* Right Side Styles */
        .bgs-safety-right {
            display: flex;
            flex-direction: column;
        }

        .bgs-feature-item {
            border-top: 1px solid #3B3731;
            padding: 35px 0;
        }



        .bgs-feature-item h3 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin: 0 0 2px 0;
        }

        .bgs-feature-item p {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .bgs-crm-section {
            padding: 60px 20px;
            text-align: center;
            background-color: #ffffff;
            margin: 4rem 0;
        }

        .bgs-crm-main-title {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 40px;
        }

        .bgs-crm-cards-wrapper {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;

        }

        .bgs-crm-card {
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


        .bgs-crm-card:hover {
            transform: translateY(-5px);
        }

        .bgs-crm-card h3 {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin: 0 0 10px 0;

        }

        .bgs-crm-card p {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .works-section {
            background: #FDFCF8;
            padding: 80px 0;
        }

        /* Header Alignment */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 50px;
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
            gap: 30px;

        }

        /* Card Style */
        .step-card {
            background: white;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 400px;
            border-radius: 10px;
            height: 350px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .card-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px 20px;
            height: 100%;
        }

        .card-content h3 {
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
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
            border-radius: 0 0 10px 10px;
        }

        .bgs-testimonial-section {
            position: relative;
            padding: 80px 0;
            overflow: hidden;
            /* min-height: 100vh; */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .left-img-section {
            position: absolute;
            left: -12px;
            top: -35px;
            width: 50%;
            height: 100%;
        }

        .right-img-section {
            position: absolute;
            right: 1rem;
            top: -6px;
            width: 50%;
            height: 100%;
        }

        .bgs-testimonial-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 10;
            top: 2rem;
        }

        .testimonial-header {
            text-align: center;
            margin-top: 10rem
        }

        /* Typography */
        .bgs-header-title {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin-bottom: 10px;
        }

        .bgs-header-subtitle {
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* Slider Layout */
        .slider-wrapper {
            position: relative;
            margin: 0 auto;
            width: 480px;
            height: 180px;
            overflow: hidden;
        }

        .bgs-t-quote-icon-left,
        .bgs-t-quote-icon-right {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            position: absolute;
            color: #2d2d2d;
            opacity: 0.9;
        }

        .bgs-t-quote-icon-left {
            left: 1rem;
            top: 4rem;
            z-index: 1000;
        }

        .bgs-t-quote-icon-right {
            right: 0;
            bottom: 0;
        }

        .slider-content {
            display: flex;
            justify-content: center;
            gap: 16px;
            transition: transform 0.4s ease;
            padding: 0 60px;
        }

        .card {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-width: 400px;
            background: #FBFBFB;
            border: 1px solid #D4D4D4;
            border-radius: 12px;
            padding: 20px;
            transition: 0.3s;
        }

        .side-card {
            opacity: 0.5;
            transform: scale(1);
        }

        .active-card {
            opacity: 1;
        }

        .date {
            color: rgba(59, 55, 49, 0.80);
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .testimonial-text {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .user-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details h4 {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .user-details p {
            color: rgba(59, 55, 49, 0.80);
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* Floating Images Positioning */
        .float-img {
            position: absolute;
            border-radius: 50%;
            object-fit: cover;
            z-index: 1;
        }

        .img-l-1 {
            width: 152px;
            height: 152px;
            left: 2%;
            top: 45%;
        }

        .img-l-2 {
            width: 85px;
            height: 85px;
            left: 22%;
            top: 17%;
        }

        .img-l-3 {
            width: 85px;
            height: 85px;
            left: 34%;
            top: 44%;
        }

        .img-r-1 {
            width: 150px;
            height: 150px;
            right: 5%;
            top: 2%;
        }

        .img-r-2 {
            width: 85px;
            height: 85px;
            right: 24%;
            top: 50%;
        }

        .img-r-3 {
            width: 85px;
            height: 85px;
            right: -2%;
            top: 50%;
        }

        /* Controls */
        .controls {
            display: flex;
            justify-content: end;
            align-items: center;
            gap: 40px;
            /* margin-top: 20px; */
        }

        .dots {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 6px;
            height: 6px;
            background: #ddd;
            border-radius: 50%;
        }

        .dot.active {
            background: #333;
        }

        .arrows {
            display: flex;
            gap: 15px;
        }

        .arrow-btn {
            width: 40px;
            height: 40px;
            background: transparent;
            border-radius: 50%;
            font-size: 1.5rem;
            cursor: pointer;
            color: #3B3731;
            transition: 0.3s;
            border: none;
        }

        .active-arrow {
            color: #333;
            border-color: #333;
        }

        .active-card {
            transition: opacity 0.3s ease-in-out;
        }

        .testimonial-slide-in {
            animation: testimonialSlideIn 0.45s ease;
        }

        @keyframes testimonialSlideIn {
            0% {
                opacity: 0;
                transform: translateX(24px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .dot {
            cursor: pointer;
        }


        .bgs-services-section {
            padding: 70px 0;
        }

        .bgs-services-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .bgs-services-header h2 {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin: 0;
        }

        .bgs-services-header p {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-top: 5px;
        }

        .bgs-info {
            text-align: right;
            color: #3B3731;
            text-align: right;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }


        /* GRID */
        .bgs-services-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-top: 3rem;
        }

        /* CARD */
        .bgs-services-card {
            background: #FBFBFB;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: 0.3s;
        }

        .bgs-services-card img {
            border-radius: 10px;
            margin-top: 15px;
            width: 255px;
            height: 200px;
            object-fit: cover;
        }

        .bgs-services-card h3 {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            margin: 12px 0 25px
        }

        .bgs-services-card p {
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        /* Hover */
        .bgs-services-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
    </style>
