<div class="row mt-5">
    <div class="col-lg-12">
        <div class="top-head d-flex flex-column align-items-center justify-content-center mt-5">
            <h1 class="large-font">Contact Support</h1>
            <h3 class="normal-font-weight" style="color:#9D9B98">Can’t find what you’re looking for? Our support team is here to help.</h3>
        </div>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
        <div class="submit-chat-box d-flex align-items-center justify-content-center mt-5 gap-30">
            <div class="box-wrapper d-flex flex-column align-items-center justify-content-center gap-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="54" height="40" viewBox="0 0 54 40" fill="none">
                    <path d="M29.1984 33.5714H24.0714H2C1.44772 33.5714 1 33.1237 1 32.5714V2C1 1.44772 1.44772 1 2 1H46.1429C46.6952 1 47.1429 1.44772 47.1429 2V22.7143" stroke="#3B3731" stroke-width="2" stroke-linecap="round" />
                    <path d="M1.47501 1.4751L23.5464 15.0626C23.8683 15.2608 24.2744 15.2605 24.596 15.0619L46.6 1.4751" stroke="#3B3731" stroke-width="2" />
                    <path d="M36.2858 32.5715C35.7335 32.5715 35.2858 33.0192 35.2858 33.5715C35.2858 34.1238 35.7335 34.5715 36.2858 34.5715V33.5715V32.5715ZM36.2858 33.5715V34.5715L52.5715 34.5715V33.5715V32.5715L36.2858 32.5715V33.5715Z" fill="#3B3731" />
                    <path d="M47.1429 28.1428L52.5715 33.5714L47.1429 39" stroke="#3B3731" stroke-width="2" stroke-linecap="round" />
                </svg>
                <p class="normal-font-bold">Submit a Request</p>
                <p class="simple-font text-center">For more detailed questions, submit a request and our support team will follow up by email.</p>
                <button class="normal-font-bold btn-custom btn-no-bg text-center mt-3" data-modal-open="request_modal" style="color:#FBAC83;border:1px solid #FBAC83">Submit request</button>
                <p class="simple-font">Responses usually within 24 hours</p>
            </div>

            <!-- Modal  -->

            <div class="modal" id="request_modal">
                <div class="modal-content size">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                                <div class="modal-head d-flex flex-column align-items-center justify-content-center">
                                    <h1 class="large-font">Submit a Request</h1>
                                    <h3 class="normal-font-weight mt-3 text-center">Please provide as much detail as possible so our support team can help quickly.</h3>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex align-items-center justify-content-end cursor modal-cross mt-3" data-modal-close>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                        <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                                        <path d="M12.8 24.0008L24 12.8008M12.8 12.8008L24 24.0008" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>
                            <div class="col-lg-8">
                                <div class="form-field mt-5">
                                    <label>Subject</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="owner_name" placeholder="A short summary of your issue.">
                                    </div>
                                </div>
                                <div class="form-field mt-4">
                                    <label>Category</label>
                                    <div class="custom-select fs-16-400">
                                        <div class="select-trigger full-width">
                                            <p class="selected-text fs-16-400-muted">Select Category</p>
                                            <svg width="16" height="16" viewBox="0 0 24 24">
                                                <path d="M6 9l6 6 6-6" fill="none" stroke="#666"
                                                    stroke-width="2" />
                                            </svg>
                                        </div>

                                        <ul class="select-options">
                                            <li data-value="bookings">Bookings</li>
                                            <li data-value="payments">Payments</li>
                                            <li data-value="account">Account</li>
                                            <li data-value="app/technical">App/Technical</li>
                                            <li data-value="other">Other</li>
                                        </ul>

                                        <input type="hidden" name="category">
                                    </div>
                                </div>
                                <div class="form-field mt-4">
                                    <label>Booking Reference <span class="light-color-font">(Optional)</span></label>
                                    <div class="input-wrapper">
                                        <input type="text" id="owner_name" placeholder="Enter your booking ID">
                                    </div>
                                </div>
                                <div class="form-field mt-4">
                                    <label>Description</label>
                                    <div class="input-wrapper">
                                        <textarea
                                            id="bio"
                                            name="bio"
                                            rows="3"
                                            placeholder="Tell us what happened…"></textarea>
                                    </div>
                                </div>
                                <div class="form-field mt-4">
                                    <label>Attachments</label>
                                    <p class="normal-font-weight" style="color:#9D9B98">Upload screenshots or documents</p>
                                    <div class="upload-box mt-3">
                                        <div class="upload-header">
                                            <button id="attachBtn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                    <path d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304" stroke="#3B3731" stroke-linecap="round" />
                                                </svg>
                                                Attach
                                            </button>
                                            <button id="uploadBtn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                                    <path d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M4.16662 5.38976C4.84163 5.38976 5.38884 4.84255 5.38884 4.16753C5.38884 3.49252 4.84163 2.94531 4.16662 2.94531C3.4916 2.94531 2.9444 3.49252 2.9444 4.16753C2.9444 4.84255 3.4916 5.38976 4.16662 5.38976Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Upload
                                            </button>
                                        </div>

                                        <input type="file" id="fileInput" hidden>

                                        <div class="file-item" id="fileItem">
                                            <div class="file-left">
                                                <div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="25" viewBox="0 0 21 25" fill="none">
                                                        <path d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M4.33759 18.3393V17.042M4.33759 17.042V14.4473H5.63494C5.97902 14.4473 6.30901 14.584 6.55231 14.8273C6.79561 15.0706 6.93229 15.4005 6.93229 15.7446C6.93229 16.0887 6.79561 16.4187 6.55231 16.662C6.30901 16.9053 5.97902 17.042 5.63494 17.042H4.33759ZM14.7164 18.3393V16.7176M14.7164 16.7176V14.4473H16.6624M14.7164 16.7176H16.6624M9.527 18.3393V14.4473H10.1757C10.6918 14.4473 11.1868 14.6523 11.5517 15.0172C11.9167 15.3822 12.1217 15.8772 12.1217 16.3933C12.1217 16.9094 11.9167 17.4044 11.5517 17.7693C11.1868 18.1343 10.6918 18.3393 10.1757 18.3393H9.527Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="file-info">
                                                    <div class="simple-font" id="fileName"></div>
                                                    <div class="file-size" id="fileSize"></div>
                                                </div>
                                            </div>
                                            <button class="remove-btn" id="removeBtn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                                    <path d="M0.75 10.75L10.75 0.75M0.75 0.75L10.75 10.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-buttons d-flex justify-content-between align-items-center mt-5">
                                    <button class="normal-font-bold btn-custom btn-no-bg text-center" data-modal-close>Cancel</button>
                                    <button id="submitRequestBtn" class="normal-font-bold btn-custom btn-active-bg text-center" style="color:#FFF">Submit Request</button>
                                </div>
                            </div>
                            <div class="col-lg-2"></div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal  -->

