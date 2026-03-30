{{-- resources/views/components/confirm-pay.blade.php --}}
@props([
'groomerSpaceConfirmationUrl' => url('booking-groomer-and-space/confirmation'),
'spaceConfirmationUrl' => url('booking-space/confirmation'),
])

<div class="confirm-pay">
    <div id="confirmPayDisplay" style="display: none;" class="confirmPayDisplay">
        <div style="display: flex; gap: 24px; align-items: center;">
            <div id="confirmPayInitials"
                style="width: 50px; height: 50px; border-radius: 50%; border: 1px solid #9D9B98; display: flex; align-items: center; justify-content: center; font-family: Lato; font-size: 16px; font-weight: 500; color: #3B3731; flex-shrink: 0; background: #FFF;">
                MV
            </div>
            <div style="font-family: Lato; line-height: 1.6;">
                <p id="confirmPayEmail"
                    style="color: #3B3731; font-family: Lato; font-size: 16px; font-weight: 400; margin: 0;">
                    emailaddress@gmail.com
                </p>
                <p id="confirmPayAddress"
                    style="color: #3B3731; font-family: Lato; font-size: 16px; font-weight: 400; margin: 10px 0;">
                    Full Name, 234 Elm Street, Springfield, IL 62704, United Kingdom - 07123 456789
                </p>
                <p id="confirmPayMessage"
                    style="color: #9D9B98; font-family: Lato; font-size: 16px; font-weight: 400; margin: 0;">
                    Optional Message - Lorem ipsum dolor sit amet.
                </p>
            </div>
        </div>
        <div>
            <button type="button" id="confirmPayChangeBtn"
                style="background: #EBEBEB; border: none; border-radius: 20px; padding: 8px 20px; font-family: Lato; font-size: 14px; font-weight: 500; color: #3B3731; cursor: pointer;">Change</button>
        </div>
    </div>

    <form id="demoForm" novalidate>
        <div class="form-grid">
            <div class="full-row">
                <label for="country">Country / Region</label>
                <div class="cp-input-wrap select-wrap" data-field="country">
                    <select id="country" name="country" data-furs-dropdown>
                        <option value="">Select country...</option>
                        <option value="uk">United Kingdom</option>
                        <option value="us">United States</option>
                        <option value="pk">Pakistan</option>
                    </select>
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div>
                <label for="fullname">Full Name</label>
                <div class="cp-input-wrap" data-field="fullname">
                    <input id="fullname" name="fullname" type="text" placeholder="Lorem Ipsum" />
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div>
                <label for="email">Email Address</label>
                <div class="cp-input-wrap" data-field="email">
                    <input id="email" name="email" type="email" placeholder="you@example.com" />
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="full-row">
                <label for="cp-address">Address</label>
                <div class="cp-input-wrap" data-field="address">
                    <input id="cp-address" name="address" type="text" placeholder="Start typing address..." />
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div>
                <label for="city">City</label>
                <div class="cp-input-wrap" data-field="city">
                    <input id="city" name="city" type="text" placeholder="Lorem Ipsum" />
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div>
                <label for="postcode">Postcode</label>
                <div class="cp-input-wrap" data-field="postcode">
                    <input id="postcode" name="postcode" type="text" placeholder="LOR 3MP" />
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div>
                <label for="phone">Phone Number</label>
                <div class="cp-input-wrap" data-field="phone">
                    <input id="phone" name="phone" type="tel" placeholder="+44 7123 456789" />
                    <span class="icon check-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                    <span class="icon x-icon" aria-hidden="true" style="opacity:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93282 6.51849 5.93273 6.99328 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93283 12.4296 5.93274 12.9044 6.22559 13.1973C6.51847 13.4897 6.99335 13.4899 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4897 12.9045 13.4899 13.1973 13.1973C13.49 12.9045 13.4898 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.49 6.99336 13.4898 6.51851 13.1973 6.22559Z"
                                fill="#FF6E6E" />
                        </svg>
                    </span>
                </div>
                <div class="error-msg" id="phoneError">Please enter valid phone number</div>
            </div>

            <div class="full-row">
                <label for="message">Message <span>(optional)</span></label>
                <div class="cp-input-wrap" data-field="message">
                    <textarea id="message" name="message" placeholder="Write a note (optional)"></textarea>
                    <span class="icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="buttons">
            <button type="button" class="btn cancel"
                onclick="document.getElementById('demoForm').reset(); resetStates();">Cancel</button>
            <button type="submit" class="btn save">Save</button>
        </div>
    </form>

    <div class="payment-method">
        <h2>Payment</h2>
        <div class="payment-icons">
            <p>Express Checkout</p>
            <div class="pay-icons-container">
                <div class="pay-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="51" height="21" viewBox="0 0 51 21" fill="none">
                        <path
                            d="M9.31859 2.70741C8.72093 3.41666 7.76448 3.97626 6.80813 3.89624C6.6886 2.93727 7.15677 1.91815 7.70472 1.28873C8.30237 0.559503 9.34847 0.0399574 10.1953 0C10.2949 0.999034 9.90629 1.97809 9.31859 2.70741ZM10.1854 4.08614C8.80062 4.00623 7.61517 4.8753 6.95765 4.8753C6.29026 4.8753 5.28401 4.1261 4.1882 4.14608C2.76369 4.16606 1.43868 4.97519 0.711431 6.26402C-0.782911 8.84157 0.322954 12.658 1.76739 14.756C2.47472 15.795 3.3215 16.9338 4.43723 16.8939C5.49329 16.8539 5.91165 16.2046 7.18685 16.2046C8.47191 16.2046 8.84056 16.8939 9.95629 16.8739C11.1119 16.8539 11.8391 15.835 12.5464 14.7959C13.3533 13.6171 13.6821 12.4681 13.702 12.4082C13.6821 12.3882 11.4706 11.5391 11.4506 8.98142C11.4307 6.8435 13.194 5.82449 13.2737 5.76455C12.2774 4.28593 10.7234 4.1261 10.1854 4.08614ZM18.185 1.18883V16.7641H20.5958V11.4392H23.9331C26.9815 11.4392 29.1234 9.34124 29.1234 6.30408C29.1234 3.26702 27.0213 1.18893 24.0128 1.18893L18.185 1.18883ZM20.5958 3.22696H23.3752C25.4673 3.22696 26.6627 4.34586 26.6627 6.31397C26.6627 8.28207 25.4673 9.41106 23.3652 9.41106H20.5958V3.22696ZM33.5265 16.8839C35.0408 16.8839 36.4454 16.1147 37.083 14.8958H37.1328V16.764H39.3643V9.01139C39.3643 6.76359 37.5711 5.31493 34.8116 5.31493C32.2513 5.31493 30.3585 6.78356 30.2888 8.80161H32.4606C32.6399 7.84254 33.5264 7.21311 34.7418 7.21311C36.2163 7.21311 37.0431 7.90247 37.0431 9.17132V10.0304L34.0345 10.2102C31.2352 10.38 29.721 11.529 29.721 13.5272C29.721 15.5452 31.285 16.8839 33.5265 16.8839ZM34.174 15.0357C32.889 15.0357 32.072 14.4163 32.072 13.4671C32.072 12.4882 32.859 11.9187 34.3633 11.8288L37.0431 11.659V12.538C37.0431 13.9967 35.8079 15.0357 34.1741 15.0357M42.343 21C44.6941 21 45.7999 20.101 46.7662 17.3735L51 5.46477H48.5493L45.7101 14.666H45.6603L42.8212 5.46477H40.3007L44.3851 16.804L44.166 17.4933C43.7974 18.6622 43.1997 19.1118 42.1338 19.1118C41.9445 19.1118 41.5759 19.0918 41.4264 19.0719V20.9401C41.5659 20.98 42.1635 21 42.3429 21"
                            fill="black" />
                    </svg>
                </div>
                <div class="pay-btn" style="background:#FFD88C;">
                    <img src="{{ asset('assets/images/icons/paypal-icon.png') }}" alt="Paypal" />
                </div>
                <div class="pay-btn">
                    <img src="{{ asset('assets/images/icons/g-pay-icon.png') }}" alt="G-Pay" />
                </div>
            </div>
        </div>

        <div class="card-payment-section">
            <div class="card-payment-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div
                        style="width:22px;height:22px;border-radius:50%;border:2px solid #FFD88C;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="width:12px;height:12px;border-radius:50%;background:#FFD88C;display:block;"></span>
                    </div>
                    <span style="font-family:Lato;font-size:16px;font-weight:500;color:#3B3731;">Credit/Debit Card</span>
                </div>
            </div>
            <div class="card-payment-body">
                <div class="card-form-grid">
                    <div class="card-form-group full-row">
                        <label>Card Number</label>
                        <div class="cp-input-wrap valid">
                            <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19"
                                value="0000 0000 0000 0000" />
                            <span class="icon" style="opacity:1;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="card-form-group full-row">
                        <label>Name on Card</label>
                        <div class="cp-input-wrap valid">
                            <input type="text" id="nameOnCard" placeholder="Lorem, Ipsum" value="Lorem, Ipsum" />
                            <span class="icon" style="opacity:1;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="card-form-group">
                        <label>Expiration Date</label>
                        <div style="display:flex;gap:10px;">
                            <div style="flex:1;">
                                <select data-furs-dropdown>
                                    <option value="">Month</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                </select>
                            </div>
                            <div style="flex:1;">
                                <select data-furs-dropdown>
                                    <option value="">Year</option>
                                    @for ($y = date('Y'); $y <= date('Y') + 10; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-form-group">
                        <label>CVV</label>
                        <div class="cp-input-wrap" style="width: 175px;">
                            <input type="text" placeholder="000" maxlength="4" />
                            <span class="icon" style="opacity: 1;" title="3-4 digit security code">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="#9D9B98" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-checkbox-row" onclick="toggleCardCheck('billingCheck','billingInner')">
                    <div id="billingCheck"
                        style="width:22px;height:22px;border-radius:50%;border:2px solid #FFD88C;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#fff;">
                        <span id="billingInner"
                            style="width:12px;height:12px;border-radius:50%;background:#FFD88C;display:block;"></span>
                    </div>
                    <span style="color: #3B3731; font-family: Lato; font-size: 18px; font-style: normal; font-weight: 400; line-height: normal;">Use my address as billing address</span>
                </div>
                <hr style="border: 0.5px solid #CBDCE8; margin: 0 20px;">
                <div class="card-checkbox-row" onclick="toggleCardCheck('termsCheck','termsInner')">
                    <div id="termsCheck"
                        style="width:22px;height:22px;border-radius:50%;border:2px solid #FFD88C;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#fff;">
                        <span id="termsInner"
                            style="width:12px;height:12px;border-radius:50%;background:#FFD88C;display:block;"></span>
                    </div>
                    <span style="color: #3B3731; font-family: Lato; font-size: 18px; font-style: normal; font-weight: 400; line-height: normal;">I agree to Fursgo's <a href="#">Terms of Service</a> and <a href="#">Cancellation Policy</a>.</span>
                </div>
            </div>
        </div>

        <p style="display:flex;align-items:center;gap:6px;color:#9D9B98;font-family:Lato;font-size:13px;margin-top:4px;padding-top:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="12" viewBox="0 0 10 12" fill="none">
                <path
                    d="M5 6.6C4.80972 6.59787 4.62332 6.65174 4.4659 6.75438C4.30848 6.85702 4.18759 7.00349 4.11951 7.17408C4.05142 7.34467 4.03941 7.53119 4.08509 7.70854C4.13077 7.88588 4.23195 8.04553 4.375 8.166V9C4.375 9.15913 4.44085 9.31174 4.55806 9.42426C4.67527 9.53679 4.83424 9.6 5 9.6C5.16576 9.6 5.32473 9.53679 5.44194 9.42426C5.55915 9.31174 5.625 9.15913 5.625 9V8.166C5.76805 8.04553 5.86923 7.88588 5.91491 7.70854C5.96059 7.53119 5.94858 7.34467 5.88049 7.17408C5.81241 7.00349 5.69152 6.85702 5.5341 6.75438C5.37668 6.65174 5.19028 6.59787 5 6.6ZM8.125 4.2V3C8.125 2.20435 7.79576 1.44129 7.20971 0.87868C6.62366 0.316071 5.8288 0 5 0C4.1712 0 3.37634 0.316071 2.79029 0.87868C2.20424 1.44129 1.875 2.20435 1.875 3V4.2C1.37772 4.2 0.900806 4.38964 0.549175 4.72721C0.197544 5.06477 0 5.52261 0 6V10.2C0 10.6774 0.197544 11.1352 0.549175 11.4728C0.900806 11.8104 1.37772 12 1.875 12H8.125C8.62228 12 9.09919 11.8104 9.45083 11.4728C9.80246 11.1352 10 10.6774 10 10.2V6C10 5.52261 9.80246 5.06477 9.45083 4.72721C9.09919 4.38964 8.62228 4.2 8.125 4.2ZM3.125 3C3.125 2.52261 3.32254 2.06477 3.67417 1.72721C4.02581 1.38964 4.50272 1.2 5 1.2C5.49728 1.2 5.97419 1.38964 6.32583 1.72721C6.67746 2.06477 6.875 2.52261 6.875 3V4.2H3.125V3ZM8.75 10.2C8.75 10.3591 8.68415 10.5117 8.56694 10.6243C8.44973 10.7368 8.29076 10.8 8.125 10.8H1.875C1.70924 10.8 1.55027 10.7368 1.43306 10.6243C1.31585 10.5117 1.25 10.3591 1.25 10.2V6C1.25 5.84087 1.31585 5.68826 1.43306 5.57574C1.55027 5.46321 1.70924 5.4 1.875 5.4H8.125C8.29076 5.4 8.44973 5.46321 8.56694 5.57574C8.68415 5.68826 8.75 5.84087 8.75 6V10.2Z"
                    fill="#9D9B98" />
            </svg>
            All payments are securely processed with encrypted technology.
        </p>

        <div style="margin-top:2rem;max-width:820px;">
            <h2 style="color:#3B3731;font-family:'Playfair Display';font-size:24px;font-weight:800;margin-bottom:1rem;">
                Remember me</h2>
            <div onclick="toggleCardCheck('rememberCheck','rememberInner')"
                style="display:flex;align-items:center;gap:12px;padding:18px 20px;border-radius:10px;border:1px solid #CBDCE8;background:#F8F8F8;cursor:pointer;user-select:none;max-width:820px;">
                <div id="rememberCheck"
                    style="width:22px;height:22px;border-radius:50%;border:2px solid #FFD88C;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#fff;">
                    <span id="rememberInner"
                        style="width:12px;height:12px;border-radius:50%;background:#FFD88C;display:block;"></span>
                </div>
                <span style="color: #3B3731; font-family: Lato; font-size: 18px; font-weight: 400;">Save my information for a faster checkout</span>
            </div>
        </div>

        <div style="margin-top:2rem;max-width:820px;border-top:1px solid #EBEBEB;">
            <div style="border-bottom:1px solid #EBEBEB;">
                <div onclick="togglePolicy(this)"
                    style="display:flex;justify-content:space-between;align-items:center;padding:18px 0;cursor:pointer;color: #3B3731; font-family: Lato; font-size: 20px; font-weight: 600;">
                    <span>Cancellation Policy</span>
                    <svg id="chevron-cancellation" xmlns="http://www.w3.org/2000/svg" width="16" height="10"
                        viewBox="0 0 16 10" fill="none" style="transition:transform 0.2s;transform:rotate(180deg);">
                        <path d="M14.5 8.5L8 2L1.5 8.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div style="padding-bottom:16px;">
                    <ul style="margin:0;padding-left:20px;list-style-type:disc;">
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Groomers are trained and certified professionals.</li>
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Appointments are based on the service and duration you booked.</li>
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Groomers may refuse service if your pet poses a safety risk.</li>
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Please ensure your pet is up-to-date with vaccinations.</li>
                    </ul>
                </div>
            </div>
            <div style="border-bottom:1px solid #EBEBEB;">
                <div onclick="togglePolicy(this)"
                    style="display:flex;justify-content:space-between;align-items:center;padding:18px 0;cursor:pointer;user-select:none;font-family:Lato;font-size:16px;font-weight:600;color:#3B3731;">
                    <span>Safety Policy</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10" fill="none"
                        style="transition:transform 0.2s;">
                        <path d="M14.5 1.5L8 8L1.5 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div style="display:none;padding-bottom:16px;">
                    <ul style="margin:0;padding-left:20px;list-style-type:disc;">
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">All grooming tools are sanitised after every pet.</li>
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Groomers follow industry-standard hygiene protocols.</li>
                    </ul>
                </div>
            </div>
            <div style="border-bottom:1px solid #EBEBEB;">
                <div onclick="togglePolicy(this)"
                    style="display:flex;justify-content:space-between;align-items:center;padding:18px 0;cursor:pointer;user-select:none;font-family:Lato;font-size:16px;font-weight:600;color:#3B3731;">
                    <span>Payment &amp; Fees</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="10" viewBox="0 0 16 10" fill="none"
                        style="transition:transform 0.2s;">
                        <path d="M14.5 1.5L8 8L1.5 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>
                <div style="display:none;padding-bottom:16px;">
                    <ul style="margin:0;padding-left:20px;list-style-type:disc;">
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">Payment is processed securely at time of booking.</li>
                        <li style="color: #9D9B98; font-family: Lato; font-size: 18px; line-height: 1.5;">A platform fee is included in the total price shown.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .confirm-pay h1 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 44px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
        margin-bottom: 2rem;
    }

    .confirm-pay .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .confirm-pay .full-row {
        grid-column: span 2;
    }

    .confirm-pay label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: block;
        margin-bottom: 8px;
    }

    .confirm-pay label span {
        color: #9D9B98;
        font-weight: 400;
    }

    .cp-input-wrap {
        position: relative;
        margin: 0 !important;
    }

    .cp-input-wrap>input,
    .cp-input-wrap>select {
        border-radius: 10px !important;
        border: 1px solid #D4D4D4 !important;
        background: #FFF !important;
    }

    .cp-input-wrap input[type="text"],
    .cp-input-wrap input[type="tel"],
    .cp-input-wrap input[type="email"],
    .cp-input-wrap select,
    .cp-input-wrap textarea {
        width: 100%;
        box-sizing: border-box;
        padding: 12px 44px 12px 14px;
        border-radius: 8px;
        border: 1px solid #eee6dd;
        background: #FFF;
        font-size: 14px;
        outline: none;
        transition: border-color .15s, box-shadow .12s;
        height: 48px;
    }

    .cp-input-wrap select {
        -webkit-appearance: none;
        appearance: none;
        padding-right: 44px;
        background-color: #FFF;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none"><path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>');
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 15px 8px;
        cursor: pointer;
    }

    .cp-input-wrap textarea {
        min-height: 100px;
        resize: vertical;
        padding: 12px 14px;
    }

    .cp-input-wrap input:focus,
    .cp-input-wrap select:focus,
    .cp-input-wrap textarea:focus {
        border-color: #e6dccd;
        box-shadow: 0 0 0 3px rgba(201, 221, 160, 0.12);
    }

    .icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        opacity: 0;
        transition: opacity .12s, transform .12s;
    }

    .cp-input-wrap.valid .icon {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }

    .cp-input-wrap.error input,
    .cp-input-wrap.error textarea,
    .cp-input-wrap.error select {
        border-color: rgba(255, 107, 107, 0.22);
        box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.06);
    }

    .error-msg {
        color: #FF6E6E;
        font-size: 13px;
        margin-top: 6px;
        font-family: Lato;
        display: none;
    }

    .cp-input-wrap.error+.error-msg {
        display: block;
    }

    .buttons {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 18px;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 26px;
        border: 1px solid #f0d5c0;
        background: transparent;
        cursor: pointer;
        font-weight: 600;
    }

    .btn.cancel {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        border: none;
        text-decoration: underline;
    }

    .btn.save {
        width: 120px;
        height: 48px;
        border-radius: 96px;
        border: 1px solid #FFC97A;
        background: #FFF;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.05);
        color: #FFC97A;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
    }

    .payment-method h2 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
        margin-top: 3rem;
    }

    .payment-icons {
        max-width: 820px;
        border-radius: 10px;
        background: #F8F8F8;
        padding: 20px;
        margin-top: 2rem;
    }

    .payment-icons p {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .payment-icons .pay-icons-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
    }

    .pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 50px;
        width: 247px;
        border-radius: 8px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
        margin: 1rem 0;
    }

    .pay-btn img,
    .pay-btn svg {
        max-width: 100px;
    }

    .card-payment-section {
        margin-top: 1.5rem;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        overflow: visible !important;
        max-width: 820px;
    }

    .card-payment-header {
        padding: 16px 20px;
        border-bottom: 1px solid #CBDCE8;
        background: #F8F8F8;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .confirm-pay .furs-dd__list {
        max-height: 550px !important;
    }

    .confirm-pay .furs-dd.is-open .furs-dd__panel {
        max-height: 600px !important;
    }

    .card-payment-body {
        padding-bottom: 20px;
    }

    .card-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        padding: 20px;
    }

    .card-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .card-form-group.full-row {
        grid-column: span 2;
    }

    .card-form-group label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        margin-top: 10px;
    }

    .card-checkbox-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 20px;
        cursor: pointer;
        user-select: none;
    }

    .confirmPayDisplay {
        background: #F9F9F9;
        padding: 24px 30px;
        border-radius: 12px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Props passed from Blade
        const groomerSpaceConfirmationUrl = "{{ $groomerSpaceConfirmationUrl }}";
        const spaceConfirmationUrl = "{{ $spaceConfirmationUrl }}";

        const form = document.getElementById('demoForm');
        if (!form) return;

        window.togglePolicy = function(header) {
            const body = header.nextElementSibling;
            const chevron = header.querySelector('svg');
            const isOpen = body.style.display !== 'none';
            body.style.display = isOpen ? 'none' : 'block';
            chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        };

        const rules = {
            country: v => v !== '',
            fullname: v => v.trim().length >= 2,
            email: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v),
            address: v => v.trim().length > 3,
            city: v => v.trim().length >= 2,
            postcode: v => v.trim().length >= 2,
            phone: v => /^\+?[\d\s\-\(\)]{7,20}$/.test(v),
            message: v => true
        };

        function wrapFor(name) {
            return document.querySelector('.cp-input-wrap[data-field="' + name + '"]');
        }

        function setValid(name, state) {
            const wrap = wrapFor(name);
            if (!wrap) return;
            wrap.classList.toggle('valid', state);
            wrap.classList.toggle('error', !state && name === 'phone');
            if (name === 'phone') {
                const x = wrap.querySelector('.x-icon');
                const check = wrap.querySelector('.check-icon');
                if (state) {
                    if (x) x.style.opacity = 0;
                    if (check) check.style.opacity = 1;
                } else {
                    if (x) x.style.opacity = 1;
                    if (check) check.style.opacity = 0;
                }
            }
        }

        function checkAllValid() {
            let allOk = true;
            Object.keys(rules).forEach(name => {
                const el = form.elements[name];
                if (!el) {
                    allOk = false;
                    return;
                }
                const val = el.value || '';
                const fn = rules[name];
                if (fn && !fn(val)) allOk = false;
            });

            const sidebarBtn = document.getElementById('confirmPayBtnSidebar');
            if (sidebarBtn) {
                if (allOk) {
                    sidebarBtn.disabled = false;
                    sidebarBtn.style.backgroundColor = '#C9DDA0';
                    sidebarBtn.style.color = '#FFFFFF';
                    sidebarBtn.style.cursor = 'pointer';
                } else {
                    sidebarBtn.disabled = true;
                    sidebarBtn.style.backgroundColor = '#F8F8F8';
                    sidebarBtn.style.color = '#DDD';
                    sidebarBtn.style.cursor = 'not-allowed';
                }
            }
            return allOk;
        }

        function validateOne(name) {
            const el = form.elements[name];
            if (!el) return false;
            const val = el.value || '';
            const fn = rules[name];
            const ok = fn ? fn(val) : true;
            setValid(name, ok);
            if (name === 'phone') {
                const err = document.getElementById('phoneError');
                if (err) err.style.display = ok ? 'none' : 'block';
            }
            checkAllValid();
            return ok;
        }

        Object.keys(rules).forEach(name => {
            const el = form.elements[name];
            if (!el) return;
            el.addEventListener('input', () => validateOne(name));
            el.addEventListener('blur', () => validateOne(name));
            if (el.tagName.toLowerCase() === 'select') {
                el.addEventListener('change', () => validateOne(name));
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let ok = true;
            Object.keys(rules).forEach(name => {
                if (!validateOne(name)) ok = false;
            });

            if (ok) {
                const fullname = form.elements['fullname'].value.trim();
                const email = form.elements['email'].value.trim();
                const address = form.elements['address'].value.trim();
                const city = form.elements['city'].value.trim();
                const postcode = form.elements['postcode'].value.trim();
                const countrySelect = form.elements['country'];
                const countryText = countrySelect.options[countrySelect.selectedIndex]?.text || '';
                const phone = form.elements['phone'].value.trim();
                const message = form.elements['message'].value.trim();

                let initials = "MV";
                if (fullname) {
                    let parts = fullname.split(' ');
                    initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
                }

                document.getElementById('confirmPayInitials').innerText = initials;
                document.getElementById('confirmPayEmail').innerText = email;
                document.getElementById('confirmPayAddress').innerText = `${fullname}, ${address}, ${city}, ${postcode}, ${countryText} - ${phone}`;
                document.getElementById('confirmPayMessage').innerText = message ? `Optional Message - ${message}` : '';

                form.style.display = 'none';
                document.getElementById('confirmPayDisplay').style.display = 'flex';
            } else {
                const firstErr = document.querySelector('.cp-input-wrap.error');
                if (firstErr) firstErr.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });

        const changeBtn = document.getElementById('confirmPayChangeBtn');
        if (changeBtn) {
            changeBtn.addEventListener('click', function() {
                document.getElementById('confirmPayDisplay').style.display = 'none';
                form.style.display = 'block';
            });
        }

        window.resetStates = function() {
            document.querySelectorAll('.cp-input-wrap').forEach(w => {
                w.classList.remove('valid', 'error');
                const x = w.querySelector('.x-icon');
                if (x) x.style.opacity = 0;
            });
            const phoneErr = document.getElementById('phoneError');
            if (phoneErr) phoneErr.style.display = 'none';
        };

        window.toggleCardCheck = function(boxId, innerId) {
            const inner = document.getElementById(innerId);
            const box = document.getElementById(boxId);
            if (!inner || !box) return;
            const visible = inner.style.display !== 'none';
            inner.style.display = visible ? 'none' : 'block';
            box.style.borderColor = visible ? '#ccc' : '#FFD88C';
        };

        const cn = document.getElementById('cardNumber');
        if (cn) {
            cn.addEventListener('input', function() {
                let v = this.value.replace(/\D/g, '').substring(0, 16);
                this.value = v.replace(/(.{4})/g, '$1 ').trim();
            });
        }

        // Dropdown toggle logic for select-wrap
        document.querySelectorAll('.cp-input-wrap.select-wrap select').forEach(select => {
            const wrap = select.closest('.cp-input-wrap.select-wrap');
            if (select.hasAttribute('data-furs-dropdown')) return;

            let isOpen = false;

            select.addEventListener('mousedown', function() {
                if (isOpen) {
                    wrap.classList.remove('open');
                    isOpen = false;
                } else {
                    wrap.classList.add('open');
                    isOpen = true;
                }
            });

            select.addEventListener('blur', function() {
                setTimeout(() => {
                    wrap.classList.remove('open');
                    isOpen = false;
                }, 100);
            });

            select.addEventListener('change', function() {
                wrap.classList.remove('open');
                isOpen = false;
            });
        });

        const sidebarBtn = document.getElementById('confirmPayBtnSidebar');
        if (sidebarBtn) {
            sidebarBtn.addEventListener('click', function(e) {
                if (!sidebarBtn.disabled) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', {
                        cancelable: true,
                        bubbles: true
                    }));

                    let ok = true;
                    Object.keys(rules).forEach(name => {
                        const el = form.elements[name];
                        if (!el) {
                            ok = false;
                            return;
                        }
                        const val = el.value || '';
                        const fn = rules[name];
                        if (fn && !fn(val)) ok = false;
                    });

                    if (ok) {
                        const currentPath = window.location.pathname;
                        if (currentPath.includes('booking-groomer-and-space')) {
                            window.location.href = groomerSpaceConfirmationUrl;
                        } else if (currentPath.includes('booking-space')) {
                            window.location.href = spaceConfirmationUrl;
                        }
                    }
                }
            });
        }
    });
</script>