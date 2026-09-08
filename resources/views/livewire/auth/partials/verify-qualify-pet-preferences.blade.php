<div class="basics-card" wire:ignore.self x-data="{
        specialties: $wire.entangle('groomer_pet_specialties'),
        sizes: $wire.entangle('groomer_pet_sizes'),
        otherJoined: $wire.entangle('groomer_specialty_other'),
        otherInput: '',
        listHas(list, value) {
            return Array.from(this[list] || []).includes(value);
        },
        toggle(list, value) {
            const items = Array.from(this[list] || []);
            const index = items.indexOf(value);
            if (index === -1) {
                items.push(value);
            } else {
                items.splice(index, 1);
            }
            this[list] = items;
        },
        otherTags() {
            return String(this.otherJoined || '').split(',').map((part) => part.trim()).filter((tag) => tag !== '');
        },
        addTags() {
            const raw = String(this.otherInput || '').trim();
            if (raw === '' || !this.listHas('specialties', 'other')) {
                return;
            }
            const next = this.otherTags();
            raw.split(',').forEach((part) => {
                const tag = part.trim();
                if (tag === '') {
                    return;
                }
                if (next.some((have) => have.toLowerCase() === tag.toLowerCase())) {
                    return;
                }
                next.push(tag);
            });
            this.otherInput = '';
            this.otherJoined = next.join(', ');
        },
        removeTag(index) {
            const tags = this.otherTags();
            if (index < 0 || index >= tags.length) {
                return;
            }
            tags.splice(index, 1);
            this.otherJoined = tags.join(', ');
        }
    }">
    <h2 class="basics-section-title">Pet preferences</h2>
    <div class="basics-field">
        <label class="form-label">Select Pet Specialty:</label>
        <div class="basics-pet-chip-group">
            <button type="button" class="basics-pet-chip" :class="{ 'is-active': listHas('specialties', 'dog') }"
                @click="toggle('specialties', 'dog')">
                <svg class="basics-pet-chip__icon" xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                    viewBox="0 0 16 15" fill="none" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.02515 0C8.64209 0.000213345 9.22451 0.285449 9.60328 0.772461L11.7185 3.49219C11.8776 3.69667 12.1105 3.83152 12.3669 3.86816L14.3513 4.15137C14.7092 4.20264 15.0125 4.44317 15.1209 4.78809C15.3556 5.53533 15.6937 6.86423 15.4334 7.6582C15.1017 8.66932 14.5694 9.17545 13.6101 9.42285C11.9669 9.84646 10.6081 9.36208 9.00269 10.5859C8.61476 10.8817 8.32298 11.2488 8.10816 11.6592L7.93433 12.0166C6.99837 13.8635 4.67318 15.7352 2.92652 14.708C1.76272 14.0232 1.19689 12.6471 1.54273 11.3418L2.01832 9.5459C2.13774 9.53792 2.2548 9.52867 2.36597 9.51465C2.6604 9.47748 2.95561 9.41272 3.15406 9.28711C3.31249 9.18662 3.47116 9.01627 3.61597 8.83105C3.76469 8.64081 3.91577 8.41352 4.05836 8.18066C4.34353 7.71488 4.60442 7.20842 4.75855 6.88281C4.81749 6.75812 4.76399 6.60893 4.63941 6.5498C4.5149 6.49107 4.36662 6.54376 4.30738 6.66797C4.15908 6.98129 3.90564 7.47235 3.6316 7.91992C3.49464 8.14359 3.35417 8.35266 3.22144 8.52246C3.08483 8.69719 2.96988 8.81132 2.88648 8.86426C2.78857 8.92626 2.59053 8.98231 2.30347 9.01855C2.0271 9.05342 1.70289 9.06706 1.38257 9.06641C1.06811 9.06575 0.762227 9.04878 0.516364 9.03125C0.129202 8.76968 -0.0880056 8.27103 0.0339417 7.80859C1.06233 3.9097 1.6697 2.10636 2.67847 1.09766C3.77006 0.00665437 5.94579 3.69894e-05 5.97144 0H8.02515ZM8.57496 3.66016C8.11053 3.66016 7.61807 3.89064 7.61793 4.8125C7.61793 5.44915 8.42917 4.8125 8.95777 4.8125C9.48622 4.81262 9.53199 5.44909 9.53199 4.8125C9.5318 4.17611 9.10336 3.66027 8.57496 3.66016Z"
                        fill="currentColor" />
                </svg>
                <span>Dog</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('specialties', 'dog')" x-cloak>
            </button>
            <button type="button" class="basics-pet-chip" :class="{ 'is-active': listHas('specialties', 'cat') }"
                @click="toggle('specialties', 'cat')">
                <svg class="basics-pet-chip__icon" xmlns="http://www.w3.org/2000/svg" width="11" height="15"
                    viewBox="0 0 11 15" fill="none" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M4.99889 2.53516C5.53684 2.4688 6.2816 2.4856 6.994 2.72656C7.78167 2.99305 8.5426 3.54017 8.9149 4.55566L10.4501 5.29199L10.4833 5.41211C10.6868 6.146 10.8081 7.28565 10.493 8.32324C10.3341 8.84623 10.0619 9.34947 9.62877 9.75684C9.19459 10.1652 8.61113 10.4656 7.8485 10.6016C5.10446 11.0909 3.54731 13.4378 3.11803 14.5459C2.94136 15.0506 1.89466 15.1787 1.6483 14.7041C-2.03157 7.61478 1.27786 2.01708 3.56236 0L4.99889 2.53516ZM6.69908 5.09961C6.29638 5.09961 5.869 5.30013 5.869 6.09961C5.86942 6.65073 6.57298 6.09961 7.03111 6.09961C7.48905 6.09978 7.52914 6.65064 7.52916 6.09961C7.52916 5.54772 7.15731 5.09966 6.69908 5.09961Z"
                        fill="currentColor" />
                </svg>
                <span>Cat</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('specialties', 'cat')" x-cloak>
            </button>
            <button type="button" class="basics-pet-chip" :class="{ 'is-active': listHas('specialties', 'other') }"
                @click="toggle('specialties', 'other')">
                <svg class="basics-pet-chip__icon" xmlns="http://www.w3.org/2000/svg" width="19" height="15"
                    viewBox="0 0 19 15" fill="none" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.10714 0C5.43536 0 4.90879 0.410454 4.58307 0.902727C4.25329 1.39909 4.07143 2.04682 4.07143 2.72727C4.07143 3.40773 4.25329 4.05545 4.58307 4.55182C4.90879 5.04273 5.43536 5.45455 6.10714 5.45455C6.77893 5.45455 7.3055 5.04409 7.63121 4.55182C7.961 4.05545 8.14286 3.40773 8.14286 2.72727C8.14286 2.04682 7.961 1.39909 7.63121 0.902727C7.3055 0.411818 6.77893 0 6.10714 0ZM12.8929 0C12.2211 0 11.6945 0.410454 11.3688 0.902727C11.039 1.39909 10.8571 2.04682 10.8571 2.72727C10.8571 3.40773 11.039 4.05545 11.3688 4.55182C11.6945 5.04273 12.2211 5.45455 12.8929 5.45455C13.5646 5.45455 14.0912 5.04409 14.4169 4.55182C14.7467 4.05545 14.9286 3.40773 14.9286 2.72727C14.9286 2.04682 14.7467 1.39909 14.4169 0.902727C14.0912 0.411818 13.5646 0 12.8929 0ZM2.03571 6.13636C1.36393 6.13636 0.837357 6.54682 0.511643 7.03909C0.181857 7.53545 0 8.18318 0 8.86364C0 9.54409 0.181857 10.1918 0.511643 10.6882C0.837357 11.1791 1.36393 11.5909 2.03571 11.5909C2.7075 11.5909 3.23407 11.1805 3.55979 10.6882C3.88957 10.1918 4.07143 9.54409 4.07143 8.86364C4.07143 8.18318 3.88957 7.53545 3.55979 7.03909C3.23407 6.54818 2.7075 6.13636 2.03571 6.13636ZM9.5 6.13636C7.87143 6.13636 6.66493 7.01455 5.89407 8.10409C5.13271 9.17727 4.75 10.5095 4.75 11.5909C4.75 12.8509 5.50321 13.7277 6.42743 14.2527C7.33671 14.7709 8.47671 15 9.5 15C10.5233 15 11.6633 14.7723 12.5726 14.2527C13.4954 13.7264 14.25 12.8509 14.25 11.5909C14.25 10.5095 13.8673 9.17727 13.1059 8.10409C12.3364 7.01318 11.1299 6.13636 9.5 6.13636ZM16.9643 6.13636C16.2925 6.13636 15.7659 6.54682 15.4402 7.03909C15.1104 7.53545 14.9286 8.18318 14.9286 8.86364C14.9286 9.54409 15.1104 10.1918 15.4402 10.6882C15.7659 11.1791 16.2925 11.5909 16.9643 11.5909C17.6361 11.5909 18.1626 11.1805 18.4884 10.6882C18.8181 10.1918 19 9.54409 19 8.86364C19 8.18318 18.8181 7.53545 18.4884 7.03909C18.1626 6.54818 17.6361 6.13636 16.9643 6.13636Z"
                        fill="currentColor" />
                </svg>
                <span>Other</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('specialties', 'other')" x-cloak>
            </button>
        </div>
        @error('groomer_pet_specialties')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="basics-field" x-show="listHas('specialties', 'other')" x-cloak>
        <label class="form-label" for="business-specialty-other">Other <span class="form-label-muted">(Please
                specify)</span></label>
        <div class="input-field-wrap basics-other-input-wrap">
            <input id="business-specialty-other" type="text" class="form-input" x-model="otherInput"
                @keydown.enter.prevent="addTags()" placeholder="e.g. Rabbit, Ferret, Hamster ...">
            <button type="button" class="basics-other-plus" aria-label="Add pet types" @click="addTags()">
                <span aria-hidden="true">+</span>
            </button>
        </div>
        @error('groomer_specialty_other')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
    <div class="basics-pet-tags" aria-label="Custom pet types"
        x-show="listHas('specialties', 'other') && otherTags().length > 0" x-cloak>
        <template x-for="(tag, tagIndex) in otherTags()" :key="'pet-tag-' + tag + '-' + tagIndex">
            <span class="basics-pet-tag">
                <span class="basics-pet-tag__label" x-text="tag"></span>
                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + tag"
                    @click="removeTag(tagIndex)">&times;</button>
            </span>
        </template>
    </div>

    <div class="basics-field">
        <label class="form-label">Select Pet Size:</label>
        <div class="basics-pet-chip-group basics-pet-chip-group--sizes">
            <button type="button" class="basics-pet-chip basics-pet-chip--size"
                :class="{ 'is-active': listHas('sizes', 'small') }" @click="toggle('sizes', 'small')">
                <span>Small 0-7 kg</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('sizes', 'small')" x-cloak>
            </button>
            <button type="button" class="basics-pet-chip basics-pet-chip--size"
                :class="{ 'is-active': listHas('sizes', 'medium') }" @click="toggle('sizes', 'medium')">
                <span>Medium 8-18 kg</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('sizes', 'medium')" x-cloak>
            </button>
            <button type="button" class="basics-pet-chip basics-pet-chip--size"
                :class="{ 'is-active': listHas('sizes', 'large') }" @click="toggle('sizes', 'large')">
                <span>Large 19+ kg</span>
                <img class="basics-pet-chip__check" src="{{ asset('images/signup/icon-success.svg') }}" alt=""
                    width="14" height="14" x-show="listHas('sizes', 'large')" x-cloak>
            </button>
        </div>
        @error('groomer_pet_sizes')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>