<button data-modal-open="request-submitted-modal" style="display:none">Request Submitted</button>

            <!-- Review submitted Modal  -->

            <div class="modal" id="request-submitted-modal">
                <div class="modal-content size mt-5">
                    <div class="container">
                        <div class="row mt-4">
                            <!-- <div class="col-lg-1"></div> -->
                            <div class="col-lg-12">
                                <div class="d-flex justify-content-center">
                                    <style>
                                        .message-svg {
                                            position: relative;
                                            width: 75px;
                                            height: 75px;
                                        }

                                        .message-svg svg:first-child {
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                        }

                                        .message-icon {
                                            position: absolute;
                                            top: 50%;
                                            left: 50%;
                                            transform: translate(-50%, -50%);
                                        }
                                    </style>
                                    <div class="message-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="75" height="75" viewBox="0 0 75 75" fill="none">
                                            <circle cx="37.5" cy="37.5" r="36.5" fill="#D8E8B7" stroke="#B5CA89" stroke-width="2" />
                                        </svg>

                                        <svg class="message-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="22" viewBox="0 0 30 22" fill="none">
                                            <path d="M15.8413 18.143H13.1429H2C1.44771 18.143 1 17.6953 1 17.143V2C1 1.44771 1.44772 1 2 1H24.2858C24.838 1 25.2858 1.44772 25.2858 2V12.4287" stroke="#B5CA89" stroke-width="2" stroke-linecap="round" />
                                            <path d="M1.25 1.25L12.6178 8.24828C12.9397 8.44644 13.3459 8.44617 13.6675 8.24758L25 1.25" stroke="#B5CA89" stroke-width="2" />
                                            <path d="M19.5715 17.1426C19.0192 17.1426 18.5715 17.5903 18.5715 18.1426C18.5715 18.6949 19.0192 19.1426 19.5715 19.1426V18.1426V17.1426ZM19.5715 18.1426V19.1426L28.143 19.1426V18.1426V17.1426L19.5715 17.1426V18.1426Z" fill="#B5CA89" />
                                            <path d="M25.2856 15.2852L28.1428 18.1423L25.2856 20.9995" stroke="#B5CA89" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                    </div>

                                    <div data-modal-submit-close class="position-absolute top-0 end-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                                            <path d="M12.8 24.0008L24 12.8008M12.8 12.8008L24 24.0008" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                    <style>
                                        [data-modal-submit-close] {
                                            position: absolute;
                                            /* top: 10px; */
                                            right: 50px;
                                            cursor: pointer;
                                        }
                                    </style>
                                </div>
                            </div>
                            <div class="col-lg-12" style="padding: 15px 35px;">

                                <div class="modal-head d-flex flex-column align-items-center justify-content-center">
                                    <h1 class="fs-28-pf-display-700 line-default mt-3">Request submitted!</h1>
                                    <h3 class="normal-light-color mt-2 text-center">Your support request has been received. Our team will review it and get <br> back to you as soon as possible — usually within 24 hours.</h3>
                                </div>

                                <style>
                                    .request-ref {
                                        padding: 8px 16px;
                                        color: #FFC97A;
                                        text-align: center;
                                        font-family: Lato;
                                        font-size: 16px;
                                        font-style: normal;
                                        font-weight: 600;
                                        line-height: normal;
                                        border-radius: 96px;
                                        background: rgba(255, 201, 122, 0.10);
                                        margin-bottom: 16px;
                                        width: 205px;
                                        height: 48px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    }

                                    .card {
                                        width: 100%;
                                        max-width: 520px;
                                        border: 1px solid #e6e6e6;
                                        border-radius: 10px;
                                        background: #fff;
                                        overflow: hidden;
                                        font-family: Arial, sans-serif;
                                    }

                                    .card-header {
                                        padding: 14px 16px;
                                        border-bottom: 1px solid #eee;
                                    }

                                    .card-body {
                                        padding: 20px;
                                    }

                                    .border-bottom {
                                        border-bottom: 1px solid #D4D4D4;
                                        padding: 5px 0;
                                    }

                                    .border-bottom:last-child {
                                        border-bottom: none;
                                    }
                                </style>

                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="request-ref mt-5">Request ref: SR-48291</div>

                                    <div class="card mt-4">

                                        <div class="card-header fs-16-600">Request summary</div>

                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                                <div class="summary-labe fs-14-400-f-color">Subject</div>
                                                <div class="value fs-14-600-f-color">Change my booking</div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                                <div class="summary-label fs-14-400-f-color">Category</div>
                                                <div class="value fs-14-600-f-color">CBookings</div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                                <div class="summary-label fs-14-400-f-color">Booking ref</div>
                                                <div class="value fs-14-600-f-color">FG-10294</div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                                <div class="summary-label fs-14-400-f-color">Submitted</div>
                                                <div class="value fs-14-600-f-color">18 Dec 2025 · 10:42</div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                                <div class="summary-label fs-14-400-f-color">Attachment</div>
                                                <div class="value fs-14-600-f-color">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none">
                                                        <path d="M2.26689 10.375H6.81608C7.31784 10.375 7.79905 10.1757 8.15385 9.82088C8.50865 9.46608 8.70797 8.98487 8.70797 8.48311V5.49392C8.70815 4.99222 8.50905 4.511 8.15446 4.15608L4.92797 0.929054C4.75228 0.75338 4.54371 0.614032 4.31416 0.518967C4.08461 0.423902 3.83859 0.374982 3.59014 0.375H2.26689C1.76513 0.375 1.28392 0.574324 0.929122 0.929122C0.574324 1.28392 0.375 1.76513 0.375 2.26689V8.48311C0.375 8.98487 0.574324 9.46608 0.929122 9.82088C1.28392 10.1757 1.76513 10.375 2.26689 10.375Z" stroke="#9D9B98" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M4.3728 0.570312V3.62977C4.3728 3.91649 4.4867 4.19147 4.68944 4.39421C4.89219 4.59695 5.16716 4.71085 5.45388 4.71085H8.51442" stroke="#9D9B98" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M1.97388 7.80717V7.26663M1.97388 7.26663V6.18555H2.51442C2.65778 6.18555 2.79527 6.2425 2.89664 6.34387C2.99801 6.44524 3.05496 6.58273 3.05496 6.72609C3.05496 6.86945 2.99801 7.00694 2.89664 7.10831C2.79527 7.20968 2.65778 7.26663 2.51442 7.26663H1.97388ZM6.2982 7.80717V7.13149M6.2982 7.13149V6.18555H7.10901M6.2982 7.13149H7.10901M4.13604 7.80717V6.18555H4.40631C4.62135 6.18555 4.82758 6.27097 4.97964 6.42303C5.1317 6.57508 5.21712 6.78132 5.21712 6.99636C5.21712 7.2114 5.1317 7.41763 4.97964 7.56969C4.82758 7.72174 4.62135 7.80717 4.40631 7.80717H4.13604Z" stroke="#9D9B98" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    &nbsp;
                                                    fursgo-invoice.pdf
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="footer-text mt-4 fs-14-400-f-color">
                                        Need urgent help?
                                        <a href="#" class="fs-14-600-f-color cursor" style="color:#FFC97A" data-open-chat>Chat with our support team</a>
                                        or visit the <a href="{{ route('help-and-support') }}" class="fs-14-600-f-color cursor" style="color:#FFC97A" id="helpCentreLink">Help Centre</a>.
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review submitted Modal  -->

