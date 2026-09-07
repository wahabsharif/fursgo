@props(['isChatOnline' => true, 'isBusiness' => false])

<div class="row mt-5 help-contact-support">
    <div class="col-lg-12">
        <div class="top-head d-flex flex-column align-items-center justify-content-center mt-5">
            <h1 class="large-font">Contact Support</h1>
            <h3 class="normal-font-weight help-faq-subtitle">Can’t find what you’re looking for? Our support team is
                here to help.</h3>
        </div>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
        <div class="submit-chat-box d-flex align-items-center justify-content-center mt-5 gap-30">
            <div
                class="box-wrapper help-contact-card d-flex flex-column align-items-center justify-content-center gap-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="54" height="40" viewBox="0 0 54 40" fill="none">
                    <path
                        d="M29.1984 33.5714H24.0714H2C1.44772 33.5714 1 33.1237 1 32.5714V2C1 1.44772 1.44772 1 2 1H46.1429C46.6952 1 47.1429 1.44772 47.1429 2V22.7143"
                        stroke="#3B3731" stroke-width="2" stroke-linecap="round" />
                    <path d="M1.47501 1.4751L23.5464 15.0626C23.8683 15.2608 24.2744 15.2605 24.596 15.0619L46.6 1.4751"
                        stroke="#3B3731" stroke-width="2" />
                    <path
                        d="M36.2858 32.5715C35.7335 32.5715 35.2858 33.0192 35.2858 33.5715C35.2858 34.1238 35.7335 34.5715 36.2858 34.5715V33.5715V32.5715ZM36.2858 33.5715V34.5715L52.5715 34.5715V33.5715V32.5715L36.2858 32.5715V33.5715Z"
                        fill="#3B3731" />
                    <path d="M47.1429 28.1428L52.5715 33.5714L47.1429 39" stroke="#3B3731" stroke-width="2"
                        stroke-linecap="round" />
                </svg>
                <p class="normal-font-bold">Submit a Request</p>
                <p class="simple-font text-center">For more detailed questions, submit a request and our support team
                    will follow up by email.</p>
                <button type="button" class="normal-font-bold btn-custom btn-no-bg text-center mt-3"
                    @click.prevent="openTicket()" style="color:#FBAC83;border:1px solid #FBAC83">Submit request</button>
                <p class="simple-font">Responses usually within 24 hours</p>
            </div>

            <div
                class="box-wrapper help-contact-card {{ $isBusiness ? 'help-contact-card--chat' : '' }} d-flex flex-column align-items-center justify-content-center gap-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="55" height="41" viewBox="0 0 55 41" fill="none">
                    <path
                        d="M46.0703 18.7454C51.3119 20.4152 53.6497 22.9059 53.6497 26.825C53.6497 30.3109 50.4696 33.0162 48.5758 34.3199C48.4861 34.3815 48.4127 34.464 48.362 34.5602C48.3113 34.6565 48.2848 34.7637 48.2847 34.8725V38.5514C48.2847 39.2511 47.5838 39.7306 46.9692 39.3961C45.8882 38.808 44.9139 38.0353 44.092 37.1097C44.0148 37.0228 43.9164 36.9574 43.8064 36.92C43.6963 36.8827 43.5784 36.8745 43.4643 36.8964C43.082 36.9702 42.6917 37.0748 42.2961 37.1807C41.612 37.3645 40.9066 37.555 40.2373 37.555C36.793 37.555 34.3814 36.8146 31.9162 35.0026"
                        stroke="#3B3731" stroke-width="2" stroke-linecap="round" />
                    <path
                        d="M21.0264 1C32.3911 1 40.5781 7.9409 40.5781 16.7656C40.5781 21.0626 38.7313 24.6322 35.4844 27.1523C32.2167 29.6885 27.4789 31.1963 21.6973 31.1963C20.0446 31.1963 18.1304 30.8774 16.4082 30.5488L16.3984 30.5469L16.1865 30.5225C16.0448 30.5151 15.9027 30.5264 15.7637 30.5547L15.5576 30.6104C15.2875 30.7022 15.0454 30.8611 14.8535 31.0723H14.8525L14.8447 31.0811C12.8976 33.277 10.6876 34.8904 9.04785 35.9189V29.5068C9.04776 29.3037 9.01025 29.1029 8.93848 28.9141L8.85449 28.7295L8.75 28.5557C8.63484 28.3896 8.48981 28.2449 8.32227 28.1299C4.84325 25.736 1.00017 21.9095 1 16.7686C1 8.11674 9.56493 1.00013 21.0264 1Z"
                        stroke="#3B3731" stroke-width="2" />
                    <circle cx="12.7418" cy="17.5108" r="1.56478" fill="#3B3731" />
                    <circle cx="21.0873" cy="17.5108" r="1.56478" fill="#3B3731" />
                    <circle cx="29.4328" cy="17.5108" r="1.56478" fill="#3B3731" />
                </svg>
                <p class="normal-font-bold">Chat with FursGo</p>
                <p class="simple-font text-center">Chat live with a FursGo team member for help with bookings,
                    payments, or account questions.</p>
                <button type="button" class="normal-font-bold btn-custom btn-groomer-bg text-center mt-3"
                    style="color:#fff" data-open-chat>
                    {{ $isChatOnline ? 'Start chat' : 'Chat offline' }}
                </button>
                <p class="simple-font">
                    {{ $isChatOnline ? 'Monday–Friday · 9am–5pm (UK time)' : 'Currently outside UK support hours. Submit a request and we’ll reply by email.' }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-1"></div>
</div>