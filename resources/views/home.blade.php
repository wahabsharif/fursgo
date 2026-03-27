@extends('layouts.app')

@section('content')

    <!-- hero section -->
    <section>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="hero-section">
                        <div class="inverted-radius">
                            <img src="{{ asset('images/hero_section.png') }}" alt="Hero Image">
                        </div>
                        <div class="bottom-left">
                            <div class="find-grommer-content">
                                <div class="find-groomer active">
                                    <p>Find Groomer</p>
                                </div>
                                <div class="find-groomer-content-area">
                                    <form action="">
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

                                                    <button type="button" class="pet-option highlight" data-pet="other">
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

                                                    <button type="button" class="weight-option medium" data-weight="medium">
                                                        <span>Medium 8–18 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option large active"
                                                        data-weight="large">
                                                        <span>Large 19+ kg</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="service-select-wrapper">
                                                <p class="label">Select Service Type</p>
                                                <div class="custom-select">
                                                    <div class="select-trigger">
                                                        <span class="selected-text">Full Groom, Face Trim ...</span>
                                                        <svg width="16" height="16" viewBox="0 0 24 24">
                                                            <path d="M6 9l6 6 6-6" fill="none" stroke="#666"
                                                                stroke-width="2" />
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

                                                <input type="text" id="search-groomer"
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
                                                            <input class="fake-input" id="dateInput" readonly
                                                                placeholder="02 November 2025" aria-label="Date input" />
                                                            <!-- chevron down svg -->
                                                            <svg class="chev" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
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
                                                                        <div>Time</div>
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
                                                            <svg class="chev" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
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
                                    <p>Find Space</p>
                                </div>
                                <div class="find-space-content-area">
                                    <form action="">
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

                                                    <button type="button" class="pet-option highlight" data-pet="other">
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

                                                    <button type="button" class="weight-option medium" data-weight="medium">
                                                        <span>Medium 8–18 kg</span>
                                                    </button>

                                                    <button type="button" class="weight-option large active"
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
                                                        <svg width="16" height="16" viewBox="0 0 24 24">
                                                            <path d="M6 9l6 6 6-6" fill="none" stroke="#666"
                                                                stroke-width="2" />
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

                                                <input type="text" placeholder="Search address, postcode, name ...">
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
                                                                placeholder="02 November 2025" aria-label="Date input" />
                                                            <!-- chevron down svg -->
                                                            <svg class="chev" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
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
                                                                        <div>Time</div>
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
                                                            <svg class="chev" viewBox="0 0 24 24" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
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
    <section class="rating-section section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="rating-text">
                        <div class="rating-stars">
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="30" viewBox="0 0 31 30" fill="none">
                                <path
                                    d="M13.3944 1.38199C13.9931 -0.460633 16.5999 -0.460628 17.1986 1.38199L19.4385 8.27576C19.7063 9.0998 20.4742 9.65772 21.3407 9.65772H28.5892C30.5266 9.65772 31.3322 12.137 29.7647 13.2758L23.9006 17.5363C23.1996 18.0456 22.9063 18.9484 23.174 19.7724L25.4139 26.6662C26.0126 28.5088 23.9037 30.041 22.3363 28.9022L16.4721 24.6417C15.7711 24.1324 14.8219 24.1324 14.1209 24.6417L8.25675 28.9022C6.68932 30.041 4.58036 28.5088 5.17907 26.6662L7.41899 19.7724C7.68674 18.9484 7.39342 18.0456 6.69244 17.5363L0.828256 13.2758C-0.739171 12.1369 0.0663853 9.65772 2.00383 9.65772H9.25236C10.1188 9.65772 10.8867 9.0998 11.1545 8.27575L13.3944 1.38199Z"
                                    fill="#FFC97A" />
                            </svg>
                        </div>
                        <h1>4.9 average rating from 5,000 + pet owners.</h1>
                    </div>
                    <p class="rating-sub-text text-center pt-3">All groomers are ID-verified and insured through FursGo
                        Protect.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row d-flex">
                <div class="col-lg-4 p-0 m-0">
                    <h1 class="text-styling">
                        <span class="groom">Groom</span> from anywhere<span class="dot1">.</span>
                        <br>
                        <span class="book">Book</span> from anywhere<span class="dot2">.</span>
                        <br>
                        <span class="earn">Earn</span> from anywhere<span class="dot3">.</span>
                    </h1>
                </div>
                <div class="col-lg-4 p-0 m-0">
                    <div class="img-fluid">
                        <img src="{{ asset('images/dog.png') }}" alt="" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-4 align-items-center">
                    <div class="points">
                        <div class="point">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                <path
                                    d="M25 0C11.25 0 0 11.25 0 25C0 38.75 11.25 50 25 50C38.75 50 50 38.75 50 25C50 11.25 38.75 0 25 0ZM20 37.5L7.5 25L11.025 21.475L20 30.425L38.975 11.45L42.5 15L20 37.5Z"
                                    fill="#D8E8B7" />
                            </svg>
                            <div class="text">
                                <h1>Booked in Minutes</h1>
                                <span>No calls, no quotes.</span>
                            </div>
                        </div>
                        <div class="point">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                <path
                                    d="M25 0C11.25 0 0 11.25 0 25C0 38.75 11.25 50 25 50C38.75 50 50 38.75 50 25C50 11.25 38.75 0 25 0ZM20 37.5L7.5 25L11.025 21.475L20 30.425L38.975 11.45L42.5 15L20 37.5Z"
                                    fill="#D8E8B7" />
                            </svg>
                            <div class="text">
                                <h1>Trusted Professionals</h1>
                                <span>Verified & insured.</span>
                            </div>
                        </div>
                        <div class="point">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50" fill="none">
                                <path
                                    d="M25 0C11.25 0 0 11.25 0 25C0 38.75 11.25 50 25 50C38.75 50 50 38.75 50 25C50 11.25 38.75 0 25 0ZM20 37.5L7.5 25L11.025 21.475L20 30.425L38.975 11.45L42.5 15L20 37.5Z"
                                    fill="#D8E8B7" />
                            </svg>
                            <div class="text">
                                <h1>Happy Pets</h1>
                                <span>Gentle grooming, stress-free service.</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works section">
        <div class="container">
            <div class="row justify-content-center">

                <!-- Heading -->
                <div class="col-lg-12 text-center">
                    <h1>How it works</h1>
                    <span>
                        Finding the right groomer shouldn’t be overwhelming — and with FursGo, it isn’t. <br>
                        We give you clear profiles and real availability, so you can confidently choose the <br>
                        groomer who’s the right fit for your pet’s needs.
                    </span>
                </div>

                <div class="how-it-works-grid section">

                    <!-- Step 1 -->
                    <div class="how-step">
                        <div class="how-it-works-image-content-area">
                            <div class="search-image">
                                <img src="{{ asset('images/search.png') }}" class="how-it-works-images" alt="">
                            </div>
                            <div class="numbering">
                                <span>1</span>
                            </div>
                            <div class="content">
                                <h2>Search</h2>
                                <span>
                                    No more guessing or endless calling around. We help you find the right groomer with
                                    total confidence.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="how-up-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="171" height="26" viewBox="0 0 171 26" fill="none">
                            <path d="M0.558105 0.851562C27.4847 18.9625 98.982 44.3179 169.558 0.851562" stroke="#3B3731"
                                stroke-width="2" stroke-dasharray="10 10" />
                        </svg>
                    </div>

                    <!-- Step 2 -->
                    <div class="how-step">
                        <div class="how-it-works-image-content-area">
                            <div class="book-and-pay-content">
                                <img src="{{ asset('images/book_and_pay.png') }}" class="how-it-works-images" alt="">
                            </div>
                            <div class="numbering">
                                <span>2</span>
                            </div>
                            <div class="content">
                                <h2>Book & Pay</h2>
                                <span>
                                    Simple, secure, and built to save you time. Book in seconds, pay safely, and manage
                                    everything in one place.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="how-down-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="171" height="26" viewBox="0 0 171 26" fill="none">
                            <path d="M0.558105 24.9469C27.4847 6.83593 98.982 -18.5194 169.558 24.9469" stroke="#3B3731"
                                stroke-width="2" stroke-dasharray="10 10" />
                        </svg>
                    </div>

                    <!-- Step 3 -->
                    <div class="how-step">
                        <div class="how-it-works-image-content-area">
                            <div class="relax-content">
                                <img src="{{ asset('images/relax.png') }}" class="how-it-works-images" alt="">
                            </div>
                            <div class="numbering">
                                <span>3</span>
                            </div>
                            <div class="content">
                                <h2>Relax</h2>
                                <span>
                                    Calm, clear, and stress-free appointments so your pet feels comfortable and you stay
                                    informed.
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 ">
                    <div class="popular-section d-flex flex-column align-items-end align-items-xs-center">
                        <h1 class="popular-services-heading">Popular Services</h1>
                        <span>
                            Our most trusted services — chosen by owners who want comfort,
                            reliability, and peace of mind.
                        </span>
                    </div>
                </div>
            </div>
            <div class="popular-services-content-grid section">
                <div class="popular-content-area">
                    <div class="image-wrapper oval">
                        <img src="{{ asset('images/popular-services-1.png') }}" alt="">
                    </div>
                    <div class="text-area">
                        <h2>Full Groom</h2>
                        <span>Bath, trim, blow-dry, <br> and essential hygiene.</span>
                    </div>
                </div>
                <div class="popular-content-area">
                    <div class="image-wrapper circle">
                        <img src="{{ asset('images/popular-services-2.png') }}" alt="">
                    </div>
                    <div class="text-area">
                        <h2>Bath & Tidy</h2>
                        <span>A quick refresh to keep your <br> pet clean between full grooms.</span>
                    </div>
                </div>
                <div class="popular-content-area">
                    <div class="image-wrapper rectangle">
                        <img src="{{ asset('images/popular-services-3.png') }}" alt="">
                    </div>
                    <div class="text-area">
                        <h2>Nail Trim</h2>
                        <span>Quality and careful nail care <br> focused on comfort and safety.</span>
                    </div>
                </div>
                <div class="popular-content-area">
                    <div class="image-wrapper oval">
                        <img src="{{ asset('images/popular-services-4.png') }}" alt="">
                    </div>
                    <div class="text-area">
                        <h2>Pet Spa</h2>
                        <span>Soothing coat and skin <br> treatments to nourish & refresh.</span>
                    </div>
                </div>
                <div class="popular-content-area">
                    <div class="image-wrapper circle">
                        <img src="{{ asset('images/popular-services-5.png') }}" alt="">
                    </div>
                    <div class="text-area">
                        <h2>Mobile Grooming</h2>
                        <span>Grooming at home, for a <br> stress-free experience.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                                        fill="#3B3731"></path>
                                </svg>
                            </div>
                            <br>
                            <h3 class="testimonial-sub-heading">Loved by Pet Parents</h3>
                            <p>Your pet’s safety comes first. <br> Every groomer is background- <br> checked, insured,
                                and trained
                                in <br> animal
                                handling.</p>

                            <!-- arrows + dots BELOW text (left column) -->
                            <div class="controls left-controls">
                                <div class="dots" id="dots">
                                    <div class="dot active"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                </div>

                                <div class="control-buttons">
                                    <button id="prev" class="circle-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"
                                            fill="none">
                                            <g filter="url(#filter_prev)">
                                                <circle cx="20" cy="16" r="16" fill="white"></circle>
                                            </g>
                                            <path d="M22 21L16.9657 15.9657L21.9155 11.016" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                            <defs>
                                                <filter id="filter_prev" x="0" y="0" width="40" height="40"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                    <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                        result="hardAlpha"></feColorMatrix>
                                                    <feOffset dy="4"></feOffset>
                                                    <feGaussianBlur stdDeviation="2"></feGaussianBlur>
                                                    <feComposite in2="hardAlpha" operator="out"></feComposite>
                                                    <feColorMatrix type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"></feColorMatrix>
                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                        result="effect1_dropShadow"></feBlend>
                                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                        result="shape"></feBlend>
                                                </filter>
                                            </defs>
                                        </svg>
                                    </button>

                                    <button id="next" class="circle-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40"
                                            fill="none">
                                            <g filter="url(#filter_next)">
                                                <circle cx="20" cy="16" r="16" fill="white"></circle>
                                            </g>
                                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round"></path>
                                            <defs>
                                                <filter id="filter_next" x="0" y="0" width="40" height="40"
                                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                    <feFlood flood-opacity="0" result="BackgroundImageFix"></feFlood>
                                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                        result="hardAlpha"></feColorMatrix>
                                                    <feOffset dy="4"></feOffset>
                                                    <feGaussianBlur stdDeviation="2"></feGaussianBlur>
                                                    <feComposite in2="hardAlpha" operator="out"></feComposite>
                                                    <feColorMatrix type="matrix"
                                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"></feColorMatrix>
                                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                                        result="effect1_dropShadow"></feBlend>
                                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                        result="shape"></feBlend>
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
                                            fill="#3B3731"></path>
                                    </svg>
                                </div>
                                <div class="track" id="track" style="transition: none; transform: translateX(0px);">
                                    <div class="card active default">
                                        <div class="svg-div">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="card-shape" width="400"
                                                height="257" viewBox="0 0 400 257" fill="none">
                                                <path
                                                    d="M10 0.5H390C395.247 0.5 399.5 4.75329 399.5 10V198C399.5 203.247 395.247 207.5 390 207.5H219C213.201 207.5 208.5 212.201 208.5 218V247C208.5 252.247 204.247 256.5 199 256.5H10C4.7533 256.5 0.5 252.247 0.5 247V10C0.500001 4.7533 4.7533 0.5 10 0.5Z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="text-stars">
                                            <p>The easiest booking I’ve ever made! My dog looked amazing and the groomer
                                                was so
                                                kind.
                                            </p>
                                            <div class="stars">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="110" height="17"
                                                    viewBox="0 0 110 17" fill="none">
                                                    <path
                                                        d="M7.58756 0.802006C7.92671 -0.267338 9.40341 -0.267334 9.74255 0.80201L11.0114 4.80273C11.1631 5.28095 11.5981 5.60473 12.0889 5.60473H16.195C17.2925 5.60473 17.7488 7.04353 16.8609 7.70442L13.539 10.177C13.1419 10.4726 12.9758 10.9964 13.1275 11.4747L14.3963 15.4754C14.7355 16.5447 13.5408 17.434 12.6529 16.7731L9.33099 14.3005C8.9339 14.0049 8.39621 14.0049 7.99913 14.3005L4.67723 16.7731C3.78932 17.434 2.59466 16.5447 2.9338 15.4754L4.20266 11.4747C4.35433 10.9964 4.18817 10.4726 3.79109 10.177L0.469188 7.70442C-0.418717 7.04353 0.0376084 5.60473 1.13512 5.60473H5.24122C5.73204 5.60473 6.16704 5.28095 6.31871 4.80273L7.58756 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M53.9225 0.802006C54.2617 -0.267338 55.7384 -0.267334 56.0775 0.80201L57.3464 4.80273C57.498 5.28095 57.933 5.60473 58.4239 5.60473H62.53C63.6275 5.60473 64.0838 7.04353 63.1959 7.70442L59.874 10.177C59.4769 10.4726 59.3107 10.9964 59.4624 11.4747L60.7313 15.4754C61.0704 16.5447 59.8758 17.434 58.9878 16.7731L55.6659 14.3005C55.2689 14.0049 54.7312 14.0049 54.3341 14.3005L51.0122 16.7731C50.1243 17.434 48.9296 16.5447 49.2688 15.4754L50.5376 11.4747C50.6893 10.9964 50.5231 10.4726 50.126 10.177L46.8041 7.70442C45.9162 7.04353 46.3726 5.60473 47.4701 5.60473H51.5762C52.067 5.60473 52.502 5.28095 52.6537 4.80273L53.9225 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M30.755 0.802006C31.0942 -0.267338 32.5709 -0.267334 32.91 0.80201L34.1789 4.80273C34.3306 5.28095 34.7656 5.60473 35.2564 5.60473H39.3625C40.46 5.60473 40.9163 7.04353 40.0284 7.70442L36.7065 10.177C36.3094 10.4726 36.1433 10.9964 36.2949 11.4747L37.5638 15.4754C37.9029 16.5447 36.7083 17.434 35.8204 16.7731L32.4985 14.3005C32.1014 14.0049 31.5637 14.0049 31.1666 14.3005L27.8447 16.7731C26.9568 17.434 25.7621 16.5447 26.1013 15.4754L27.3701 11.4747C27.5218 10.9964 27.3557 10.4726 26.9586 10.177L23.6367 7.70442C22.7488 7.04353 23.2051 5.60473 24.3026 5.60473H28.4087C28.8995 5.60473 29.3345 5.28095 29.4862 4.80273L30.755 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M77.09 0.802006C77.4291 -0.267338 78.9058 -0.267334 79.245 0.80201L80.5138 4.80273C80.6655 5.28095 81.1005 5.60473 81.5913 5.60473H85.6974C86.7949 5.60473 87.2512 7.04353 86.3633 7.70442L83.0414 10.177C82.6443 10.4726 82.4782 10.9964 82.6299 11.4747L83.8987 15.4754C84.2379 16.5447 83.0432 17.434 82.1553 16.7731L78.8334 14.3005C78.4363 14.0049 77.8986 14.0049 77.5015 14.3005L74.1796 16.7731C73.2917 17.434 72.0971 16.5447 72.4362 15.4754L73.7051 11.4747C73.8567 10.9964 73.6906 10.4726 73.2935 10.177L69.9716 7.70442C69.0837 7.04353 69.54 5.60473 70.6375 5.60473H74.7436C75.2344 5.60473 75.6694 5.28095 75.8211 4.80273L77.09 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M100.257 0.802006C100.597 -0.267338 102.073 -0.267334 102.412 0.80201L103.681 4.80273C103.833 5.28095 104.268 5.60473 104.759 5.60473H108.865C109.962 5.60473 110.419 7.04353 109.531 7.70442L106.209 10.177C105.812 10.4726 105.646 10.9964 105.797 11.4747L107.066 15.4754C107.405 16.5447 106.211 17.434 105.323 16.7731L102.001 14.3005C101.604 14.0049 101.066 14.0049 100.669 14.3005L97.3471 16.7731C96.4592 17.434 95.2645 16.5447 95.6037 15.4754L96.8725 11.4747C97.0242 10.9964 96.858 10.4726 96.461 10.177L93.1391 7.70442C92.2512 7.04353 92.7075 5.60473 93.805 5.60473H97.9111C98.4019 5.60473 98.8369 5.28095 98.9886 4.80273L100.257 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="testimonial-photo"><img
                                                src="https://dev.zeeteck.com/projects/fursgo//assets/images/testimonial-1.png">
                                        </div>
                                        <div class="avatar-meta">
                                            <div class="avatar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                                                    viewBox="0 0 35 35" fill="none">
                                                    <circle cx="17.5" cy="17.5" r="17.5" fill="#FFC97A"></circle>
                                                </svg>
                                            </div>
                                            <div class="meta">
                                                <p>Sarah M. <br> <span>1 day ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="svg-div">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="card-shape" width="296"
                                                height="257" fill="none">
                                                <path
                                                    d="M10 0.5H286C291.247 0.5 295.5 4.75329 295.5 10V198C295.5 203.247 291.247 207.5 286 207.5H164.66C158.861 207.5 154.16 212.201 154.16 218V247C154.16 252.247 149.907 256.5 144.66 256.5H10C4.7533 256.5 0.5 252.247 0.5 247V10C0.500001 4.7533 4.7533 0.5 10 0.5Z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="text-stars">
                                            <p>
                                                Five stars every time. Reliable and professional.
                                            </p>
                                            <div class="stars">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="110" height="17"
                                                    viewBox="0 0 110 17" fill="none">
                                                    <path
                                                        d="M7.58756 0.802006C7.92671 -0.267338 9.40341 -0.267334 9.74255 0.80201L11.0114 4.80273C11.1631 5.28095 11.5981 5.60473 12.0889 5.60473H16.195C17.2925 5.60473 17.7488 7.04353 16.8609 7.70442L13.539 10.177C13.1419 10.4726 12.9758 10.9964 13.1275 11.4747L14.3963 15.4754C14.7355 16.5447 13.5408 17.434 12.6529 16.7731L9.33099 14.3005C8.9339 14.0049 8.39621 14.0049 7.99913 14.3005L4.67723 16.7731C3.78932 17.434 2.59466 16.5447 2.9338 15.4754L4.20266 11.4747C4.35433 10.9964 4.18817 10.4726 3.79109 10.177L0.469188 7.70442C-0.418717 7.04353 0.0376084 5.60473 1.13512 5.60473H5.24122C5.73204 5.60473 6.16704 5.28095 6.31871 4.80273L7.58756 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M53.9225 0.802006C54.2617 -0.267338 55.7384 -0.267334 56.0775 0.80201L57.3464 4.80273C57.498 5.28095 57.933 5.60473 58.4239 5.60473H62.53C63.6275 5.60473 64.0838 7.04353 63.1959 7.70442L59.874 10.177C59.4769 10.4726 59.3107 10.9964 59.4624 11.4747L60.7313 15.4754C61.0704 16.5447 59.8758 17.434 58.9878 16.7731L55.6659 14.3005C55.2689 14.0049 54.7312 14.0049 54.3341 14.3005L51.0122 16.7731C50.1243 17.434 48.9296 16.5447 49.2688 15.4754L50.5376 11.4747C50.6893 10.9964 50.5231 10.4726 50.126 10.177L46.8041 7.70442C45.9162 7.04353 46.3726 5.60473 47.4701 5.60473H51.5762C52.067 5.60473 52.502 5.28095 52.6537 4.80273L53.9225 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M30.755 0.802006C31.0942 -0.267338 32.5709 -0.267334 32.91 0.80201L34.1789 4.80273C34.3306 5.28095 34.7656 5.60473 35.2564 5.60473H39.3625C40.46 5.60473 40.9163 7.04353 40.0284 7.70442L36.7065 10.177C36.3094 10.4726 36.1433 10.9964 36.2949 11.4747L37.5638 15.4754C37.9029 16.5447 36.7083 17.434 35.8204 16.7731L32.4985 14.3005C32.1014 14.0049 31.5637 14.0049 31.1666 14.3005L27.8447 16.7731C26.9568 17.434 25.7621 16.5447 26.1013 15.4754L27.3701 11.4747C27.5218 10.9964 27.3557 10.4726 26.9586 10.177L23.6367 7.70442C22.7488 7.04353 23.2051 5.60473 24.3026 5.60473H28.4087C28.8995 5.60473 29.3345 5.28095 29.4862 4.80273L30.755 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M77.09 0.802006C77.4291 -0.267338 78.9058 -0.267334 79.245 0.80201L80.5138 4.80273C80.6655 5.28095 81.1005 5.60473 81.5913 5.60473H85.6974C86.7949 5.60473 87.2512 7.04353 86.3633 7.70442L83.0414 10.177C82.6443 10.4726 82.4782 10.9964 82.6299 11.4747L83.8987 15.4754C84.2379 16.5447 83.0432 17.434 82.1553 16.7731L78.8334 14.3005C78.4363 14.0049 77.8986 14.0049 77.5015 14.3005L74.1796 16.7731C73.2917 17.434 72.0971 16.5447 72.4362 15.4754L73.7051 11.4747C73.8567 10.9964 73.6906 10.4726 73.2935 10.177L69.9716 7.70442C69.0837 7.04353 69.54 5.60473 70.6375 5.60473H74.7436C75.2344 5.60473 75.6694 5.28095 75.8211 4.80273L77.09 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M100.257 0.802006C100.597 -0.267338 102.073 -0.267334 102.412 0.80201L103.681 4.80273C103.833 5.28095 104.268 5.60473 104.759 5.60473H108.865C109.962 5.60473 110.419 7.04353 109.531 7.70442L106.209 10.177C105.812 10.4726 105.646 10.9964 105.797 11.4747L107.066 15.4754C107.405 16.5447 106.211 17.434 105.323 16.7731L102.001 14.3005C101.604 14.0049 101.066 14.0049 100.669 14.3005L97.3471 16.7731C96.4592 17.434 95.2645 16.5447 95.6037 15.4754L96.8725 11.4747C97.0242 10.9964 96.858 10.4726 96.461 10.177L93.1391 7.70442C92.2512 7.04353 92.7075 5.60473 93.805 5.60473H97.9111C98.4019 5.60473 98.8369 5.28095 98.9886 4.80273L100.257 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                                                viewBox="0 0 35 35" fill="none">
                                                <circle cx="17.5" cy="17.5" r="17.5" fill="#FFC97A"></circle>
                                            </svg>
                                        </div>
                                        <div class="meta">
                                            <p>David R. <br> <span>1 week ago</span></p>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="svg-div">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="card-shape" width="296"
                                                height="257" fill="none">
                                                <path
                                                    d="M10 0.5H286C291.247 0.5 295.5 4.75329 295.5 10V198C295.5 203.247 291.247 207.5 286 207.5H164.66C158.861 207.5 154.16 212.201 154.16 218V247C154.16 252.247 149.907 256.5 144.66 256.5H10C4.7533 256.5 0.5 252.247 0.5 247V10C0.500001 4.7533 4.7533 0.5 10 0.5Z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="text-stars">
                                            <p>
                                                Such a smooth <br> experience — my <br> pup loved it!
                                            </p>
                                            <div class="stars">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="110" height="17"
                                                    viewBox="0 0 110 17" fill="none">
                                                    <path
                                                        d="M7.58756 0.802006C7.92671 -0.267338 9.40341 -0.267334 9.74255 0.80201L11.0114 4.80273C11.1631 5.28095 11.5981 5.60473 12.0889 5.60473H16.195C17.2925 5.60473 17.7488 7.04353 16.8609 7.70442L13.539 10.177C13.1419 10.4726 12.9758 10.9964 13.1275 11.4747L14.3963 15.4754C14.7355 16.5447 13.5408 17.434 12.6529 16.7731L9.33099 14.3005C8.9339 14.0049 8.39621 14.0049 7.99913 14.3005L4.67723 16.7731C3.78932 17.434 2.59466 16.5447 2.9338 15.4754L4.20266 11.4747C4.35433 10.9964 4.18817 10.4726 3.79109 10.177L0.469188 7.70442C-0.418717 7.04353 0.0376084 5.60473 1.13512 5.60473H5.24122C5.73204 5.60473 6.16704 5.28095 6.31871 4.80273L7.58756 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M53.9225 0.802006C54.2617 -0.267338 55.7384 -0.267334 56.0775 0.80201L57.3464 4.80273C57.498 5.28095 57.933 5.60473 58.4239 5.60473H62.53C63.6275 5.60473 64.0838 7.04353 63.1959 7.70442L59.874 10.177C59.4769 10.4726 59.3107 10.9964 59.4624 11.4747L60.7313 15.4754C61.0704 16.5447 59.8758 17.434 58.9878 16.7731L55.6659 14.3005C55.2689 14.0049 54.7312 14.0049 54.3341 14.3005L51.0122 16.7731C50.1243 17.434 48.9296 16.5447 49.2688 15.4754L50.5376 11.4747C50.6893 10.9964 50.5231 10.4726 50.126 10.177L46.8041 7.70442C45.9162 7.04353 46.3726 5.60473 47.4701 5.60473H51.5762C52.067 5.60473 52.502 5.28095 52.6537 4.80273L53.9225 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M30.755 0.802006C31.0942 -0.267338 32.5709 -0.267334 32.91 0.80201L34.1789 4.80273C34.3306 5.28095 34.7656 5.60473 35.2564 5.60473H39.3625C40.46 5.60473 40.9163 7.04353 40.0284 7.70442L36.7065 10.177C36.3094 10.4726 36.1433 10.9964 36.2949 11.4747L37.5638 15.4754C37.9029 16.5447 36.7083 17.434 35.8204 16.7731L32.4985 14.3005C32.1014 14.0049 31.5637 14.0049 31.1666 14.3005L27.8447 16.7731C26.9568 17.434 25.7621 16.5447 26.1013 15.4754L27.3701 11.4747C27.5218 10.9964 27.3557 10.4726 26.9586 10.177L23.6367 7.70442C22.7488 7.04353 23.2051 5.60473 24.3026 5.60473H28.4087C28.8995 5.60473 29.3345 5.28095 29.4862 4.80273L30.755 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M77.09 0.802006C77.4291 -0.267338 78.9058 -0.267334 79.245 0.80201L80.5138 4.80273C80.6655 5.28095 81.1005 5.60473 81.5913 5.60473H85.6974C86.7949 5.60473 87.2512 7.04353 86.3633 7.70442L83.0414 10.177C82.6443 10.4726 82.4782 10.9964 82.6299 11.4747L83.8987 15.4754C84.2379 16.5447 83.0432 17.434 82.1553 16.7731L78.8334 14.3005C78.4363 14.0049 77.8986 14.0049 77.5015 14.3005L74.1796 16.7731C73.2917 17.434 72.0971 16.5447 72.4362 15.4754L73.7051 11.4747C73.8567 10.9964 73.6906 10.4726 73.2935 10.177L69.9716 7.70442C69.0837 7.04353 69.54 5.60473 70.6375 5.60473H74.7436C75.2344 5.60473 75.6694 5.28095 75.8211 4.80273L77.09 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                    <path
                                                        d="M100.257 0.802006C100.597 -0.267338 102.073 -0.267334 102.412 0.80201L103.681 4.80273C103.833 5.28095 104.268 5.60473 104.759 5.60473H108.865C109.962 5.60473 110.419 7.04353 109.531 7.70442L106.209 10.177C105.812 10.4726 105.646 10.9964 105.797 11.4747L107.066 15.4754C107.405 16.5447 106.211 17.434 105.323 16.7731L102.001 14.3005C101.604 14.0049 101.066 14.0049 100.669 14.3005L97.3471 16.7731C96.4592 17.434 95.2645 16.5447 95.6037 15.4754L96.8725 11.4747C97.0242 10.9964 96.858 10.4726 96.461 10.177L93.1391 7.70442C92.2512 7.04353 92.7075 5.60473 93.805 5.60473H97.9111C98.4019 5.60473 98.8369 5.28095 98.9886 4.80273L100.257 0.802006Z"
                                                        fill="#FFC97A"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                                                viewBox="0 0 35 35" fill="none">
                                                <circle cx="17.5" cy="17.5" r="17.5" fill="#FFC97A"></circle>
                                            </svg>
                                        </div>
                                        <div class="meta">
                                            <p>Sarah M. <br> <span>1 day ago</span></p>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payments-protected-text d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                            <path
                                d="M7.5 0C3.375 0 0 3.375 0 7.5C0 11.625 3.375 15 7.5 15C11.625 15 15 11.625 15 7.5C15 3.375 11.625 0 7.5 0ZM6 11.25L2.25 7.5L3.3075 6.4425L6 9.1275L11.6925 3.435L12.75 4.5L6 11.25Z"
                                fill="#D8E8B7"></path>
                        </svg>
                        &nbsp;
                        &nbsp;
                        <p class="protected-text">All payments and messages are protected through FursGo.</p>
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
                        <img src="{{ asset('images/our_mission.png') }}" alt="" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-lg-5 content-column">
                    <h1 class="main-heading">Our Mission</h1>
                    <p class="description">To create a harmonious grooming community—where pets receive gentle, timely
                        care; owners enjoy convenience and peace of mind; groomers find flexible, rewarding
                        opportunities; space owners foster meaningful connections; and Fetchers enable smooth,
                        stress‑free journeys.
                    </p>
                    <button class="action-button">About Us</button>
                </div>
            </div>
        </div>
    </section>

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
                        <h1 class="fursgo-address-heading">FursGo across the UK</h1>
                        <p class="fursgo-sub-heading">Now serving pet owners across the UK .</p>
                        <ul class="fursgo-locations">
                            <li>London</li>
                            <li>Manchester</li>
                            <li>Birmingham Leeds</li>
                            <li>Bristol</li>
                            <li>Edinburgh</li>
                        </ul>
                        <p class="link">More Coming soon.</p>
                        <div class="fursgo-map-image-wrapper">
                            <img src="{{ asset('images/Intersect.png') }}" alt="" style="border-bottom-right-radius: 10%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
