@php
    $hero = $homepage['hero'] ?? [];
    $feature = $homepage['feature'] ?? [];
    $howItWorks = $homepage['how_it_works'] ?? [];
    $popularServices = $homepage['popular_services'] ?? [];
    $testimonials = $homepage['testimonials'] ?? [];
    $mission = $homepage['mission'] ?? [];
    $serviceMap = $homepage['service_map'] ?? [];

    $featureLines = preg_split('/\.\s*/', trim((string) ($feature['headline'] ?? ''), " \t\n\r\0\x0B."), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $featureLineClasses = ['groom', 'book', 'earn'];
    $featureDotClasses = ['dot1', 'dot2', 'dot3'];
@endphp
<!-- hero section -->
    <section>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="hero-section">
                        <div class="inverted-radius">
                            <img src="{{ $hero['photo'] ?? asset('images/hero_section.png') }}" alt="">
                        </div>
                        <div class="bottom-left">
                            <div class="find-grommer-content">
                                <div class="find-groomer">
                                    <p class="find-groomer-space-text active">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                            <path d="M4.10318 9.48141C5.14742 10.5256 7.68713 9.67928 9.7756 7.59053C11.8644 5.50211 12.7107 2.96245 11.6665 1.91824M7.17571 1.20895L7.64836 1.68192M5.52145 2.86351L5.9941 3.33615M4.10285 4.75439L4.57549 5.22702M3.6302 7.1179L4.10285 7.59053M9.7756 0.5L10.2482 0.972635M9.30295 3.33648L10.2482 4.28175M7.64869 4.99104L8.59398 5.93631M5.75778 6.40894L6.70307 7.35421" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4.10274 10.9001C4.49435 10.5085 4.49435 9.87361 4.10274 9.48201C3.71113 9.09041 3.0762 9.09041 2.68459 9.48201L0.793717 11.3728C0.402106 11.7644 0.402106 12.3994 0.793717 12.791C1.18533 13.1826 1.82026 13.1826 2.21187 12.791L4.10274 10.9001Z" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Find Groomer
                                    </p>
                                </div>
                                <div class="find-groomer-content-area">
                                    <form method="GET" action="{{ route('search-results') }}" data-home-search="groomer">
                                        <input type="hidden" name="mode" value="groomer">
                                        <input type="hidden" name="pet_type" value="">
                                        <input type="hidden" name="pet_size" value="">
                                        <div class="select-box-first-row">
                                            <div class="pet-type-wrapper">
                                                <p class="label">Select Pet Type</p>

                                                <div class="pet-toggle">
                                                    <button type="button" class="pet-option" data-pet="cat">
                                                        <span>Cat</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="23"
                                                            viewBox="0 0 16 23" fill="none">
                                                            <path
                                                                d="M7.06607 3.58301C7.8265 3.48922 8.87941 3.51287 9.88638 3.85352C10.9998 4.23021 12.076 5.00399 12.6022 6.43945L14.7711 7.47949L14.818 7.64941C15.1058 8.68692 15.2774 10.2987 14.8317 11.7656C14.6072 12.5047 14.2229 13.2153 13.611 13.791C12.9973 14.3682 12.1722 14.7931 11.0944 14.9854C7.21594 15.6769 5.01487 18.9931 4.40787 20.5596C4.16903 21.2436 3.62369 22.8966 3.62369 23C-3.55897 11.9396 1.57217 3.05801 5.0358 0L7.06607 3.58301ZM9.46841 7.20898C8.89932 7.20905 8.29568 7.49145 8.29556 8.62109C8.29556 9.40123 9.29039 8.62109 9.93814 8.62109C10.5855 8.6214 10.6413 9.40108 10.6413 8.62109C10.6411 7.84111 10.1161 7.20898 9.46841 7.20898Z" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" class="pet-option" data-pet="dog">
                                                        <span>Dog</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21"
                                                            viewBox="0 0 22 21" fill="none">
                                                            <path
                                                                d="M11.4592 0C12.0763 -1.81872e-05 12.6594 0.284475 13.0383 0.771484L16.2531 4.90625C16.4122 5.1107 16.6451 5.24555 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01307 20.759 6.35645C21.0768 7.32331 21.6368 9.33324 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5012 13.4996 14.6389 12.8358 12.4377 14.5137C11.758 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72483 21.7058 4.32932 20.2969L1.40646 18.5781L2.88596 12.9932C3.03734 12.9827 3.18545 12.9709 3.32639 12.9531C3.72903 12.9023 4.11589 12.815 4.36935 12.6543C4.5727 12.5253 4.78067 12.3019 4.97775 12.0498C5.17867 11.7928 5.3839 11.4855 5.57834 11.168C5.96741 10.5326 6.32411 9.84071 6.53439 9.39648C6.59333 9.27178 6.53985 9.1226 6.41525 9.06348C6.29076 9.00482 6.14244 9.05746 6.08322 9.18164C5.87884 9.61345 5.53038 10.2892 5.15256 10.9062C4.96359 11.2149 4.76927 11.5055 4.5842 11.7422C4.39523 11.9839 4.23009 12.1511 4.10178 12.2324C3.94892 12.3293 3.65905 12.4071 3.26389 12.457C2.87952 12.5055 2.43081 12.5234 1.98947 12.5225C1.58423 12.5216 1.18935 12.5013 0.862518 12.4795C0.852878 12.4768 0.842809 12.4744 0.833221 12.4717C0.259699 12.3089 -0.117574 11.6851 0.0334167 11.1084C1.50838 5.48346 2.34844 2.92212 3.76584 1.50488C5.26577 0.00544894 8.2594 1.93192e-05 8.28146 0H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47085 11.6506 6.59869 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72593 12.5754 5.01786 11.8508 5.01758Z" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" class="pet-option" data-pet="other">
                                                        <span>Other</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16"
                                                            viewBox="0 0 20 16" fill="none">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="pet-weight-wrapper">
                                                <p class="label">Select Pet Size</p>

                                                <div class="weight-toggle">
                                                    <button type="button" class="weight-option" data-weight="small">
                                                        <span>Small 0–7 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option medium"
                                                        data-weight="medium">
                                                        <span>Medium 8–18 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option large"
                                                        data-weight="large">
                                                        <span>Large 19+ kg</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="service-select-wrapper">
                                                <p class="label">Select Service Type</p>

                                                <!-- <div class="select-container">
                                                    <select id="serviceType" class="service-select">
                                                        <option value="" disabled selected hidden>
                                                            Full Grooming, Pet Spa ...
                                                        </option>
                                                        <option value="full-groom">Full Groom</option>
                                                        <option value="face-trim-only">Face Trim Only</option>
                                                        <option value="tail-trim-only">Tail Trim Only</option>
                                                        <option value="bath-and-wash">Bath & Wash</option>
                                                        <option value="nail-trim">Nail Trim</option>
                                                    </select>
                                                </div> -->
                                                <div class="custom-select">
                                                    <div class="select-trigger">
                                                        <span class="selected-text">Full Grooming, Pet Spa ...</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8"
                                                            viewBox="0 0 15 8" fill="none">
                                                            <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                stroke="#3B3731" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>

                                                    <ul class="select-options">
                                                        <li data-value="full-groom">Full Groom</li>
                                                        <li data-value="face-trim-only">Face Trim Only</li>
                                                        <li data-value="tail-trim-only">Tail Trim Only</li>
                                                        <li data-value="bath-and-wash">Bath & Brush</li>
                                                        <li data-value="nail-trim">Nail Trim</li>
                                                    </ul>

                                                    <input type="hidden" name="serviceType">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="select-box-second-row">
                                            <div class="search-box">
                                                <p class="label">Search Groomer</p>

                                                <input type="text" id="search-groomer" name="search"
                                                    placeholder="Search address, postcode, name ...">
                                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                                    height="16" fill="gray" viewBox="0 0 16 16">
                                                    <path
                                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                                </svg>
                                            </div>

                                            <div class="datetime-wrapper" id="datetime">
                                                <!-- Date field -->
                                                <div class="field-group">
                                                    <p class="label">Date</p>
                                                    <div class="field date" id="dateField">
                                                        <div class="input-row" tabindex="0" role="button"
                                                            aria-haspopup="dialog" aria-expanded="false">
                                                            <input class="fake-input" id="dateInput" name="date" readonly
                                                                placeholder="02 November 2025"
                                                                aria-label="Date input" />
                                                            <!-- chevron down svg -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                                height="8" viewBox="0 0 15 8" fill="none">
                                                                <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                    stroke="#3B3731" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </div>

                                                        <div class="popover" id="datePopover" data-type="date">
                                                            <div style="display:flex;">
                                                                <div class="panel calendar">
                                                                    <div class="month-nav">
                                                                        <button type="button" id="prevMonth"
                                                                            title="Previous month"
                                                                            aria-label="Previous month">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="6" height="10" viewBox="0 0 6 10"
                                                                                fill="none">
                                                                                <path
                                                                                    d="M4.56836 0.5L0.500066 4.56829L4.50007 8.56829"
                                                                                    stroke="#3B3731" stroke-width="1.5"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round" />
                                                                            </svg>
                                                                        </button>

                                                                        <div id="monthLabel">November 2025</div>

                                                                        <button type="button" id="nextMonth"
                                                                            title="Next month" aria-label="Next month">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                width="6" height="10" viewBox="0 0 6 10"
                                                                                fill="none">
                                                                                <path
                                                                                    d="M0.5 0.5L4.56829 4.56829L0.568294 8.56829"
                                                                                    stroke="#3B3731" stroke-width="1.5"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <div class="weekday-row" id="weekdayRow"></div>
                                                                    <div class="days-grid" id="daysGrid"></div>
                                                                </div>

                                                                <div class="time-col">
                                                                    <div class="title">
                                                                        <p class="time-text">Time</p>
                                                                    </div>
                                                                    <div class="time-list" id="timeList" role="listbox"
                                                                        aria-label="Time options"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Time field -->
                                                <div class="field-group">
                                                    <p class="label">Time</p>
                                                    <div class="field time" id="timeField">
                                                        <div class="input-row" tabindex="0" role="button"
                                                            aria-haspopup="dialog" aria-expanded="false">
                                                            <input class="fake-input" id="timeInput" readonly
                                                                placeholder="13:00" aria-label="Time input" />
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                                height="8" viewBox="0 0 15 8" fill="none">
                                                                <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                    stroke="#3B3731" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="submit-button">
                                                <button type="submit" class="form-submit-button">Search</button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="find-space-content">
                                <div class="find-space">
                                    <!-- <p>Find Space</p> -->
                                    <p class="find-groomer-space-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                            <path d="M13.6338 12.6012V3.98751C13.6338 3.96684 13.6355 3.94659 13.6387 3.92677L11.3098 1.9408C10.8146 1.51908 10.4724 1.22843 10.1821 1.03893C9.90186 0.856019 9.7135 0.797503 9.53333 0.797503C9.3533 0.797503 9.16612 0.856233 8.88614 1.03893C8.59582 1.22845 8.25275 1.51885 7.75684 1.9408L5.42632 3.92677C5.42953 3.94667 5.43286 3.96676 5.43286 3.98751V12.6012C5.43251 12.8211 5.24532 12.9999 5.01448 12.9999C4.78378 12.9998 4.59645 12.821 4.59609 12.6012V4.63393L4.16464 5.00308C3.99239 5.14987 3.72704 5.13454 3.57302 4.97037C3.41961 4.80631 3.43397 4.55476 3.60571 4.40807L7.19791 1.34734H7.19954C7.67887 0.939499 8.06705 0.607022 8.4122 0.381618C8.76777 0.149501 9.12077 2.33993e-07 9.53333 0C9.94583 0 10.2988 0.149492 10.6545 0.381618C10.9998 0.607077 11.3896 0.939348 11.8687 1.34734L15.461 4.40807C15.6327 4.55476 15.647 4.80631 15.4936 4.97037C15.3396 5.13454 15.0743 5.14987 14.902 5.00308L14.4706 4.63393V12.6012C14.4702 12.821 14.2829 12.9998 14.0522 12.9999C13.8213 12.9999 13.6342 12.8211 13.6338 12.6012Z" fill="#FDFCF8" />
                                            <path d="M1.89711 6.93401C1.89711 6.63323 1.81156 6.37517 1.68983 6.20094C1.56805 6.02682 1.42657 5.95363 1.29997 5.95363C1.17344 5.95374 1.03181 6.02694 0.910117 6.20094C0.788487 6.37516 0.702836 6.6334 0.702836 6.93401C0.702947 7.23473 0.788337 7.49296 0.910117 7.66708C1.03179 7.84098 1.17347 7.91281 1.29997 7.91291C1.42647 7.91291 1.56811 7.8409 1.68983 7.66708C1.81161 7.49296 1.897 7.23473 1.89711 6.93401ZM2.59994 6.93401C2.59983 7.38204 2.47407 7.80238 2.25264 8.119C2.03101 8.43588 1.69739 8.6666 1.29997 8.6666C0.902825 8.66649 0.570251 8.43562 0.348672 8.119C0.127221 7.80237 0.000110851 7.38206 0 6.93401C0 6.48573 0.127124 6.06432 0.348672 5.74755C0.570251 5.43102 0.902904 5.20006 1.29997 5.19995C1.69734 5.19995 2.03101 5.43073 2.25264 5.74755C2.47419 6.06432 2.59994 6.48573 2.59994 6.93401Z" fill="#FDFCF8" />
                                            <path d="M0.866821 12.5937V8.20615C0.866821 7.9818 1.06083 7.79993 1.30015 7.79993C1.53946 7.79993 1.73347 7.9818 1.73347 8.20615V12.5937C1.73329 12.8179 1.53935 12.9999 1.30015 12.9999C1.06094 12.9999 0.867004 12.8179 0.866821 12.5937Z" fill="#FDFCF8" />
                                            <path d="M11.084 9.68613C11.084 9.33228 11.0824 9.10762 11.0599 8.94297C11.039 8.79034 11.0071 8.75062 10.9878 8.73154C10.9684 8.71253 10.9283 8.67955 10.7729 8.65896C10.6054 8.6368 10.376 8.63687 10.016 8.63687H9.27833C8.91831 8.63687 8.68888 8.6368 8.52144 8.65896C8.36597 8.67955 8.32588 8.71253 8.30656 8.73154C8.28722 8.75062 8.25527 8.79034 8.2344 8.94297C8.21189 9.10762 8.21035 9.33228 8.21035 9.68613V12.1917H11.084V9.68613ZM10.3864 5.64688C10.6128 5.64705 10.7966 5.82811 10.7969 6.0508C10.7969 6.27378 10.613 6.45456 10.3864 6.45473H8.9079C8.68133 6.45456 8.49739 6.27378 8.49739 6.0508C8.49773 5.82811 8.68154 5.64705 8.9079 5.64688H10.3864ZM10.3864 3.46631L10.4682 3.4742C10.6556 3.51161 10.7969 3.67481 10.7969 3.87023C10.7969 4.06566 10.6556 4.22886 10.4682 4.26627L10.3864 4.27416H8.9079C8.68133 4.27399 8.49739 4.09321 8.49739 3.87023C8.49739 3.64726 8.68133 3.46648 8.9079 3.46631H10.3864ZM11.905 12.1917H15.1891C15.4159 12.1917 15.5997 12.3726 15.5997 12.5957C15.5993 12.8185 15.4156 12.9996 15.1891 12.9996H0.410517C0.184008 12.9996 0.000345759 12.8185 0 12.5957C0 12.3726 0.183795 12.1917 0.410517 12.1917H7.38931V9.68613C7.38931 9.35529 7.38828 9.06657 7.41978 8.83568C7.45297 8.59291 7.529 8.35435 7.72606 8.16037C7.92324 7.96635 8.1656 7.89167 8.4124 7.859C8.64727 7.82794 8.94165 7.82902 9.27833 7.82902H10.016C10.3527 7.82902 10.647 7.82794 10.8819 7.859C11.1287 7.89167 11.3711 7.96635 11.5682 8.16037C11.7653 8.35435 11.8413 8.59291 11.8745 8.83568C11.906 9.06657 11.905 9.35529 11.905 9.68613V12.1917Z" fill="#FDFCF8" />
                                        </svg>
                                        Find Space
                                    </p>
                                </div>
                                <div class="find-space-content-area">
                                    <form method="GET" action="{{ route('search-results') }}" data-home-search="space">
                                        <input type="hidden" name="mode" value="space">
                                        <input type="hidden" name="pet_type" value="">
                                        <input type="hidden" name="pet_size" value="">
                                        <div class="select-box-first-row">
                                            <div class="pet-type-wrapper">
                                                <p class="label">Select Pet Type</p>

                                                <div class="pet-toggle">
                                                    <button type="button" class="pet-option" data-pet="cat">
                                                        <span>Cat</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="23"
                                                            viewBox="0 0 16 23" fill="none">
                                                            <path
                                                                d="M7.06607 3.58301C7.8265 3.48922 8.87941 3.51287 9.88638 3.85352C10.9998 4.23021 12.076 5.00399 12.6022 6.43945L14.7711 7.47949L14.818 7.64941C15.1058 8.68692 15.2774 10.2987 14.8317 11.7656C14.6072 12.5047 14.2229 13.2153 13.611 13.791C12.9973 14.3682 12.1722 14.7931 11.0944 14.9854C7.21594 15.6769 5.01487 18.9931 4.40787 20.5596C4.16903 21.2436 3.62369 22.8966 3.62369 23C-3.55897 11.9396 1.57217 3.05801 5.0358 0L7.06607 3.58301ZM9.46841 7.20898C8.89932 7.20905 8.29568 7.49145 8.29556 8.62109C8.29556 9.40123 9.29039 8.62109 9.93814 8.62109C10.5855 8.6214 10.6413 9.40108 10.6413 8.62109C10.6411 7.84111 10.1161 7.20898 9.46841 7.20898Z"
                                                                fill="#D4D4D4" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" class="pet-option" data-pet="dog">
                                                        <span>Dog</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21"
                                                            viewBox="0 0 22 21" fill="none">
                                                            <path
                                                                d="M11.4592 0C12.0763 -1.81872e-05 12.6594 0.284475 13.0383 0.771484L16.2531 4.90625C16.4122 5.1107 16.6451 5.24555 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01307 20.759 6.35645C21.0768 7.32331 21.6368 9.33324 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5012 13.4996 14.6389 12.8358 12.4377 14.5137C11.758 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72483 21.7058 4.32932 20.2969L1.40646 18.5781L2.88596 12.9932C3.03734 12.9827 3.18545 12.9709 3.32639 12.9531C3.72903 12.9023 4.11589 12.815 4.36935 12.6543C4.5727 12.5253 4.78067 12.3019 4.97775 12.0498C5.17867 11.7928 5.3839 11.4855 5.57834 11.168C5.96741 10.5326 6.32411 9.84071 6.53439 9.39648C6.59333 9.27178 6.53985 9.1226 6.41525 9.06348C6.29076 9.00482 6.14244 9.05746 6.08322 9.18164C5.87884 9.61345 5.53038 10.2892 5.15256 10.9062C4.96359 11.2149 4.76927 11.5055 4.5842 11.7422C4.39523 11.9839 4.23009 12.1511 4.10178 12.2324C3.94892 12.3293 3.65905 12.4071 3.26389 12.457C2.87952 12.5055 2.43081 12.5234 1.98947 12.5225C1.58423 12.5216 1.18935 12.5013 0.862518 12.4795C0.852878 12.4768 0.842809 12.4744 0.833221 12.4717C0.259699 12.3089 -0.117574 11.6851 0.0334167 11.1084C1.50838 5.48346 2.34844 2.92212 3.76584 1.50488C5.26577 0.00544894 8.2594 1.93192e-05 8.28146 0H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47085 11.6506 6.59869 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72593 12.5754 5.01786 11.8508 5.01758Z"
                                                                fill="#D4D4D4" />
                                                        </svg>
                                                    </button>

                                                    <button type="button" class="pet-option" data-pet="other">
                                                        <span>Other</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16"
                                                            viewBox="0 0 20 16" fill="none">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z"
                                                                fill="white" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="pet-weight-wrapper">
                                                <p class="label">Select Pet Size</p>

                                                <div class="weight-toggle">
                                                    <button type="button" class="weight-option" data-weight="small">
                                                        <span>Small 0–7 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option medium"
                                                        data-weight="medium">
                                                        <span>Medium 8–18 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option large"
                                                        data-weight="large">
                                                        <span>Large 19+ kg</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="service-select-wrapper">
                                                <p class="label">Select Space Type</p>

                                                <div class="custom-select">
                                                    <div class="select-trigger">
                                                        <span class="selected-text">Private Rooms, Salon...</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8"
                                                            viewBox="0 0 15 8" fill="none">
                                                            <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                stroke="#3B3731" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>

                                                    <ul class="select-options">
                                                        <li data-value="private-rooms">Private Rooms</li>
                                                        <li data-value="salon">Salon</li>
                                                        <li data-value="mobile-station">Mobile Station</li>
                                                        <li data-value="garden/shed">Garden / Shed</li>
                                                        <li data-value="others">Others</li>
                                                    </ul>

                                                    <input type="hidden" name="serviceType">
                                                </div>

                                            </div>

                                        </div>
                                        <div class="select-box-second-row">
                                            <div class="search-box">
                                                <p class="label">Search Space</p>

                                                <input type="text" name="search" placeholder="Search address, postcode, name ...">
                                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                                    height="16" fill="gray" viewBox="0 0 16 16">
                                                    <path
                                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                                </svg>
                                            </div>

                                            <div class="datetime-wrapper" id="datetime">
                                                <!-- Date field -->
                                                <div class="field-group">
                                                    <p class="label">Date</p>
                                                    <div class="field date" id="dateField">
                                                        <div class="input-row" tabindex="0" role="button"
                                                            aria-haspopup="dialog" aria-expanded="false">
                                                            <input class="fake-input" id="dateInput" readonly
                                                                placeholder="02 November 2025"
                                                                aria-label="Date input" />
                                                            <!-- chevron down svg -->
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                                height="8" viewBox="0 0 15 8" fill="none">
                                                                <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                    stroke="#3B3731" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </div>

                                                        <div class="popover" id="datePopover" data-type="date">
                                                            <div style="display:flex;">
                                                                <div class="panel calendar">
                                                                    <div class="month-nav">
                                                                        <button type="button" id="prevMonth"
                                                                            title="Previous month"
                                                                            aria-label="Previous month">
                                                                            <svg class="chev rotate-left"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M6 9l6 6 6-6" stroke="#444"
                                                                                    stroke-width="1.6"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round" />
                                                                            </svg>
                                                                        </button>
                                                                        <div id="monthLabel">November 2025</div>
                                                                        <button type="button" id="nextMonth"
                                                                            title="Next month" aria-label="Next month">
                                                                            <svg class="chev rotate-right"
                                                                                viewBox="0 0 24 24" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M6 9l6 6 6-6" stroke="#444"
                                                                                    stroke-width="1.6"
                                                                                    stroke-linecap="round"
                                                                                    stroke-linejoin="round" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <div class="weekday-row" id="weekdayRow"></div>
                                                                    <div class="days-grid" id="daysGrid"></div>
                                                                </div>

                                                                <div class="time-col">
                                                                    <div class="title">
                                                                        <p class="time-text">Time</p>
                                                                    </div>
                                                                    <div class="time-list" id="timeList" role="listbox"
                                                                        aria-label="Time options"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Time field -->
                                                <div class="field-group">
                                                    <p class="label">Time</p>
                                                    <div class="field time" id="timeField">
                                                        <div class="input-row" tabindex="0" role="button"
                                                            aria-haspopup="dialog" aria-expanded="false">
                                                            <input class="fake-input" id="timeInput" readonly
                                                                placeholder="13:00" aria-label="Time input" />
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                                height="8" viewBox="0 0 15 8" fill="none">
                                                                <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                                                                    stroke="#3B3731" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="submit-button">
                                                <button type="submit" class="form-submit-button">Search</button>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="bottom-right">
                            <p class="hero-text">
                                <span class="book-text">Book</span> trusted <br>
                                pet grooming <br>
                                in minutes!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- rating section -->

    <!-- rating section -->
    <section class="rating-section section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="rating-text">
                        <div class="rating-stars">
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30"
                                fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30"
                                fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30"
                                fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30"
                                fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <!-- faint full star -->
                                <path d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z" fill="#3B3731" fill-opacity="0.125" />
                                <!-- colored left half -->
                                <path d="M9.25234 9.65772C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199C13.9931 -0.460632 16.5999 -0.460626 17.1986 1.382L19.7897 9.35646C19.8545 9.55606 19.8875 9.76462 19.8875 9.97449V27.1231L16.4721 24.6416C15.7711 24.1324 14.8219 24.1324 14.1209 24.6416L8.25674 28.9022C6.68931 30.041 4.58035 28.5088 5.17905 26.6662L7.41897 19.7724C7.68672 18.9484 7.3934 18.0456 6.69243 17.5363L0.82825 13.2758C-0.739177 12.137 0.0663681 9.65772 2.00382 9.65772H9.25234Z" fill="#FFC97A" />
                            </svg>
                        </div>
                        <h1>{{ $hero['rating_headline'] ?? '4.9 average rating from 5,000 + pet owners.' }}</h1>
                    </div>
                    <p class="rating-sub-text text-center pt-3">{{ $hero['rating_copy'] ?? 'All groomers are ID-verified and insured through FursGo Protect.' }}</p>
                </div>
            </div>
        </div>
    </section>
    <!-- rating section -->

    <section class="section">
        <div class="container">
            <div class="row d-flex">
                <div class="col-lg-4 ">
                    <h1 class="text-styling">
                        @forelse ($featureLines as $index => $line)
                            @php
                                $parts = preg_split('/\s+/', trim($line), 2);
                                $first = $parts[0] ?? '';
                                $rest = $parts[1] ?? '';
                                $wordClass = $featureLineClasses[$index] ?? 'groom';
                                $dotClass = $featureDotClasses[$index] ?? 'dot1';
                            @endphp
                            <span class="{{ $wordClass }}">{{ $first }}</span>@if ($rest !== '') {{ $rest }}@endif<span class="{{ $dotClass }}">.</span>
                            @if (! $loop->last)
                                <br>
                            @endif
                        @empty
                            <span class="groom">Groom</span> from anywhere<span class="dot1">.</span>
                            <br>
                            <span class="book">Book</span> from anywhere<span class="dot2">.</span>
                            <br>
                            <span class="earn">Earn</span> from anywhere<span class="dot3">.</span>
                        @endforelse
                    </h1>
                </div>
                <div class="col-lg-4 p-0 m-0">
                    <div class="img-fluid">
                        <img src="{{ $feature['photo'] ?? asset('images/dog.png') }}" alt="" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-4 align-items-center">
                    <div class="points">
                        @foreach (($feature['items'] ?? []) as $item)
                            <div class="point">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50"
                                    fill="none">
                                    <path
                                        d="M25 0C11.25 0 0 11.25 0 25C0 38.75 11.25 50 25 50C38.75 50 50 38.75 50 25C50 11.25 38.75 0 25 0ZM20 37.5L7.5 25L11.025 21.475L20 30.425L38.975 11.45L42.5 15L20 37.5Z"
                                        fill="#D8E8B7" />
                                </svg>
                                <div class="text">
                                    <h1>{{ $item['title'] }}</h1>
                                    <span class="mt-1">{{ $item['description'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="how-it-works section" id="how_it_works">
        <div class="container">
            <div class="row justify-content-center">

                <!-- Heading -->
                <div class="col-lg-12 text-center">
                    <h1>{{ $howItWorks['title'] ?? 'How it works' }}</h1>
                    <span>{{ $howItWorks['intro'] ?? '' }}</span>
                </div>

                <div class="how-it-works-grid section">
                    @foreach (($howItWorks['steps'] ?? []) as $index => $step)
                        <div class="how-step">
                            <div class="how-it-works-image-content-area">
                                <div class="{{ $index === 0 ? 'search-image' : ($index === 1 ? 'book-and-pay-content' : 'relax-content') }}">
                                    <img src="{{ $step['image'] ?? asset('images/search.png') }}" class="how-it-works-images" alt="">
                                </div>
                                <div class="numbering">
                                    <span>{{ $index + 1 }}</span>
                                </div>
                                <div class="content">
                                    <h2>{{ $step['title'] }}</h2>
                                    <span>{{ $step['description'] }}</span>
                                </div>
                            </div>
                        </div>

                        @if (! $loop->last)
                            @if ($index % 2 === 0)
                                <div class="how-up-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="171" height="26" viewBox="0 0 171 26"
                                        fill="none">
                                        <path d="M0.558105 0.851562C27.4847 18.9625 98.982 44.3179 169.558 0.851562"
                                            stroke="#3B3731" stroke-width="2" stroke-dasharray="10 10" />
                                    </svg>
                                </div>
                            @else
                                <div class="how-down-arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="171" height="26" viewBox="0 0 171 26"
                                        fill="none">
                                        <path d="M0.558105 24.9469C27.4847 6.83593 98.982 -18.5194 169.558 24.9469" stroke="#3B3731"
                                            stroke-width="2" stroke-dasharray="10 10" />
                                    </svg>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </section>
    <!-- How it works -->

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 ">
                    <div class="popular-section d-flex flex-column align-items-end align-items-xs-center">
                        <h1 class="popular-services-heading">{{ $popularServices['title'] ?? 'Popular Services' }}</h1>
                        <span>{{ $popularServices['intro'] ?? '' }}</span>
                    </div>
                </div>
            </div>
            <div class="popular-services-content-grid section">
                @foreach (($popularServices['items'] ?? []) as $service)
                    <div class="popular-content-area">
                        <div class="image-wrapper {{ $service['meta']['shape_class'] ?? 'oval' }}">
                            <img src="{{ $service['image'] ?? asset('images/popular-services-1.png') }}" alt="">
                        </div>
                        <div class="text-area">
                            <h2>{{ $service['title'] }}</h2>
                            <span>{{ $service['description'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="testimonial-section d-flex flex-column align-items-start">
                        <h1 class="testimonial-heading">Testimonials</h1>
                        <span class="testimonial-heading-text">
                            Hear from real pet owners who’ve used FursGo and found the care they were looking for.
                        </span>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="layout">
                        <div class="left">
                            <div class="quote">
                                <svg xmlns="http://www.w3.org/2000/svg" width="41" height="35" viewBox="0 0 41 35"
                                    fill="none">
                                    <path
                                        d="M10.0516 35C8.11183 35 6.52473 34.5833 5.29032 33.75C4.05591 32.9167 3.04193 31.8519 2.24839 30.5556C1.36667 28.9815 0.749462 27.3611 0.396774 25.6945C0.132258 23.9352 0 22.5 0 21.3889C0 16.8519 1.10215 12.7315 3.30645 9.02779C5.51075 5.32408 8.94946 2.31482 13.6226 0L14.8129 2.5C12.0796 3.70371 9.69893 5.60185 7.67097 8.19445C5.73118 10.787 4.76129 13.4259 4.76129 16.1111C4.76129 17.2222 4.89355 18.1944 5.15806 19.0278C6.56882 17.8241 8.2 17.2222 10.0516 17.2222C12.3441 17.2222 14.328 18.0093 16.0032 19.5833C17.6785 21.1574 18.5161 23.3333 18.5161 26.1111C18.5161 28.7037 17.6785 30.8333 16.0032 32.5C14.328 34.1667 12.3441 35 10.0516 35ZM32.5355 35C30.5957 35 29.0086 34.5833 27.7742 33.75C26.5398 32.9167 25.5258 31.8519 24.7323 30.5556C23.8505 28.9815 23.2333 27.3611 22.8806 25.6945C22.6161 23.9352 22.4839 22.5 22.4839 21.3889C22.4839 16.8519 23.586 12.7315 25.7903 9.02779C27.9946 5.32408 31.4333 2.31482 36.1064 0L37.2968 2.5C34.5634 3.70371 32.1828 5.60185 30.1548 8.19445C28.2151 10.787 27.2452 13.4259 27.2452 16.1111C27.2452 17.2222 27.3774 18.1944 27.6419 19.0278C29.0527 17.8241 30.6839 17.2222 32.5355 17.2222C34.828 17.2222 36.8118 18.0093 38.4871 19.5833C40.1624 21.1574 41 23.3333 41 26.1111C41 28.7037 40.1624 30.8333 38.4871 32.5C36.8118 34.1667 34.828 35 32.5355 35Z"
                                        fill="#3B3731" />
                                </svg>
                            </div>
                            <br>
                            <h3 class="testimonial-sub-heading">{{ $testimonials['intro'] ?? 'Loved by Pet Parents' }}</h3>
                            <p>{!! nl2br(e($testimonials['desc'] ?? '')) !!}</p>

                            <!-- arrows + dots BELOW text (left column) -->
                            <div class="controls left-controls">
                                <div class="dots" id="dots"></div>

                                <div class="control-buttons">
                                    <button id="prev" class="circle-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 40 40" fill="none">
                                            <g filter="url(#filter_prev)">
                                                <circle cx="20" cy="16" r="16" fill="white" />
                                            </g>
                                            <path d="M22 21L16.9657 15.9657L21.9155 11.016" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <defs>
                                                <filter id="filter_prev" x="0" y="0" width="40" height="40"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                        result="hardAlpha" />
                                                    <feOffset dy="4" />
                                                    <feGaussianBlur stdDeviation="2" />
                                                    <feComposite in2="hardAlpha" operator="out" />
                                                    <feColorMatrix type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                        result="effect1_dropShadow" />
                                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                        result="shape" />
                                                </filter>
                                            </defs>
                                        </svg>
                                    </button>

                                    <button id="next" class="circle-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                            viewBox="0 0 40 40" fill="none">
                                            <g filter="url(#filter_next)">
                                                <circle cx="20" cy="16" r="16" fill="white" />
                                            </g>
                                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <defs>
                                                <filter id="filter_next" x="0" y="0" width="40" height="40"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                        result="hardAlpha" />
                                                    <feOffset dy="4" />
                                                    <feGaussianBlur stdDeviation="2" />
                                                    <feComposite in2="hardAlpha" operator="out" />
                                                    <feColorMatrix type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                        result="effect1_dropShadow" />
                                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                        result="shape" />
                                                </filter>
                                            </defs>
                                        </svg>
                                    </button>
                                </div>

                            </div>
                        </div>

                        <div class="slider-wrap">
                            <div class="viewport" id="viewport">
                                <div class="ending-quotes">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="35" viewBox="0 0 41 35"
                                        fill="none">
                                        <path
                                            d="M30.9484 0C32.8882 0 34.4753 0.416664 35.7097 1.25C36.9441 2.08333 37.9581 3.14815 38.7516 4.44444C39.6333 6.01852 40.2505 7.63889 40.6032 9.30555C40.8677 11.0648 41 12.5 41 13.6111C41 18.1481 39.8978 22.2685 37.6935 25.9722C35.4892 29.6759 32.0505 32.6852 27.3774 35L26.1871 32.5C28.9204 31.2963 31.3011 29.3981 33.329 26.8056C35.2688 24.213 36.2387 21.5741 36.2387 18.8889C36.2387 17.7778 36.1065 16.8056 35.8419 15.9722C34.4312 17.1759 32.8 17.7778 30.9484 17.7778C28.6559 17.7778 26.672 16.9907 24.9968 15.4167C23.3215 13.8426 22.4839 11.6667 22.4839 8.88889C22.4839 6.29629 23.3215 4.16666 24.9968 2.5C26.672 0.833328 28.6559 0 30.9484 0ZM8.46452 0C10.4043 0 11.9914 0.416664 13.2258 1.25C14.4602 2.08333 15.4742 3.14815 16.2677 4.44444C17.1495 6.01852 17.7667 7.63889 18.1194 9.30555C18.3839 11.0648 18.5161 12.5 18.5161 13.6111C18.5161 18.1481 17.414 22.2685 15.2097 25.9722C13.0054 29.6759 9.56667 32.6852 4.89355 35L3.70322 32.5C6.43656 31.2963 8.8172 29.3981 10.8452 26.8056C12.7849 24.213 13.7548 21.5741 13.7548 18.8889C13.7548 17.7778 13.6226 16.8056 13.3581 15.9722C11.9473 17.1759 10.3161 17.7778 8.46452 17.7778C6.17204 17.7778 4.18817 16.9907 2.5129 15.4167C0.837631 13.8426 0 11.6667 0 8.88889C0 6.29629 0.837631 4.16666 2.5129 2.5C4.18817 0.833328 6.17204 0 8.46452 0Z"
                                            fill="#3B3731" />
                                    </svg>
                                </div>
                                <div class="track" id="track">
                                    @foreach (($testimonials['items'] ?? []) as $index => $item)
                                        <div class="card{{ $index === 0 ? ' active default' : '' }}">
                                            <div class="svg-div">
                                                @if ($index === 0)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="card-shape" width="400"
                                                        height="257" viewBox="0 0 400 257" fill="none">
                                                        <path
                                                            d="M10 0.5H390C395.247 0.5 399.5 4.75329 399.5 10V198C399.5 203.247 395.247 207.5 390 207.5H219C213.201 207.5 208.5 212.201 208.5 218V247C208.5 252.247 204.247 256.5 199 256.5H10C4.7533 256.5 0.5 252.247 0.5 247V10C0.500001 4.7533 4.7533 0.5 10 0.5Z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="card-shape" width="296"
                                                        height="257" fill="none">
                                                        <path
                                                            d="M10 0.5H286C291.247 0.5 295.5 4.75329 295.5 10V198C295.5 203.247 291.247 207.5 286 207.5H164.66C158.861 207.5 154.16 212.201 154.16 218V247C154.16 252.247 149.907 256.5 144.66 256.5H10C4.7533 256.5 0.5 252.247 0.5 247V10C0.500001 4.7533 4.7533 0.5 10 0.5Z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="text-stars">
                                                <p>{{ $item['description'] }}</p>
                                                <div class="stars" aria-label="{{ (int) ($item['meta']['rating'] ?? 5) }} out of 5 stars">
                                                    @for ($star = 1; $star <= (int) ($item['meta']['rating'] ?? 5); $star++)
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none" aria-hidden="true">
                                                            <path d="M8.58756 0.802006C8.92671 -0.267338 10.4034 -0.267334 10.7426 0.80201L12.0114 4.80273C12.1631 5.28095 12.5981 5.60473 13.0889 5.60473H17.195C18.2925 5.60473 18.7488 7.04353 17.8609 7.70442L14.539 10.177C14.1419 10.4726 13.9758 10.9964 14.1275 11.4747L15.3963 15.4754C15.7355 16.5447 14.5408 17.434 13.6529 16.7731L10.331 14.3005C9.9339 14.0049 9.39621 14.0049 8.99913 14.3005L5.67723 16.7731C4.78932 17.434 3.59466 16.5447 3.9338 15.4754L5.20266 11.4747C5.35433 10.9964 5.18817 10.4726 4.79109 10.177L1.46919 7.70442C0.581283 7.04353 1.03761 5.60473 2.13512 5.60473H6.24122C6.73204 5.60473 7.16704 5.28095 7.31871 4.80273L8.58756 0.802006Z" fill="#FFC97A" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            @if (! empty($item['image']))
                                                <div class="testimonial-photo"><img src="{{ $item['image'] }}" alt=""></div>
                                            @endif
                                            <div class="{{ $index === 0 ? 'avatar-meta' : '' }}">
                                                <div class="avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                                                        viewBox="0 0 35 35" fill="none">
                                                        <circle cx="17.5" cy="17.5" r="17.5" fill="#FFC97A" />
                                                    </svg>
                                                </div>
                                                <div class="meta">
                                                    <p>{{ $item['title'] }} <br> <span>{{ $item['meta']['date_label'] ?? '' }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>

    <!-- Our Mission  -->
    <section class="section">
        <div class="container">
            <div class="our-mission row d-flex justify-content-center align-items-center">
                <div class="col-lg-7">
                    <div class="image-wrapper">
                        <img src="{{ $mission['photo'] ?? asset('images/our_mission.png') }}" alt="" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-5 content-column">
                    <h1 class="main-heading">{{ $mission['title'] ?? 'Our Mission' }}</h1>
                    <p class="description">{{ $mission['desc'] ?? '' }}</p>
                    <a href="{{ $mission['cta_url'] ?? route('profile_pets_preferences.about_us') }}" wire:navigate class="action-button">{{ $mission['cta'] ?? 'About Us' }}</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Our Mission  -->

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-1 d-flex justify-content-lg-center">
                    <div class="location-svg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="33" height="46" viewBox="0 0 33 46" fill="none">
                            <path
                                d="M16.5 21.85C14.9371 21.85 13.4382 21.2442 12.3331 20.1659C11.228 19.0875 10.6071 17.625 10.6071 16.1C10.6071 14.575 11.228 13.1125 12.3331 12.0341C13.4382 10.9558 14.9371 10.35 16.5 10.35C18.0629 10.35 19.5618 10.9558 20.6669 12.0341C21.772 13.1125 22.3929 14.575 22.3929 16.1C22.3929 16.8551 22.2404 17.6028 21.9443 18.3004C21.6481 18.9981 21.2141 19.6319 20.6669 20.1659C20.1197 20.6998 19.4701 21.1233 18.7551 21.4123C18.0401 21.7013 17.2739 21.85 16.5 21.85ZM16.5 0C12.1239 0 7.92709 1.69624 4.83274 4.71558C1.73839 7.73492 0 11.83 0 16.1C0 28.175 16.5 46 16.5 46C16.5 46 33 28.175 33 16.1C33 11.83 31.2616 7.73492 28.1673 4.71558C25.0729 1.69624 20.8761 0 16.5 0Z"
                                fill="#FFC97A" />
                        </svg>
                    </div>
                </div>
                <div class="col-lg-11 mt-xs-5">
                    <div class="fursgo-address-map">
                        <h1 class="fursgo-address-heading">{{ $serviceMap['title'] ?? 'FursGo across the UK' }}</h1>
                        <p class="fursgo-sub-heading">{{ $serviceMap['desc'] ?? '' }}</p>
                        <ul class="fursgo-locations">
                            @foreach (($serviceMap['cities'] ?? []) as $city)
                                <li>{{ $city['title'] }}</li>
                            @endforeach
                        </ul>
                        <p class="link">More Coming soon.</p>
                        <div class="fursgo-map-image-wrapper">
                            <img src="{{ $serviceMap['image'] ?? asset('images/Intersect.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
