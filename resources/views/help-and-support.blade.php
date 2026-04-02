@extends('layouts.app')

@section('content')

<section class="container mb-5 mt-5">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-lg-12">
                    <div class="top-head d-flex flex-column align-items-center justify-content-center">
                        <h1 class="large-font">Help & Support Center</h1>
                        <form action="search.php">
                            <div class="search-wrapper">
                                <input type="text" placeholder="Search for topics like refunds, bookings, payments."
                                    class="normal-font-weight" name="search_results">
                                <button class="search-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42"
                                        fill="none">
                                        <circle cx="21" cy="21" r="21" fill="#FFC97A" />
                                        <path
                                            d="M19.7354 14.75C22.4886 14.75 24.7207 16.9821 24.7207 19.7354C24.7207 21.1492 24.1329 22.4248 23.1865 23.333C22.2901 24.1932 21.0751 24.7207 19.7354 24.7207C16.9821 24.7207 14.75 22.4886 14.75 19.7354C14.75 16.982 16.982 14.75 19.7354 14.75Z"
                                            stroke="white" stroke-width="1.5" />
                                        <path
                                            d="M28.4697 29.5303C28.7626 29.8232 29.2374 29.8232 29.5303 29.5303C29.8232 29.2374 29.8232 28.7626 29.5303 28.4697L29 29L28.4697 29.5303ZM23.7059 23.7059L23.1755 24.2362L28.4697 29.5303L29 29L29.5303 28.4697L24.2362 23.1755L23.7059 23.7059Z"
                                            fill="white" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                        <div class="common-topics d-flex align-items-center justify-content-center gap-20 mb-3">
                            <p>Common Topics</p>
                            <p class="bg cursor">Bookings</p>
                            <p class="bg cursor">Payments</p>
                            <p class="bg cursor">Account</p>
                            <p class="bg cursor">Pets</p>
                            <p class="bg cursor">Policies</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="d-flex align-items-center justify-content-center mt-5">
                        <h1 class="large-font">FAQs</h1>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                    <div class="support-tabs d-flex align-items-center justify-content-between mt-5" data-tabs>

                        <div class="bookings-tab help-tabs tab-btn d-flex align-items-center flex-column cursor active"
                            data-tab="tab-bookings">
                            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" width="53" height="49"
                                viewBox="0 0 53 49" fill="none">
                                <path class="bg"
                                    d="M2.56033 18.379C2.45541 20.2698 2.45541 22.5192 2.45541 25.2216V29.4961C2.45541 37.5555 2.45779 41.5842 5.23836 44.0891C8.01892 46.5939 12.4973 46.5939 21.4516 46.5939H30.9497C39.904 46.5939 44.38 46.5917 47.163 44.0891C49.9459 41.5864 49.9459 37.5555 49.9459 29.4961V25.2216C49.9459 22.519 49.9456 20.2697 49.8404 18.379H2.56033Z"
                                    fill="#FFC97A" fill-opacity="0.125" />
                                <path class="check"
                                    d="M18.6586 32.6426L23.7623 37.7463L33.6203 27.8883C34.0835 27.4251 34.0849 26.6745 33.6233 26.2097C33.1594 25.7425 32.4042 25.7412 31.9387 26.2067L23.7623 34.3831L20.3343 30.9646C19.871 30.5026 19.1212 30.5032 18.6586 30.9658C18.1955 31.4288 18.1955 32.1796 18.6586 32.6426Z"
                                    fill="#FFC97A" />
                                <path class="stroke"
                                    d="M0.5 23.8852C0.5 14.6029 0.5 9.96055 3.51366 7.07816C6.52732 4.19576 11.3744 4.1933 21.071 4.1933H31.3566C41.0532 4.1933 45.9029 4.1933 48.914 7.07816C51.925 9.96301 51.9276 14.6029 51.9276 23.8852V28.8081C51.9276 38.0904 51.9276 42.7327 48.914 45.6151C45.9003 48.4975 41.0532 48.5 31.3566 48.5H21.071C11.3744 48.5 6.52474 48.5 3.51366 45.6151C0.502571 42.7303 0.5 38.0904 0.5 28.8081V23.8852Z"
                                    stroke="#3B3731" />
                                <path class="stroke"
                                    d="M12.9681 4.17459V0.5M39.4334 4.17459V0.5M1.05872 16.4232H51.3428"
                                    stroke="#3B3731" stroke-linecap="round" />
                            </svg>
                            <p class="normal-font-bold mt-2">Bookings</p>
                        </div>

                        <div class="payments-tab help-tabs tab-btn d-flex align-items-center flex-column cursor"
                            data-tab="tab-payments">
                            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" width="64" height="49"
                                viewBox="0 0 64 49" fill="none">
                                <path
                                    d="M54.5 0.5H8.9C4.26081 0.5 0.5 4.26081 0.5 8.9V40.1C0.5 44.7392 4.26081 48.5 8.9 48.5H54.5C59.1392 48.5 62.9 44.7392 62.9 40.1V8.9C62.9 4.26081 59.1392 0.5 54.5 0.5Z"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M0.5 14.9H62.9H0.5Z" fill="#D4D4D4" />
                                <path d="M0.5 14.9H62.9" stroke="#3B3731" stroke-linejoin="round" />
                                <rect x="1.79999" y="16.1" width="59.8" height="5.2" fill="#EFEFEF" />
                                <rect class="accent" x="10.5" y="34.5" width="15" height="4" rx="2" fill="#D4D4D4" />
                            </svg>

                            <p class="normal-font-bold mt-2">Payments</p>
                        </div>

                        <div class="account-tab help-tabs tab-btn d-flex align-items-center flex-column cursor"
                            data-tab="tab-account">
                            <svg xmlns="http://www.w3.org/2000/svg" width="45" height="50" viewBox="0 0 45 50"
                                fill="none">
                                <path class="circle"
                                    d="M0.75 48.75V46.0833C0.75 37.2565 7.92341 30.0831 16.7502 30.0831H27.417C36.2437 30.0831 43.4171 37.2565 43.4171 46.0833V48.75"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path class="bottom"
                                    d="M2.75 49.0622C2.75 40.0779 9.30689 32.0622 17.375 32.0622H27.125C35.1931 32.0622 41.75 40.0779 41.75 49.0622"
                                    fill="#EFEFEF" />
                                <path
                                    d="M22.0832 22.0836C16.1898 22.0836 11.4164 17.3102 11.4164 11.4168C11.4164 5.52339 16.1898 0.75 22.0832 0.75C27.9766 0.75 32.75 5.52339 32.75 11.4168C32.75 17.3102 27.9766 22.0836 22.0832 22.0836Z"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <p class="normal-font-bold mt-2">Account</p>
                        </div>

                        <div class="pets-tab help-tabs tab-btn d-flex align-items-center flex-column cursor"
                            data-tab="tab-pets">
                            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" width="60" height="48"
                                viewBox="0 0 60 48" fill="none">
                                <path class="bg"
                                    d="M30 20.1367C34.9421 20.1367 38.6136 22.8242 40.9766 26.2188L40.9775 26.2197C43.322 29.5685 44.5 33.7314 44.5 37.0908C44.5 40.8917 42.2642 43.551 39.4531 45.1758H39.4521C36.6749 46.7838 33.1669 47.5 30 47.5C27.0314 47.5 23.7622 46.8668 21.0762 45.4658L20.5469 45.1758L20.0264 44.8594C17.4622 43.2181 15.5 40.6548 15.5 37.0908C15.5 33.8364 16.6052 29.828 18.8057 26.5361L19.0225 26.2197C21.3901 22.8286 25.0625 20.1367 30 20.1367ZM6.42871 20.1367C8.20655 20.1368 9.65921 21.1717 10.6348 22.5244L10.8232 22.7988C11.8038 24.2943 12.3574 26.2689 12.3574 28.3633C12.3574 30.4577 11.8038 32.4322 10.8232 33.9277V33.9287C9.85172 35.4166 8.32546 36.5908 6.42871 36.5908C4.53251 36.5908 3.00627 35.4132 2.03418 33.9287V33.9277C1.0536 32.4321 0.5 30.4578 0.5 28.3633C0.500059 26.3996 0.98661 24.5418 1.85547 23.085L2.03418 22.7988C3.00573 21.3109 4.53189 20.1367 6.42871 20.1367ZM53.5713 20.1367C55.4676 20.1367 56.9937 21.3143 57.9658 22.7988C58.9464 24.2943 59.4999 26.2689 59.5 28.3633C59.5 30.4578 58.9464 32.4321 57.9658 33.9277V33.9287C56.9943 35.4167 55.4681 36.5908 53.5713 36.5908C51.6751 36.5908 50.1488 35.4132 49.1768 33.9287V33.9277L48.998 33.6416C48.1294 32.1847 47.6426 30.3268 47.6426 28.3633C47.6426 26.3997 48.1292 24.5418 48.998 23.085L49.1768 22.7988C50.1483 21.3109 51.6745 20.1368 53.5713 20.1367ZM19.2861 0.5C21.1823 0.500174 22.7087 1.67856 23.6807 3.16309C24.6612 4.65865 25.2139 6.63314 25.2139 8.72754C25.2138 10.822 24.6612 12.7965 23.6807 14.292H23.6797C22.7082 15.7798 21.1828 16.9539 19.2861 16.9541C17.3899 16.9541 15.8637 15.7765 14.8916 14.292H14.8906C13.9101 12.7965 13.3575 10.822 13.3574 8.72754C13.3574 6.63317 13.9101 4.65864 14.8906 3.16309L14.8916 3.16211C15.8632 1.67418 17.3893 0.5 19.2861 0.5ZM40.7139 0.5C42.6102 0.5 44.1363 1.67845 45.1084 3.16309H45.1094C46.0899 4.65864 46.6426 6.63317 46.6426 8.72754C46.6425 10.822 46.0899 12.7965 45.1094 14.292H45.1084C44.1368 15.7799 42.6107 16.9541 40.7139 16.9541C38.8177 16.9539 37.2913 15.7765 36.3193 14.292C35.3388 12.7965 34.7862 10.822 34.7861 8.72754C34.7861 6.63314 35.3388 4.65865 36.3193 3.16309L36.3203 3.16211C37.2918 1.67432 38.8173 0.500172 40.7139 0.5Z"
                                    fill="#E5E5E5" stroke="#3B3731" />
                            </svg>
                            <p class="normal-font-bold mt-2">Pets</p>
                        </div>

                        <div class="safety-tab help-tabs tab-btn d-flex align-items-center flex-column cursor"
                            data-tab="tab-safety">
                            <svg class="tab-icon" xmlns="http://www.w3.org/2000/svg" width="39" height="48"
                                viewBox="0 0 39 48" fill="none">
                                <path class="bg"
                                    d="M19.2314 0.589844C19.3392 0.551626 19.4566 0.550954 19.5645 0.588867L38.166 7.13379C38.366 7.20417 38.4998 7.39345 38.5 7.60547V27.1172C38.5 32.1351 34.6089 36.812 30.2061 40.4746C25.8337 44.1119 21.1026 46.63 19.6953 47.3447C19.5673 47.4097 19.4327 47.4097 19.3047 47.3447C17.8974 46.63 13.1663 44.1119 8.79395 40.4746C4.39114 36.812 0.5 32.1351 0.5 27.1172V7.60352C0.5 7.39205 0.63285 7.20279 0.832031 7.13184L19.2314 0.589844Z"
                                    stroke="#3B3731" />
                                <path class="check"
                                    d="M12.8384 25.2797L17.9422 30.3834L27.8002 20.5254C28.2634 20.0622 28.2647 19.3116 27.8031 18.8468C27.3393 18.3796 26.5841 18.3783 26.1185 18.8438L17.9422 27.0202L14.5141 23.6017C14.0509 23.1397 13.301 23.1403 12.8384 23.6028C12.3754 24.0659 12.3754 24.8166 12.8384 25.2797Z"
                                    fill="#D4D4D4" />
                            </svg>
                            <p class="normal-font-bold mt-2">Safety</p>
                        </div>

                    </div>

                    <div class="help-panel tab-panels d-flex align-items-center justify-content-center mt-5">

                        <div class="tab-panel active" id="tab-bookings">
                            <h2 class="large-font">Bookings</h2>
                            <h3 class="normal-font-weight" style="color:#9D9B98">View booking-related questions.</h3>
                            <!-- accordian -->
                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">How do I
                                book a grooming appointment?</button>
                            <!-- <hr class="mt-4" > -->
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Can I
                                choose my groomer?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">What
                                grooming services are available?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Do you
                                offer in-home dog grooming?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Can I
                                cancel or reschedule my booking?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Can I
                                cancel or reschedule my booking?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">What
                                happens if the groomer cancels?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">What if
                                there’s an issue with a booking?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                                    veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                                    consequat.</p>
                            </div>

                        </div>

                        <div class="tab-panel" id="tab-payments">
                            <h2 class="large-font">Payments</h2>
                            <h3 class="normal-font-weight" style="color:#9D9B98">View payment-related questions.</h3>
                            <!-- accordian -->
                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">How is
                                pricing determined?</button>
                            <!-- <hr class="mt-4" > -->
                            <div class="panel">
                                <p class="normal-font-weight">Pricing is set individually by each groomer and reflects
                                    factors such as your dog’s size, coat condition, and the type of grooming service
                                    selected. All prices are shown clearly before you confirm your booking.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">When do I
                                pay?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Payment is taken at the time of booking to secure your
                                    appointment and confirm your chosen time slot. This helps ensure a smooth experience
                                    for both you and the groomer.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Will I
                                receive a refund if I cancel?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Refunds depend on how much notice is given and the
                                    individual groomer’s cancellation policy. Full details are always available before
                                    you complete your booking.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">How are
                                Space Owners paid?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Space rental fees are paid automatically through the
                                    platform once bookings are completed, providing a simple and predictable income
                                    stream.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">How do
                                groomers get paid?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Payments to groomers are handled automatically after each
                                    service is completed, with the platform fee deducted, making payouts simple and
                                    reliable.</p>
                            </div>

                        </div>

                        <div class="tab-panel" id="tab-account">
                            <h2 class="large-font">Account</h2>
                            <h3 class="normal-font-weight" style="color:#9D9B98">View account-related questions.</h3>
                            <!-- accordian -->
                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">What is
                                FursGo?</button>
                            <!-- <hr class="mt-4" > -->
                            <div class="panel">
                                <p class="normal-font-weight">FursGo is a dog grooming booking platform designed to make
                                    finding and booking quality grooming simple and stress-free. We bring dog owners
                                    together with trusted professional groomers and licensed grooming spaces, all
                                    through one easy-to-use platform.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Does FursGo
                                provide grooming services?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Grooming services on FursGo are carried out by independent
                                    professional groomers. While FursGo manages the booking and payment experience, the
                                    grooming itself is always provided by experienced professionals.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">What makes
                                FursGo different?</button>
                            <div class="panel">
                                <p class="normal-font-weight">FursGo is built around care, trust, and ease of use. Dog
                                    owners can book with confidence, groomers have the freedom to work flexibly, and
                                    space owners can earn from their facilities without added complexity.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Who is a
                                Space Owner on FursGo?</button>
                            <div class="panel">
                                <p class="normal-font-weight">A Space Owner is a licensed grooming salon or studio that
                                    offers its facilities to independent groomers on a time-slot basis through FursGo.
                                </p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Are
                                groomers employed by FursGo?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Groomers listed on FursGo operate independently. The
                                    platform provides booking and payment support, while groomers remain in control of
                                    their own services.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">How do I
                                contact FursGo?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Our support team can be reached anytime through the
                                    support section on the website or app, and we’ll be happy to assist.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Do I need
                                an account to use FursGo?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Creating an account allows you to manage bookings,
                                    communicate with groomers, and access support easily in one place.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Who can
                                join FursGo as a groomer?</button>
                            <div class="panel">
                                <p class="normal-font-weight">FursGo is open to professional dog groomers who meet our
                                    platform standards and share our commitment to quality care, professionalism, and
                                    reliability.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Why should
                                I list my space on FursGo?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Listing your space allows you to earn from unused capacity
                                    without the need to hire staff or manage additional bookings yourself. FursGo
                                    handles the booking and payment process.</p>
                            </div>

                        </div>

                        <div class="tab-panel" id="tab-pets">
                            <h2 class="large-font">Pets </h2>
                            <h3 class="normal-font-weight" style="color:#9D9B98">View pets-related questions.</h3>
                            <!-- accordian -->
                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Is my dog
                                insured during grooming?</button>
                            <!-- <hr class="mt-4" > -->
                            <div class="panel">
                                <p class="normal-font-weight">Groomers are expected to hold their own professional
                                    insurance. FursGo supports the booking process but does not directly insure pets.
                                </p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Do groomers
                                need their own insurance?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Professional groomers on FursGo are responsible for
                                    maintaining their own appropriate insurance to cover the services they provide.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Are Space
                                Owners responsible for grooming outcomes?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Responsibility for grooming services remains with the
                                    groomer, including service quality and client care.</p>
                            </div>

                        </div>

                        <div class="tab-panel" id="tab-safety">
                            <h2 class="large-font">Safety </h2>
                            <h3 class="normal-font-weight" style="color:#9D9B98">View safety-related questions.</h3>
                            <!-- accordian -->
                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Are
                                groomers vetted before joining?</button>
                            <!-- <hr class="mt-4" > -->
                            <div class="panel">
                                <p class="normal-font-weight">All groomers must meet FursGo’s onboarding standards
                                    before being listed, helping maintain quality and trust across the platform.</p>
                            </div>

                            <button class="medium-font accordion " style="border-bottom: 1px solid #D4D4D4;">Do Space
                                Owners provide grooming services?</button>
                            <div class="panel">
                                <p class="normal-font-weight">Space Owners provide the physical space only. Grooming
                                    services are always delivered by independent professional groomers.</p>
                            </div>

                        </div>

                    </div>


                </div>
                <div class="col-lg-1"></div>
            </div>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <x-ui.contact-support />
        </div>
        <div class="col-lg-1"></div>

    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
