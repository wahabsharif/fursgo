<?php

/**
 * Edit pet details form (shown when user clicks Edit details on a pet card).
 * Prefill is done in my_pets.js from the card’s data-* attributes.
 */
?>
<div id="pets-edit-view" class="ep-view" hidden>
    <h2 class="ep-title" id="ep-title">Edit pet details</h2>

    <form id="ep-form" class="ep-form" novalidate>
        <input type="hidden" id="ep-pet-id" name="pet_id" value="">

        <div class="ep-top">
            <div class="ep-avatar-col">
                <div class="ep-avatar" id="ep-avatar">
                    <img id="ep-avatar-img" src="" alt="" hidden>
                    <span class="ep-avatar-ph" id="ep-avatar-ph" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="122" height="122" viewBox="0 0 122 122" fill="none">
                            <circle cx="61" cy="61" r="61" fill="#E5E5E5" />
                            <path d="M24.0703 103.944C30.4873 63.836 91.5334 63.8551 97.8986 104.002C98.3553 106.882 97.1596 109.741 94.7635 111.404C89.249 115.231 77.6016 121.594 61.1092 121.594C44.627 121.594 32.8575 115.239 27.2628 111.411C24.8286 109.745 23.6043 106.857 24.0703 103.944Z" fill="white" />
                            <path d="M79.4515 47.9769C79.4515 52.9355 77.4817 57.6911 73.9755 61.1974C70.4692 64.7037 65.7137 66.6735 60.7551 66.6735C55.7964 66.6735 51.0409 64.7037 47.5347 61.1974C44.0284 57.6911 42.0586 52.9355 42.0586 47.9769C42.0586 43.0182 44.0284 38.2627 47.5347 34.7564C51.0409 31.2501 55.7964 29.2803 60.7551 29.2803C65.7137 29.2803 70.4692 31.2501 73.9755 34.7564C77.4817 38.2627 79.4515 43.0182 79.4515 47.9769Z" fill="white" />
                        </svg>
                    </span>
                </div>
                <input type="file" id="ep-avatar-input" accept="image/*" hidden>
                <button type="button" class="ep-upload-btn" id="ep-avatar-btn">
                    <svg width="18" height="14" viewBox="0 0 20 15" fill="none" aria-hidden="true">
                        <path d="M1.25 12.5c0 .69.56 1.25 1.25 1.25h15c.69 0 1.25-.56 1.25-1.25V5c0-.69-.56-1.25-1.25-1.25h-1.47a2.5 2.5 0 0 1-1.77-.73L12.35 1.6A1.25 1.25 0 0 0 11.47 1.25H8.54c-.33 0-.65.13-.88.36L6.62 3.02a2.5 2.5 0 0 1-1.77.73H2.5C1.81 3.75 1.25 4.31 1.25 5v7.5z" stroke="#3B3731" stroke-width="1.2" />
                        <circle cx="10" cy="8.1" r="2.6" stroke="#3B3731" stroke-width="1.2" />
                    </svg>
                    Upload photo
                </button>
            </div>

            <div class="ep-toggles">
                <div class="pet-type-wrapper">
                    <p class="label">Select Pet Type</p>
                    <div class="pet-toggle" id="ep-type-group" role="group" aria-label="Pet type">
                        <button type="button" class="pet-option" data-pet="cat">
                            <span>Cat</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="23" viewBox="0 0 16 23" fill="none" aria-hidden="true">
                                <path d="M7.06607 3.58301C7.8265 3.48922 8.87941 3.51287 9.88638 3.85352C10.9998 4.23021 12.076 5.00399 12.6022 6.43945L14.7711 7.47949L14.818 7.64941C15.1058 8.68692 15.2774 10.2987 14.8317 11.7656C14.6072 12.5047 14.2229 13.2153 13.611 13.791C12.9973 14.3682 12.1722 14.7931 11.0944 14.9854C7.21594 15.6769 5.01487 18.9931 4.40787 20.5596C4.16903 21.2436 3.62369 22.8966 3.62369 23C-3.55897 11.9396 1.57217 3.05801 5.0358 0L7.06607 3.58301ZM9.46841 7.20898C8.89932 7.20905 8.29568 7.49145 8.29556 8.62109C8.29556 9.40123 9.29039 8.62109 9.93814 8.62109C10.5855 8.6214 10.6413 9.40108 10.6413 8.62109C10.6411 7.84111 10.1161 7.20898 9.46841 7.20898Z" />
                            </svg>
                        </button>
                        <button type="button" class="pet-option" data-pet="dog">
                            <span>Dog</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none" aria-hidden="true">
                                <path d="M11.4592 0C12.0763 -1.81872e-05 12.6594 0.284475 13.0383 0.771484L16.2531 4.90625C16.4122 5.1107 16.6451 5.24555 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01307 20.759 6.35645C21.0768 7.32331 21.6368 9.33324 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5012 13.4996 14.6389 12.8358 12.4377 14.5137C11.758 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72483 21.7058 4.32932 20.2969L1.40646 18.5781L2.88596 12.9932C3.03734 12.9827 3.18545 12.9709 3.32639 12.9531C3.72903 12.9023 4.11589 12.815 4.36935 12.6543C4.5727 12.5253 4.78067 12.3019 4.97775 12.0498C5.17867 11.7928 5.3839 11.4855 5.57834 11.168C5.96741 10.5326 6.32411 9.84071 6.53439 9.39648C6.59333 9.27178 6.53985 9.1226 6.41525 9.06348C6.29076 9.00482 6.14244 9.05746 6.08322 9.18164C5.87884 9.61345 5.53038 10.2892 5.15256 10.9062C4.96359 11.2149 4.76927 11.5055 4.5842 11.7422C4.39523 11.9839 4.23009 12.1511 4.10178 12.2324C3.94892 12.3293 3.65905 12.4071 3.26389 12.457C2.87952 12.5055 2.43081 12.5234 1.98947 12.5225C1.58423 12.5216 1.18935 12.5013 0.862518 12.4795C0.852878 12.4768 0.842809 12.4744 0.833221 12.4717C0.259699 12.3089 -0.117574 11.6851 0.0334167 11.1084C1.50838 5.48346 2.34844 2.92212 3.76584 1.50488C5.26577 0.00544894 8.2594 1.93192e-05 8.28146 0H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47085 11.6506 6.59869 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72593 12.5754 5.01786 11.8508 5.01758Z" />
                            </svg>
                        </button>
                        <button type="button" class="pet-option" data-pet="other">
                            <span>Other</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pet-weight-wrapper">
                    <p class="label">Select Pet Size</p>
                    <div class="weight-toggle" id="ep-size-group" role="group" aria-label="Pet size">
                        <button type="button" class="weight-option" data-weight="small">
                            <span>Small 0–7 kg</span>
                        </button>
                        <button type="button" class="weight-option medium" data-weight="medium">
                            <span>Medium 8–18 kg</span>
                        </button>
                        <button type="button" class="weight-option large" data-weight="large">
                            <span>Large 19+ kg</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="ep-grid">
            <div class="ep-field">
                <label class="ep-label" for="ep-name">Name</label>
                <div class="ep-input-wrap">
                    <input type="text" id="ep-name" name="pet_name" class="ep-input" autocomplete="off">
                    <span class="ep-check" id="ep-name-check" hidden aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 19 19" fill="none">
                            <path d="M9.5 0a9.5 9.5 0 1 0 0 19 9.5 9.5 0 0 0 0-19zm-1.9 14.25L2.85 9.5l1.34-1.34L7.6 11.56l7.21-7.21L16.15 5.7 7.6 14.25z" fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="ep-field">
                <label class="ep-label" for="ep-birthday">Birthday</label>
                <input type="text" id="ep-birthday" name="birthday" class="ep-input" placeholder="dd/mm/yyyy" inputmode="numeric" autocomplete="off">
            </div>

            <div class="ep-field">
                <span class="ep-label">Sex</span>
                <div class="ep-radios" role="radiogroup" aria-label="Sex">
                    <label class="ep-radio"><input type="radio" name="ep_sex" value="male"> Male</label>
                    <label class="ep-radio"><input type="radio" name="ep_sex" value="female"> Female</label>
                </div>
            </div>

            <div class="ep-field" id="ep-other-type-wrap">
                <label class="ep-label" for="ep-other-type">Pet Type <span class="ep-label-hint">(for 'other' pets)</span></label>
                <div class="ep-input-wrap">
                    <input type="text" id="ep-other-type" class="ep-input" placeholder="e.g. Rabbit, Guinea Pig..." autocomplete="off">
                    <span class="ep-check" id="ep-other-check" hidden aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 19 19" fill="none">
                            <path d="M9.5 0a9.5 9.5 0 1 0 0 19 9.5 9.5 0 0 0 0-19zm-1.9 14.25L2.85 9.5l1.34-1.34L7.6 11.56l7.21-7.21L16.15 5.7 7.6 14.25z" fill="#C9DDA0" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="ep-field">
                <label class="ep-label" for="ep-breed">Breed(s)</label>
                <select id="ep-breed" class="ep-input ep-select">
                    <option value="">Select breed</option>
                </select>
            </div>

            <div class="ep-field ep-field--narrow">
                <label class="ep-label" for="ep-weight">Weight (kg)</label>
                <input type="number" id="ep-weight" name="pet_weight" class="ep-input" min="0" step="0.1">
            </div>
        </div>

        <div class="ep-field">
            <span class="ep-label">Vaccination status</span>
            <div class="ep-radios" role="radiogroup" aria-label="Vaccination status">
                <label class="ep-radio"><input type="radio" name="ep_vax" value="yes"> Yes</label>
                <label class="ep-radio"><input type="radio" name="ep_vax" value="no"> No</label>
            </div>
        </div>

        <div class="ep-field">
            <label class="ep-label" for="ep-medical">Medical Notes</label>
            <textarea id="ep-medical" class="ep-textarea" rows="3" placeholder="Help us keep your pets healthy and safe!&#10;(e.g allergies, sensitivities, medications, or ongoing treatments)."></textarea>
        </div>

        <div class="ep-field">
            <label class="ep-label" for="ep-personality">Personality &amp; behaviour</label>
            <textarea id="ep-personality" class="ep-textarea" rows="3" placeholder="Any behaviour we should know about?&#10;(e.g. Friendly with people, nervous around loud noises, doesn't like paws touched)."></textarea>
        </div>

        <div class="ep-field">
            <label class="ep-label" for="ep-grooming">Grooming preferences</label>
            <textarea id="ep-grooming" class="ep-textarea" rows="3" placeholder="Any style preferences?&#10;(e.g clip length, shampoo type, sensitive areas)."></textarea>
        </div>

        <div class="ep-gallery">
            <h3 class="ep-gallery-title">Photo Gallery</h3>
            <p class="ep-gallery-sub">Show off your pet by adding additional photos.</p>
            <div class="ep-gallery-grid" id="ep-gallery-grid">
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <label class="ep-gallery-slot">
                        <input type="file" class="ep-gallery-input" accept="image/*" hidden>
                        <img class="ep-gallery-preview" alt="" hidden>
                        <span class="ep-gallery-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path d="M9.23617 13.6118C8.87181 13.6118 8.57644 13.3164 8.57644 12.952V2.19849L5.97584 4.79909C5.71475 5.06018 5.29098 5.05869 5.03172 4.79578C4.77505 4.53548 4.77649 4.11684 5.03494 3.85832L8.6489 0.243335C8.97323 -0.0810793 9.49914 -0.0811162 9.82351 0.243253L13.4386 3.85835C13.6972 4.1169 13.6988 4.5356 13.4422 4.79613C13.1829 5.05953 12.7586 5.06116 12.4972 4.79978L9.89589 2.19849V12.952C9.89589 13.3164 9.60052 13.6118 9.23617 13.6118ZM2.13224 18.1282C1.52441 18.1282 1.0173 17.925 0.610906 17.5187C0.204515 17.1123 0.000879635 16.6047 0 15.996V13.4587C0 13.0943 0.29537 12.799 0.659726 12.799C1.02408 12.799 1.31945 13.0943 1.31945 13.4587V15.996C1.31945 16.1992 1.4039 16.3857 1.57279 16.5555C1.74168 16.7252 1.92772 16.8097 2.13091 16.8088H16.3414C16.5437 16.8088 16.7298 16.7243 16.8995 16.5555C17.0693 16.3866 17.1538 16.2001 17.1529 15.996V13.4587C17.1529 13.0943 17.4482 12.799 17.8126 12.799C18.177 12.799 18.4723 13.0943 18.4723 13.4587V15.996C18.4723 16.6038 18.2691 17.1109 17.8627 17.5173C17.4564 17.9237 16.9488 18.1274 16.3401 18.1282H2.13224Z" fill="#9D9B98" />
                            </svg>
                            Add Photo
                        </span>
                    </label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="ep-field">
            <label class="ep-label" for="ep-notes">Notes <span class="ep-label-hint">(Optional)</span></label>
            <textarea id="ep-notes" class="ep-textarea" rows="3" placeholder="Anything your groomer should know?&#10;(e.g. anxious around dryers, allergies, behaviour cues)."></textarea>
        </div>

        <div class="ep-footer">
            <button type="button" class="ep-btn ep-btn--ghost" id="ep-cancel">Cancel</button>
            <button type="submit" class="ep-btn ep-btn--primary" id="ep-save">Save changes</button>
        </div>
    </form>
</div>