<div class="box-wrapper d-flex flex-column align-items-center justify-content-center gap-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="55" height="41" viewBox="0 0 55 41" fill="none">
                    <path d="M46.0703 18.7454C51.3119 20.4152 53.6497 22.9059 53.6497 26.825C53.6497 30.3109 50.4696 33.0162 48.5758 34.3199C48.4861 34.3815 48.4127 34.464 48.362 34.5602C48.3113 34.6565 48.2848 34.7637 48.2847 34.8725V38.5514C48.2847 39.2511 47.5838 39.7306 46.9692 39.3961C45.8882 38.808 44.9139 38.0353 44.092 37.1097C44.0148 37.0228 43.9164 36.9574 43.8064 36.92C43.6963 36.8827 43.5784 36.8745 43.4643 36.8964C43.082 36.9702 42.6917 37.0748 42.2961 37.1807C41.612 37.3645 40.9066 37.555 40.2373 37.555C36.793 37.555 34.3814 36.8146 31.9162 35.0026" stroke="#3B3731" stroke-width="2" stroke-linecap="round" />
                    <path d="M21.0264 1C32.3911 1 40.5781 7.9409 40.5781 16.7656C40.5781 21.0626 38.7313 24.6322 35.4844 27.1523C32.2167 29.6885 27.4789 31.1963 21.6973 31.1963C20.0446 31.1963 18.1304 30.8774 16.4082 30.5488L16.3984 30.5469L16.1865 30.5225C16.0448 30.5151 15.9027 30.5264 15.7637 30.5547L15.5576 30.6104C15.2875 30.7022 15.0454 30.8611 14.8535 31.0723H14.8525L14.8447 31.0811C12.8976 33.277 10.6876 34.8904 9.04785 35.9189V29.5068C9.04776 29.3037 9.01025 29.1029 8.93848 28.9141L8.85449 28.7295L8.75 28.5557C8.63484 28.3896 8.48981 28.2449 8.32227 28.1299C4.84325 25.736 1.00017 21.9095 1 16.7686C1 8.11674 9.56493 1.00013 21.0264 1Z" stroke="#3B3731" stroke-width="2" />
                    <circle cx="12.7418" cy="17.5108" r="1.56478" fill="#3B3731" />
                    <circle cx="21.0873" cy="17.5108" r="1.56478" fill="#3B3731" />
                    <circle cx="29.4328" cy="17.5108" r="1.56478" fill="#3B3731" />
                </svg>
                <p class="normal-font-bold">Chat with FursGo</p>
                <p class="simple-font text-center"> Chat live with a FursGo team member for help with bookings, payments, or account questions.</p>
                <button data-open-chat class="normal-font-bold btn-custom btn-groomer-bg text-center mt-3" style="color:#fff">Start chat</button>
                <p class="simple-font">Responses usually within 24 hours</p>
            </div>
        </div>
    </div>
    <div class="col-lg-1"></div>

</div>