@endpush

@push('script')
<script src="{{ asset('js/common.js') }}"></script>
<script>
    // Accordion: only one open at a time per tab panel
    document.querySelectorAll('.tab-panel').forEach((tabPanel) => {
        const accordions = tabPanel.querySelectorAll('.accordion');

        accordions.forEach((btn) => {
            btn.addEventListener('click', function() {
                const isActive = this.classList.contains('active');

                // close all accordions in this tab
                accordions.forEach((otherBtn) => {
                    otherBtn.classList.remove('active');
                    const otherPanel = otherBtn.nextElementSibling;
                    if (otherPanel && otherPanel.classList.contains('panel')) {
                        otherPanel.style.maxHeight = null;
                        otherPanel.style.display = 'none';
                    }
                });

                // open clicked accordion if it was not already open
                if (!isActive) {
                    this.classList.add('active');
                    const panel = this.nextElementSibling;
                    if (panel && panel.classList.contains('panel')) {
                        panel.style.display = 'block';
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    }
                }
            });
        });
    });

    // Optional: keep your file upload logic here if needed
    const fileInput = document.getElementById('fileInput');
    const attachBtn = document.getElementById('attachBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileItem = document.getElementById('fileItem');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeBtn = document.getElementById('removeBtn');

    if (attachBtn && uploadBtn && fileInput) {
        attachBtn.onclick = uploadBtn.onclick = () => fileInput.click();

        fileInput.onchange = () => {
            const file = fileInput.files[0];
            if (!file || !fileItem || !fileName || !fileSize) return;

            fileItem.style.display = 'flex';
            fileName.textContent = file.name;
            fileSize.textContent = `${Math.round(file.size / 1024)} KB • Uploading...`;

            setTimeout(() => {
                fileSize.textContent = `${Math.round(file.size / 1024)} KB of ${Math.round(file.size / 1024)} KB`;
            }, 1500);
        };

        if (removeBtn) {
            removeBtn.onclick = () => {
                fileInput.value = '';
                if (fileItem) fileItem.style.display = 'none';
            };
        }
    }
</script>
@endpush