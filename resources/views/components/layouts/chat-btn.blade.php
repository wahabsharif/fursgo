@push('styles')
    <style>
        /* Fixed button bottom left */
        #chat-btn {
            position: fixed;
            bottom: 44px;
            z-index: 1001;
            cursor: pointer;
            border: none;
            background: #FFC97A;
            padding: 20px;
            border-radius: 100px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Chat panel */
        #chat-panel {
            position: fixed;
            bottom: 130px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        #chat-panel.open {
            display: flex;
        }

        /* Header */
        .chat-header {
            background: #F5A05A;
            color: white;
            padding: 14px 16px;
            font-weight: bold;
            font-size: 15px;
        }

        /* Body - place your divs here */
        .chat-body {
            padding: 16px;
            background: #fafafa;
            min-height: 200px;
        }

        .need-help-chat {
            border-radius: 103px;
            background: #FFC97A;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            width: 295px;
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            position: fixed;
            bottom: 50px;
            z-index: 1001;
            cursor: pointer;
            left: 23%;
        }

        .need-help-chat {
            display: flex;
            /* always in layout */
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        /* Hover effect */
        #chat-btn:hover+.need-help-chat,
        .need-help-chat:hover {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Overlay behind chat panel */
        body.chat-open::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.10);
            z-index: 1;
            backdrop-filter: blur(2px);
        }

        /* Chat panel above overlay */
        #chat-panel {
            position: fixed;
            z-index: 2;
            /* higher than overlay */
        }

        body.chat-open #chat-btn:hover+.need-help-chat,
        body.chat-open .need-help-chat:hover {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
        }

        /* card styles */
        /* Base Card Styling */
        .fs-card {
            width: 400px;
            height: 550px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            border: 1px solid #ffc97a;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        }

        /* Header */
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

        .fs-back-arrow {
            position: relative;
            right: 4.5rem;
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
        }

        /* Body & Backgrounds */
        .fs-card-body {
            margin-top: -3rem;
            padding: 20px;
            /* flex-grow: 1; */
        }

        .fs-2 {
            padding: 12px;
            margin-top: 0;
            padding-left: 1.1rem;
        }

        .fs-gradient-bg {
            background: linear-gradient(180deg, #ffc97a 0%, #fff 75%);
        }

        /* Card 1 Specifics */

        .fs-welcome-text {
            margin-top: 5rem;
        }

        .fs-welcome-text h2 {
            color: #fff;
            font-family: Lato;
            font-size: 30px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            margin: 0;
        }

        .fs-welcome-text p {
            color: #fff;
            font-family: Lato;
            font-size: 22px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            margin: 0;
        }

        .fs-content {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .fs-menu-list {
            border-radius: 10px;
            border: 1px solid #e2e2e2;
            background: #fff;
            box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.03);
            margin-top: 1.5rem;
        }

        .fs-menu-item {
            display: flex;
            justify-content: space-between;
            padding: 18px 15px;
            /* border-radius: 10px; */
            border: 1px solid #f0f0f0;
            color: #3b3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            transition: 0.2s;
        }

        .fs-menu-item:hover {
            background-color: #fff8e1;
        }

        .fs-status-msg {
            text-align: center;
            color: #9d9b98;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-bottom: 10px;
        }

        .fs-chat-bubble {
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 10px;
            max-width: 80%;
            color: #3b3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-user-msg {
            width: 241px;
            background: #fdfcf8;
            margin-left: auto;
            color: #3b3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-bot-msg {
            width: 275px;
            height: 67px;
            border-radius: 10px 10px 10px 0px;
            background: rgba(255, 201, 122, 0.13);
            padding-left: 1rem;
        }

        .fs-option-buttons {
            margin-top: 15px;
            margin-left: 7rem;
        }

        .fs-opt-btn {
            width: 251px;
            height: 48px;
            padding: 18px;
            margin-bottom: 8px;
            border: 1px solid #eee;
            background: #fff;
            text-align: left;
            display: flex;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px 10px 0px 10px;
        }

        .fs-opt-btn p {
            color: #3b3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .fs-chat-input {
            width: 360px;
            height: 72px;
            padding: 10px;
            border: 1px solid #e2e2e2;
            border-radius: 6px;
            color: #9d9b98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            border-radius: 10px 10px 0px 0px;
        }

        .fs-input-footer {
            display: flex;
            justify-content: space-around;
            border: 1px solid #e2e2e2;
            background: #f8f8f8;
            width: 360px;
            height: 48px;
            border-radius: 0px 0 10px 10px;
        }

        .fs-input-footer span {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .fs-input-footer div:nth-of-type(1) {
            color: #3b3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 600;
            margin-top: 14px;
            gap: 1rem;
            display: flex;
            align-items: self-start;
            margin-right: 4rem;
        }

        .fs-input-footer div:nth-of-type(2) {
            color: #d4d4d4;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-top: 14px;
        }

        .fs-msg-section {
            width: 370px;
        }

        .fs-chat-input-wrapper {
            position: relative;
        }

        .fs-chat-input-wrapper input {
            color: #3b3731;
        }

        .fs-chat-input-wrapper input .placeholder {
            color: #9d9b98;
            /* border: 1px solid #ddd; */
        }

        .fs-send-icon {
            position: absolute;
            right: 28px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .fs-rating-overlay {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush

<div class="container">
    <!-- Chat Toggle Button -->
    <button id="chat-btn" onclick="toggleChat()">
        <span id="chat-open-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="25" viewBox="0 0 34 25" fill="none">
                <path
                    d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                    stroke="white" stroke-width="2" stroke-linecap="round" />
                <path
                    d="M13.0184 19.318C11.9642 19.318 10.7659 19.116 9.7326 18.9188C9.66412 18.9065 9.59363 18.9118 9.52775 18.9341C9.46187 18.9565 9.40276 18.9953 9.35598 19.0468C8.33359 20.1998 7.20024 21.0924 6.26259 21.7256C5.63442 22.1499 4.82848 21.6769 4.82848 20.9189V17.7045C4.82845 17.6392 4.81254 17.5749 4.78211 17.5171C4.75169 17.4594 4.70767 17.4099 4.65385 17.3729C2.53093 15.9123 0 13.4594 0 10.061C0 4.41892 5.54148 6.10352e-05 12.616 6.10352e-05C19.6463 6.10352e-05 24.9471 4.32477 24.9471 10.0594C24.9471 15.5976 20.1533 19.318 13.0184 19.318Z"
                    fill="white" />
            </svg>
        </span>

        <span id="chat-close-icon" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M1.25 16.25L16.25 1.25M1.25 1.25L16.25 16.25" stroke="white" stroke-width="2.5"
                    stroke-linecap="round" />
            </svg>
        </span>
    </button>

    <div class="need-help-chat">
        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="25" viewBox="0 0 34 25" fill="none">
            <path
                d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                stroke="white" stroke-width="2" stroke-linecap="round" />
            <path
                d="M13.0184 19.318C11.9642 19.318 10.7659 19.116 9.7326 18.9188C9.66412 18.9065 9.59363 18.9118 9.52775 18.9341C9.46187 18.9565 9.40276 18.9953 9.35598 19.0468C8.33359 20.1998 7.20024 21.0924 6.26259 21.7256C5.63442 22.1499 4.82848 21.6769 4.82848 20.9189V17.7045C4.82845 17.6392 4.81254 17.5749 4.78211 17.5171C4.75169 17.4594 4.70767 17.4099 4.65385 17.3729C2.53093 15.9123 0 13.4594 0 10.061C0 4.41892 5.54148 6.10352e-05 12.616 6.10352e-05C19.6463 6.10352e-05 24.9471 4.32477 24.9471 10.0594C24.9471 15.5976 20.1533 19.318 13.0184 19.318Z"
                fill="white" />
        </svg>
        <p>Need Help? Chat with FursGo</p>
    </div>

    <!-- Chat Panel -->
    <div id="chat-panel">
        <div class="fs-container d-flex align-items-center gap-25">

            <div class="fs-card fs-card-1">
                <div class="fs-card-header">
                    <span class="fs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="25"
                            viewBox="0 0 34 25" fill="none">
                            <path
                                d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                                stroke="#FFF8EE" stroke-width="2" stroke-linecap="round" />
                            <path
                                d="M13.0184 19.3179C11.9642 19.3179 10.7659 19.1159 9.7326 18.9188C9.66412 18.9064 9.59363 18.9117 9.52775 18.9341C9.46187 18.9565 9.40276 18.9952 9.35598 19.0467C8.33359 20.1998 7.20024 21.0923 6.26259 21.7256C5.63442 22.1499 4.82848 21.6768 4.82848 20.9188V17.7044C4.82845 17.6391 4.81254 17.5748 4.78211 17.5171C4.75169 17.4593 4.70767 17.4098 4.65385 17.3729C2.53093 15.9122 0 13.4594 0 10.0609C0 4.41886 5.54148 0 12.616 0C19.6463 0 24.9471 4.3247 24.9471 10.0593C24.9471 15.5976 20.1533 19.3179 13.0184 19.3179Z"
                                fill="#FFF8EE" />
                        </svg></span> FursGo Support
                </div>

                <div class="fs-card-body fs-gradient-bg">
                    <div class="fs-welcome-text">
                        <h2>Hi Bella 👋
                        </h2>
                        <p>What can we help you with today?</p>
                    </div>

                    <div class="fs-menu-list">
                        <div class="fs-menu-item">
                            <div class="fs-content">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="21" height="20"
                                            viewBox="0 0 21 20" fill="none">
                                            <path
                                                d="M1.30105 7.45312C1.26025 8.18844 1.26025 9.06324 1.26025 10.1142V11.7765C1.26025 14.9108 1.26118 16.4775 2.34253 17.4517C3.42388 18.4258 5.1655 18.4258 8.6478 18.4258H12.3416C15.8239 18.4258 17.5646 18.4249 18.6468 17.4517C19.7291 16.4784 19.7291 14.9108 19.7291 11.7765V10.1142C19.7291 9.06315 19.729 8.18838 19.6881 7.45312H1.30105Z"
                                                fill="#EFEFEF" fill-opacity="0.933333" />
                                            <path
                                                d="M7.56141 13.0002L9.54623 14.985L13.38 11.1513C13.5601 10.9712 13.5606 10.6793 13.3811 10.4985C13.2007 10.3168 12.907 10.3163 12.726 10.4973L9.54623 13.6771L8.21308 12.3477C8.03293 12.168 7.74131 12.1682 7.56141 12.3481C7.38133 12.5282 7.38133 12.8202 7.56141 13.0002Z"
                                                fill="#E2E2E2" />
                                            <path
                                                d="M0.5 9.59437C0.5 5.98454 0.5 4.17914 1.672 3.05819C2.844 1.93724 4.729 1.93628 8.5 1.93628H12.5C16.271 1.93628 18.157 1.93628 19.328 3.05819C20.499 4.1801 20.5 5.98454 20.5 9.59437V11.5089C20.5 15.1187 20.5 16.9241 19.328 18.0451C18.156 19.166 16.271 19.167 12.5 19.167H8.5C4.729 19.167 2.843 19.167 1.672 18.0451C0.501 16.9232 0.5 15.1187 0.5 11.5089V9.59437Z"
                                                stroke="#9D9B98" />
                                            <path d="M5.3488 1.92903V0.5M15.641 1.92903V0.5M0.717285 6.69248H20.2726"
                                                stroke="#9D9B98" stroke-linecap="round" />
                                        </svg></span></div>
                                <span>Bookings</span>
                            </div>

                            <span class="fs-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15"
                                    viewBox="0 0 8 15" fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span>
                        </div>

                        <div class="fs-menu-item">
                            <div class="fs-content">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="21" height="17"
                                            viewBox="0 0 21 17" fill="none">
                                            <path
                                                d="M17.8077 0.5H3.19231C1.70539 0.5 0.5 1.70539 0.5 3.19231V13.1923C0.5 14.6792 1.70539 15.8846 3.19231 15.8846H17.8077C19.2946 15.8846 20.5 14.6792 20.5 13.1923V3.19231C20.5 1.70539 19.2946 0.5 17.8077 0.5Z"
                                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M0.5 5.11548H20.5H0.5Z" fill="#E2E2E2" />
                                            <path d="M0.5 5.11548H20.5" stroke="#9D9B98" stroke-linejoin="round" />
                                            <rect x="0.916504" y="5.5" width="19.1667" height="1.66667"
                                                fill="#EFEFEF" />
                                            <rect x="3.70508" y="11.3975" width="4.80769" height="1.28205" rx="0.641026"
                                                fill="#E2E2E2" />
                                        </svg></span></div>
                                <span>Payments</span>
                            </div>

                            <span class="fs-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15"
                                    viewBox="0 0 8 15" fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span>
                        </div>

                        <div class="fs-menu-item">
                            <div class="fs-content">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="21" height="24"
                                            viewBox="0 0 21 24" fill="none">
                                            <path
                                                d="M0.5 22.9998V21.7498C0.5 17.6123 3.8625 14.2498 8 14.2498H13C17.1375 14.2498 20.5 17.6123 20.5 21.7498V22.9998"
                                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M1.4375 23.146C1.4375 18.9347 4.51101 15.1774 8.2929 15.1774H12.8632C16.6451 15.1774 19.7185 18.9347 19.7185 23.146"
                                                fill="#EFEFEF" />
                                            <path
                                                d="M10.4995 10.5C7.73701 10.5 5.49951 8.2625 5.49951 5.5C5.49951 2.7375 7.73701 0.5 10.4995 0.5C13.262 0.5 15.4995 2.7375 15.4995 5.5C15.4995 8.2625 13.262 10.5 10.4995 10.5Z"
                                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg></span></div>
                                <span>Account</span>
                            </div>

                            <span class="fs-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15"
                                    viewBox="0 0 8 15" fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span>
                        </div>

                        <div class="fs-menu-item">
                            <div class="fs-content">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="16"
                                            viewBox="0 0 20 16" fill="none">
                                            <path
                                                d="M10 7.0459C11.5107 7.0459 12.6428 7.86238 13.3857 8.92969V8.93066C14.1273 9.98987 14.4999 11.3108 14.5 12.3633C14.5 13.4761 13.8525 14.2676 12.9844 14.7695L12.9834 14.7705C12.1204 15.27 11.0124 15.5 10 15.5C8.98789 15.5 7.87921 15.2682 7.01562 14.7695L6.85547 14.6729C6.06858 14.1698 5.5 13.4075 5.5 12.3633C5.50008 11.3766 5.82756 10.1545 6.47949 9.13281L6.61426 8.93066C7.35909 7.86387 8.49106 7.0459 10 7.0459ZM2.14258 7.0459C2.62465 7.0459 3.04274 7.34487 3.3291 7.78223C3.61535 8.21881 3.78605 8.8114 3.78613 9.4541C3.78613 10.097 3.61542 10.6903 3.3291 11.127H3.32812C3.04229 11.5647 2.62511 11.8633 2.14258 11.8633C1.66068 11.8632 1.24334 11.5641 0.957031 11.127C0.670713 10.6903 0.5 10.097 0.5 9.4541C0.500079 8.8114 0.670779 8.21881 0.957031 7.78223V7.78125C1.24281 7.34371 1.66025 7.04601 2.14258 7.0459ZM17.8574 7.0459C18.3393 7.04601 18.7567 7.345 19.043 7.78223C19.3292 8.21881 19.4999 8.8114 19.5 9.4541C19.5 10.0167 19.37 10.5413 19.1455 10.9561L19.043 11.127C18.7572 11.5646 18.3398 11.8632 17.8574 11.8633C17.3754 11.8633 16.9573 11.5643 16.6709 11.127C16.3846 10.6903 16.2139 10.097 16.2139 9.4541C16.2139 8.8114 16.3846 8.21881 16.6709 7.78223L16.6719 7.78125C16.9577 7.34376 17.3751 7.0459 17.8574 7.0459ZM6.42871 0.5C6.91055 0.500058 7.32793 0.799265 7.61426 1.23633V1.2373C7.90058 1.67399 8.07129 2.2663 8.07129 2.90918C8.07127 3.55202 7.90056 4.14439 7.61426 4.58105V4.58203C7.32845 5.01972 6.91116 5.3183 6.42871 5.31836C5.94664 5.31836 5.52855 5.01841 5.24219 4.58105C4.95594 4.1444 4.78615 3.55195 4.78613 2.90918C4.78613 2.26644 4.95598 1.67396 5.24219 1.2373L5.24316 1.23633C5.529 0.798602 5.9462 0.5 6.42871 0.5ZM13.5713 0.5C14.0533 0.5 14.4715 0.799049 14.7578 1.23633V1.2373C15.044 1.67396 15.2139 2.26644 15.2139 2.90918C15.2139 3.55195 15.0441 4.1444 14.7578 4.58105L14.7568 4.58203C14.471 5.01969 14.0538 5.31836 13.5713 5.31836C13.1495 5.31831 12.7774 5.08862 12.499 4.73828L12.3857 4.58105L12.2832 4.41016C12.0589 3.99547 11.9287 3.47158 11.9287 2.90918C11.9287 2.2663 12.0994 1.67399 12.3857 1.2373V1.23633C12.6715 0.798611 13.0888 0.500058 13.5713 0.5Z"
                                                fill="#E5E5E5" stroke="#9D9B98" />
                                        </svg></span></div>
                                <span>Pets</span>
                            </div>

                            <span class="fs-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15"
                                    viewBox="0 0 8 15" fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span>
                        </div>
                        <div class="fs-menu-item">
                            <div class="fs-content">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="25"
                                            viewBox="0 0 20 25" fill="none">
                                            <path
                                                d="M9.62652 2.16654C9.83944 2.09197 10.0713 2.09159 10.2845 2.16546L17.7891 4.76602C18.1917 4.90553 18.4617 5.28481 18.4617 5.71089V13.6401C18.4617 17.9053 12.1238 21.4721 10.4184 22.3547C10.1529 22.4921 9.84737 22.4921 9.58183 22.3547C7.87642 21.4721 1.53857 17.9053 1.53857 13.6401V5.70842C1.53857 5.28355 1.80705 4.90506 2.20804 4.76463L9.62652 2.16654Z"
                                                fill="#EFEFEF" />
                                            <path
                                                d="M9.78125 0.589844C9.88907 0.551502 10.0073 0.550884 10.1152 0.588867L19.166 3.77344C19.366 3.84382 19.4999 4.03309 19.5 4.24512V13.9062C19.5 16.2585 17.7427 18.5031 15.6045 20.3408C13.4925 22.1559 11.1379 23.4649 10.1963 23.9561C10.0725 24.0205 9.92748 24.0205 9.80371 23.9561C8.86207 23.4649 6.50748 22.1559 4.39551 20.3408C2.25728 18.5031 0.5 16.2585 0.5 13.9062V4.24316C0.5 4.0317 0.632852 3.84244 0.832031 3.77148L9.78125 0.589844Z"
                                                stroke="#9D9B98" />
                                            <path
                                                d="M6.58377 12.964L9.20107 15.5813L14.2565 10.5259C14.494 10.2884 14.4947 9.90345 14.258 9.66507C14.0201 9.4255 13.6328 9.42481 13.3941 9.66355L9.20107 13.8566L7.4431 12.1035C7.20554 11.8666 6.821 11.8669 6.58377 12.1041C6.34631 12.3416 6.34631 12.7266 6.58377 12.964Z"
                                                fill="#E2E2E2" />
                                        </svg></span></div>
                                <span>Safety</span>
                            </div>

                            <span class="fs-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15"
                                    viewBox="0 0 8 15" fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fs-card fs-card-2">

                <div class="fs-card-header">
                    <span class="fs-back-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="16"
                            viewBox="0 0 9 16" fill="none">
                            <path d="M7.74365 14.3737L1.00021 7.63022L7.63045 0.999976" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="fs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="25"
                            viewBox="0 0 34 25" fill="none">
                            <path
                                d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                                stroke="#FFF8EE" stroke-width="2" stroke-linecap="round" />
                            <path
                                d="M13.0184 19.3179C11.9642 19.3179 10.7659 19.1159 9.7326 18.9188C9.66412 18.9064 9.59363 18.9117 9.52775 18.9341C9.46187 18.9565 9.40276 18.9952 9.35598 19.0467C8.33359 20.1998 7.20024 21.0923 6.26259 21.7256C5.63442 22.1499 4.82848 21.6768 4.82848 20.9188V17.7044C4.82845 17.6391 4.81254 17.5748 4.78211 17.5171C4.75169 17.4593 4.70767 17.4098 4.65385 17.3729C2.53093 15.9122 0 13.4594 0 10.0609C0 4.41886 5.54148 0 12.616 0C19.6463 0 24.9471 4.3247 24.9471 10.0593C24.9471 15.5976 20.1533 19.3179 13.0184 19.3179Z"
                                fill="#FFF8EE" />
                        </svg></span> FursGo Support
                </div>

                <div class="fs-card-body fs-2">
                    <p class="fs-status-msg">You are speaking with FursGo.</p>

                    <div class="fs-chat-bubble fs-user-msg">
                        I need support with bookings.
                    </div>

                    <div class="fs-chat-bubble fs-bot-msg">
                        Got it 👍 What's the issue with your booking?
                    </div>

                    <div class="fs-option-buttons">
                        <button class="fs-opt-btn">
                            <div>
                                <p> Change or cancel a booking</p>
                            </div>

                            <div> <span><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" viewBox="0 0 8 15"
                                        fill="none">
                                        <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></div>
                            </span>
                        </button>

                        <button class="fs-opt-btn">
                            <div>
                                <p>Booking is pending</p>
                            </div>

                            <div> <span><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" viewBox="0 0 8 15"
                                        fill="none">
                                        <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></div>
                        </button>

                        <button class="fs-opt-btn">
                            <div>
                                <p>Something else</p>
                            </div>

                            <div> <span><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" viewBox="0 0 8 15"
                                        fill="none">
                                        <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="#9D9B98"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></div>
                        </button>
                    </div>

                    <div class="fs-msg-section">
                        <div class="fs-input-area">
                            <div class="fs-chat-input-wrapper">
                                <input type="text" class="fs-chat-input" placeholder="Write a message...">

                                <span class="fs-send-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M18.2448 0.0763204C19.2868 -0.287756 20.2878 0.713198 19.9237 1.75518L13.8471 19.118C13.4522 20.2441 11.8831 20.3077 11.399 19.2175L8.46685 12.6211L12.5938 8.49316C12.7297 8.34735 12.8036 8.15449 12.8001 7.95522C12.7966 7.75595 12.7159 7.56583 12.575 7.4249C12.434 7.28398 12.2439 7.20325 12.0446 7.19974C11.8454 7.19622 11.6525 7.27019 11.5067 7.40605L7.3787 11.5329L0.782123 8.60084C-0.308075 8.11575 -0.243464 6.54765 0.881605 6.15281L18.2448 0.0763204Z"
                                            fill="#FFC97A" />
                                    </svg>
                                </span>
                            </div>


                            <div class="fs-input-footer">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="11" height="12"
                                            viewBox="0 0 11 12" fill="none">
                                            <path
                                                d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                stroke="#3B3731" stroke-linecap="round" />
                                        </svg> Attach &nbsp;</span>
                                    <hr class="vertical-line-1">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.16656 5.3889C4.84157 5.3889 5.38878 4.84169 5.38878 4.16668C5.38878 3.49167 4.84157 2.94446 4.16656 2.94446C3.49154 2.94446 2.94434 3.49167 2.94434 4.16668C2.94434 4.84169 3.49154 5.3889 4.16656 5.3889Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M11.5002 7.83346L9.61427 5.94757C9.38507 5.71844 9.07425 5.58972 8.75016 5.58972C8.42607 5.58972 8.11525 5.71844 7.88605 5.94757L2.3335 11.5001"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg> Upload
                                    </span>

                                </div>
                                <div> <span>0/3,000</span></div>


                            </div>
                        </div>
                    </div>


                </div>

            </div>

            <div class="fs-card fs-card-3">

                <div class="fs-card-header">
                    <span class="fs-back-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="9" height="16"
                            viewBox="0 0 9 16" fill="none">
                            <path d="M7.74365 14.3737L1.00021 7.63022L7.63045 0.999976" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span>
                    <span class="fs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="34" height="25"
                            viewBox="0 0 34 25" fill="none">
                            <path
                                d="M27.6424 11.2471C30.7873 12.249 32.19 13.7434 32.19 16.0949C32.19 18.1864 30.282 19.8096 29.1457 20.5918C29.0918 20.6287 29.0478 20.6782 29.0174 20.736C28.987 20.7938 28.971 20.8581 28.971 20.9233V22.5512C28.971 23.251 28.2678 23.7266 27.6859 23.3379C27.2333 23.0356 26.8194 22.6756 26.4554 22.2657C26.4091 22.2135 26.35 22.1743 26.284 22.1519C26.218 22.1295 26.1472 22.1246 26.0788 22.1377C25.8494 22.182 25.6152 22.2447 25.3778 22.3083C24.9674 22.4186 24.5441 22.5328 24.1425 22.5328C22.076 22.5328 20.629 22.0886 19.1499 21.0014"
                                stroke="#FFF8EE" stroke-width="2" stroke-linecap="round" />
                            <path
                                d="M13.0184 19.3179C11.9642 19.3179 10.7659 19.1159 9.7326 18.9188C9.66412 18.9064 9.59363 18.9117 9.52775 18.9341C9.46187 18.9565 9.40276 18.9952 9.35598 19.0467C8.33359 20.1998 7.20024 21.0923 6.26259 21.7256C5.63442 22.1499 4.82848 21.6768 4.82848 20.9188V17.7044C4.82845 17.6391 4.81254 17.5748 4.78211 17.5171C4.75169 17.4593 4.70767 17.4098 4.65385 17.3729C2.53093 15.9122 0 13.4594 0 10.0609C0 4.41886 5.54148 0 12.616 0C19.6463 0 24.9471 4.3247 24.9471 10.0593C24.9471 15.5976 20.1533 19.3179 13.0184 19.3179Z"
                                fill="#FFF8EE" />
                        </svg></span> FursGo Support
                </div>

                <div class="fs-card-body fs-2">

                    <div class="fs-chat-bubble fs-user-msg" style="height: 56px;">
                        lorem ipsum dolor sit amet.
                    </div>

                    <div class="fs-chat-bubble fs-bot-msg">
                        Thanks! Most future bookings can be changed, depending on the provider’s policy.
                    </div>

                    <div class="fs-chat-bubble fs-user-msg">
                        That’s not what i’m asking.
                    </div>

                    <div class="fs-chat-bubble fs-bot-msg">
                        I couldn’t find an exact answer for that. Would you like to:
                    </div>

                    <div class="fs-chat-bubble fs-bot-msg"
                        style="background-color:#FBAC83; color:#fff; padding:7px; padding-left:12px; width:251; height: 48px; display: flex;  gap: 3rem;  align-items: center;">
                        <div>
                            <p> Submit a support request</p>

                        </div>
                        <div><span><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" viewBox="0 0 8 15"
                                    fill="none">
                                    <path d="M0.5 0.5L7.06033 7.06033L0.610127 13.5105" stroke="white"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg></span></div>

                    </div>

                    <div class="fs-msg-section">
                        <div class="fs-input-area">
                            <div class="fs-chat-input-wrapper">
                                <input type="text" class="fs-chat-input" placeholder="Write a message...">

                                <span class="fs-send-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                        fill="none">
                                        <path
                                            d="M18.2448 0.0763204C19.2868 -0.287756 20.2878 0.713198 19.9237 1.75518L13.8471 19.118C13.4522 20.2441 11.8831 20.3077 11.399 19.2175L8.46685 12.6211L12.5938 8.49316C12.7297 8.34735 12.8036 8.15449 12.8001 7.95522C12.7966 7.75595 12.7159 7.56583 12.575 7.4249C12.434 7.28398 12.2439 7.20325 12.0446 7.19974C11.8454 7.19622 11.6525 7.27019 11.5067 7.40605L7.3787 11.5329L0.782123 8.60084C-0.308075 8.11575 -0.243464 6.54765 0.881605 6.15281L18.2448 0.0763204Z"
                                            fill="#FFC97A" />
                                    </svg>
                                </span>
                            </div>


                            <div class="fs-input-footer">
                                <div><span><svg xmlns="http://www.w3.org/2000/svg" width="11" height="12"
                                            viewBox="0 0 11 12" fill="none">
                                            <path
                                                d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                stroke="#3B3731" stroke-linecap="round" />
                                        </svg> Attach</span>
                                    <hr class="vertical-line-1">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.16656 5.3889C4.84157 5.3889 5.38878 4.84169 5.38878 4.16668C5.38878 3.49167 4.84157 2.94446 4.16656 2.94446C3.49154 2.94446 2.94434 3.49167 2.94434 4.16668C2.94434 4.84169 3.49154 5.3889 4.16656 5.3889Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M11.5002 7.83346L9.61427 5.94757C9.38507 5.71844 9.07425 5.58972 8.75016 5.58972C8.42607 5.58972 8.11525 5.71844 7.88605 5.94757L2.3335 11.5001"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg> Upload
                                    </span>

                                </div>
                                <div> <span>0/3,000</span></div>


                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function toggleChat(e) {
        e.stopPropagation(); // prevent outside click from firing

        const panel = document.getElementById('chat-panel');
        const openIcon = document.getElementById('chat-open-icon');
        const closeIcon = document.getElementById('chat-close-icon');
        const body = document.body;

        panel.classList.toggle('open');
        body.classList.toggle('chat-open');

        const isOpen = panel.classList.contains('open');

        // Toggle icons
        openIcon.style.display = isOpen ? 'none' : 'block';
        closeIcon.style.display = isOpen ? 'block' : 'none';
    }

    // Attach click properly (remove inline onclick from HTML)
    document.getElementById('chat-btn').addEventListener('click', toggleChat);


    // Close on outside click
    document.addEventListener('click', function (e) {
        const panel = document.getElementById('chat-panel');
        const btn = document.getElementById('chat-btn');
        const openIcon = document.getElementById('chat-open-icon');
        const closeIcon = document.getElementById('chat-close-icon');
        const body = document.body;

        if (!panel.contains(e.target) && !btn.contains(e.target)) {
            panel.classList.remove('open');
            body.classList.remove('chat-open');

            // Reset icons
            openIcon.style.display = 'block';
            closeIcon.style.display = 'none';
        }
    });
</script>
