@props(['profile'])

@php
$pets = $profile['pets'] ?? [];
$petCount = count($pets);
$defaultPetId = $pets[0]['id'] ?? null;
@endphp

<div
    class="admin-co-pets-tab"
    x-data="{
        selectedPetId: @js($defaultPetId),
        scrollProgress: 0,
        canScrollLeft: false,
        canScrollRight: false,

        // Drag-to-scroll (only after the pointer actually moves)
        dragging: false,
        dragReady: false,
        didDrag: false,
        dragStartX: 0,
        dragScrollLeft: 0,

        syncScroll() {
            const el = this.$refs.petScroller;
            if (!el || el.clientWidth === 0) return;
            const max = Math.max(0, el.scrollWidth - el.clientWidth);
            this.scrollProgress = max > 0 ? (el.scrollLeft / max) * 100 : 0;
            this.canScrollLeft = el.scrollLeft > 2;
            this.canScrollRight = max > 2 && el.scrollLeft < max - 2;
        },
        scrollPets(dir) {
            const el = this.$refs.petScroller;
            if (!el) return;
            this.syncScroll();
            if (dir < 0 && !this.canScrollLeft) return;
            if (dir > 0 && !this.canScrollRight) return;
            el.scrollBy({ left: dir * 280, behavior: 'smooth' });
        },
        startDrag(e) {
            // Left mouse button only (touches/pens always ok)
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            const el = this.$refs.petScroller;
            if (!el) return;
            this.dragReady = true;
            this.dragging = false;
            this.didDrag = false;
            this.dragStartX = e.clientX;
            this.dragScrollLeft = el.scrollLeft;
        },
        onDrag(e) {
            if (!this.dragReady) return;
            const el = this.$refs.petScroller;
            if (!el) return;
            const dx = e.clientX - this.dragStartX;

            // Wait until a real drag — so a normal click still selects the card
            if (!this.dragging) {
                if (Math.abs(dx) < 8) return;
                this.dragging = true;
                this.didDrag = true;
                el.setPointerCapture(e.pointerId);
            }

            el.scrollLeft = this.dragScrollLeft - dx;
        },
        endDrag() {
            this.dragReady = false;
            this.dragging = false;
        },
        selectPet(id) {
            // Ignore click that was really a drag
            if (this.didDrag) {
                this.didDrag = false;
                return;
            }
            this.selectedPetId = id;
            this.$dispatch('admin-pet-selected', { id });
        },
    }"
    x-init="$nextTick(() => {
        syncScroll();
        selectPet(selectedPetId);
        const onResize = () => syncScroll();
        window.addEventListener('resize', onResize);
        const el = $refs.petScroller;
        if (el && typeof IntersectionObserver !== 'undefined') {
            const io = new IntersectionObserver((entries) => {
                if (entries.some((e) => e.isIntersecting)) syncScroll();
            });
            io.observe(el);
        }
    })">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Pets <span class="page-title-count">({{ $petCount }} Total)</span></h2>
        <div class="admin-co-pets-head-actions">
            <button type="button" class="admin-co-btn-light">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                    <line x1="6.5" y1="0.5" x2="6.5" y2="12.5" stroke="#3B3731" stroke-linecap="round" />
                    <line x1="12.5" y1="6.5" x2="0.5" y2="6.5" stroke="#3B3731" stroke-linecap="round" />
                </svg>
                Add Pet
            </button>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Data
            </button>
        </div>
    </div>

    {{-- Pet selector carousel --}}
    <section class="admin-card admin-co-panel admin-co-pets-picker-panel">
        <div
            class="admin-co-pets-scroller"
            :class="{ 'is-dragging': dragging }"
            x-ref="petScroller"
            @scroll.passive="syncScroll()"
            @pointerdown="startDrag($event)"
            @pointermove="onDrag($event)"
            @pointerup="endDrag()"
            @pointercancel="endDrag()">
            @foreach ($pets as $pet)
            <button
                type="button"
                class="admin-co-pet-pick is-{{ $pet['status'] }}"
                :class="{ 'is-selected': selectedPetId === @js($pet['id']) }"
                @click="selectPet(@js($pet['id']))"
                @dragstart.prevent>
                <div class="admin-co-pet-avatar admin-co-pet-avatar--pick" aria-hidden="true">
                    @if (! empty($pet['image']))
                    <img src="{{ $pet['image'] }}" alt="" width="44" height="44">
                    @else
                    <span class="admin-co-pet-avatar-icon is-placeholder"></span>
                    @endif
                </div>
                <div class="admin-co-pet-pick-body">
                    <div class="admin-co-pet-pick-name-row">
                        <span class="admin-co-pet-name">
                            <span class="admin-co-pet-type-icon" aria-hidden="true">
                                @if (($pet['type'] ?? '') === 'cat')
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="22" viewBox="0 0 16 22" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                </svg>
                                @elseif (($pet['type'] ?? '') === 'other')
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="20" viewBox="0 0 9 20" fill="none">
                                    <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                </svg>
                                @endif
                            </span>
                            {{ $pet['name'] }}
                        </span>
                        <span class="admin-po-status is-{{ $pet['status'] }}">{{ $pet['status_label'] }}</span>
                    </div>
                    <p class="admin-co-pet-meta">{{ $pet['meta'] }}</p>
                    <span class="admin-co-pet-sessions">{{ $pet['sessions'] }} Grooming sessions</span>
                </div>
            </button>
            @endforeach
        </div>

        <div class="admin-co-pets-nav">
            <div class="admin-co-pets-progress" aria-hidden="true">
                <span
                    class="admin-co-pets-progress-thumb"
                    :style="'left: calc((100% - 33%) * ' + (scrollProgress / 100) + ')'"></span>
            </div>
            <div class="admin-co-pets-arrows">
                <button
                    type="button"
                    class="admin-co-pets-arrow"
                    :class="{ 'is-active': canScrollLeft }"
                    :aria-disabled="!canScrollLeft"
                    aria-label="Previous pets"
                    @click="scrollPets(-1)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <circle cx="16" cy="16" r="16" fill="currentColor" />
                        <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button
                    type="button"
                    class="admin-co-pets-arrow"
                    :class="{ 'is-active': canScrollRight }"
                    :aria-disabled="!canScrollRight"
                    aria-label="Next pets"
                    @click="scrollPets(1)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                        <circle cx="16" cy="16" r="16" fill="currentColor" />
                        <path d="M14 21L19.0343 15.9657L14.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    @foreach ($pets as $pet)
    <div class="admin-co-pet-detail" x-show="selectedPetId === @js($pet['id'])" x-cloak>
        {{-- Selected pet summary --}}
        <section class="admin-card admin-co-panel admin-co-pet-hero">
            <div class="admin-co-pet-hero-left">
                <div class="admin-co-pet-avatar admin-co-pet-avatar--hero" aria-hidden="true">
                    @if (! empty($pet['image']))
                    <img src="{{ $pet['image'] }}" alt="" width="72" height="72">
                    @else
                    <span class="admin-co-pet-avatar-icon">
                        @if (($pet['type'] ?? '') === 'cat')
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 16 22" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.06581 3.58301C7.82628 3.4892 8.88002 3.51281 9.8871 3.85352C11.0004 4.23025 12.0758 5.00413 12.6019 6.43945L14.7709 7.47949L14.8187 7.64941C15.1065 8.68692 15.2771 10.2987 14.8314 11.7656C14.6069 12.5047 14.2228 13.2153 13.6107 13.791C12.997 14.3682 12.1721 14.7931 11.0941 14.9854C7.2159 15.677 5.01567 18.993 4.40858 20.5596C4.15936 21.2733 2.67778 21.4552 2.32948 20.7842C-2.87198 10.7633 1.8074 2.85093 5.03651 0L7.06581 3.58301ZM9.46913 7.20898C8.89997 7.20898 8.29639 7.49132 8.29628 8.62109C8.29628 9.40118 9.29012 8.62119 9.93788 8.62109C10.5856 8.62109 10.642 9.40123 10.642 8.62109C10.6418 7.84122 10.1167 7.20917 9.46913 7.20898Z" fill="#FFC97A" />
                        </svg>
                        @elseif (($pet['type'] ?? '') === 'other')
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" viewBox="0 0 22 20" fill="none">
                            <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="34" viewBox="0 0 22 21" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                        </svg>
                        @endif
                    </span>
                    @endif
                </div>
                <div class="admin-co-pet-hero-info">
                    <div class="admin-co-pet-hero-name-row">
                        <h3 class="admin-co-pet-hero-name">
                            <span class="admin-co-pet-type-icon" aria-hidden="true">
                                @if (($pet['type'] ?? '') === 'cat')
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="22" viewBox="0 0 16 22" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                </svg>
                                @elseif (($pet['type'] ?? '') === 'other')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 22 20" fill="none">
                                    <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                </svg>
                                @endif
                            </span>
                            {{ $pet['name'] }}
                        </h3>
                        <span class="admin-po-status is-{{ $pet['status'] }}">{{ $pet['status_label'] }}</span>
                        @if ($pet['vaccinated'] ?? false)
                        <span class="admin-co-pet-vaccinated">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A6C4D9" />
                            </svg>
                            Vaccinated
                        </span>
                        @endif
                    </div>
                    <p class="admin-co-pet-hero-meta">{{ $pet['meta'] }}</p>
                    <p class="admin-co-pet-hero-added">Added {{ $pet['added'] ?? '—' }}</p>
                </div>
            </div>

            <div class="admin-co-pet-hero-stats">
                <div class="admin-co-pet-hero-stat">
                    <p class="admin-co-pet-hero-stat-label">Grooming sessions</p>
                    <p class="admin-co-pet-hero-stat-value">{{ $pet['sessions'] }} Sessions</p>
                </div>
                <div class="admin-co-pet-hero-stat">
                    <p class="admin-co-pet-hero-stat-label">Last groomed</p>
                    <p class="admin-co-pet-hero-stat-value">{{ $pet['last_groomed'] ?? '—' }}</p>
                </div>
                <div class="admin-co-pet-hero-stat">
                    <p class="admin-co-pet-hero-stat-label">Avg. session rating</p>
                    <p class="admin-co-pet-hero-stat-value">
                        @if (($pet['avg_rating'] ?? '—') !== '—')
                        <span class="admin-bp-rating">
                            {{ $pet['avg_rating'] }}
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                            </svg>
                        </span>
                        @else
                        —
                        @endif
                    </p>
                </div>
            </div>
        </section>

        {{-- Pet Details + Health & Medical --}}
        <div class="admin-co-split">
            @php
            $petDetails = collect($pet['details'] ?? []);
            $petDetail = fn (string $label, string $fallback = '') => data_get($petDetails->firstWhere('label', $label), 'value', $fallback);
            $petWeightRaw = (string) $petDetail('Weight', '7 kg');
            $petWeightNum = (float) (preg_replace('/[^\d.]/', '', $petWeightRaw) ?: 7);
            $petWeightUnit = str_contains(strtolower($petWeightRaw), 'lb') ? 'lb' : 'kg';
            @endphp
            <section
                class="admin-card admin-co-panel"
                :class="{ 'is-editing': editingDetails }"
                x-data="{
                    editingDetails: false,
                    openSpecies: false,
                    openGender: false,
                    openAge: false,
                    openUnit: false,
                    openCoat: false,
                    openColour: false,
                    openChip: false,
                    species: @js($petDetail('Species', 'Cat')),
                    breed: @js($petDetail('Breed', '')),
                    gender: @js($petDetail('Gender', 'Female (spayed)')),
                    age: @js($petDetail('Age', '6 years')),
                    weight: @js($petWeightNum),
                    weightUnit: @js($petWeightUnit),
                    coat: @js($petDetail('Coat type', 'Shorthair')),
                    colour: @js($petDetail('Colour', 'Grey')),
                    microchipped: @js($petDetail('Microchipped', 'Yes')),
                    snapshot: null,
                    closeMenus() {
                        this.openSpecies = false;
                        this.openGender = false;
                        this.openAge = false;
                        this.openUnit = false;
                        this.openCoat = false;
                        this.openColour = false;
                        this.openChip = false;
                    },
                    startEdit() {
                        this.snapshot = {
                            species: this.species,
                            breed: this.breed,
                            gender: this.gender,
                            age: this.age,
                            weight: this.weight,
                            weightUnit: this.weightUnit,
                            coat: this.coat,
                            colour: this.colour,
                            microchipped: this.microchipped,
                        };
                        this.closeMenus();
                        this.editingDetails = true;
                    },
                    cancelEdit() {
                        if (this.snapshot) {
                            Object.assign(this, this.snapshot);
                        }
                        this.closeMenus();
                        this.editingDetails = false;
                    },
                    saveEdit() {
                        this.snapshot = null;
                        this.closeMenus();
                        this.editingDetails = false;
                    },
                    detailRows() {
                        return [
                            { label: 'Species', value: this.species },
                            { label: 'Breed', value: this.breed },
                            { label: 'Gender', value: this.gender },
                            { label: 'Age', value: this.age },
                            { label: 'Weight', value: this.weight + ' ' + this.weightUnit },
                            { label: 'Coat type', value: this.coat },
                            { label: 'Colour', value: this.colour },
                            { label: 'Microchipped', value: this.microchipped },
                        ];
                    },
                }">
                <div x-show="!editingDetails">
                    <x-admin.customer.section-header title="Pet Details">
                        <button type="button" class="admin-co-link-btn" @click="startEdit()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Edit
                        </button>
                    </x-admin.customer.section-header>
                    <dl class="admin-co-details">
                        <template x-for="row in detailRows()" :key="row.label">
                            <div class="admin-co-details-row">
                                <dt x-text="row.label"></dt>
                                <dd x-text="row.value"></dd>
                            </div>
                        </template>
                    </dl>
                </div>

                <div x-show="editingDetails" x-cloak>
                    <div class="admin-co-section-head">
                        <h3 class="admin-co-section-title admin-co-edit-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Pet Details
                        </h3>
                        <div class="admin-co-section-action-wrap">
                            <button type="button" class="admin-co-form-btn is-cancel" @click="cancelEdit()">Cancel</button>
                            <button type="button" class="admin-co-form-btn is-save" @click="saveEdit()">Save changes</button>
                        </div>
                    </div>

                    <form class="admin-co-pet-details-form" @submit.prevent>
                        <div class="admin-co-edit-field" :class="{ 'is-open': openSpecies }">
                            <label>Species</label>
                            <div class="admin-co-dd" @click.outside="openSpecies = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openSpecies = !openSpecies">
                                    <span class="admin-co-dd-value" x-text="species"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openSpecies" x-cloak>
                                    <template x-for="opt in ['Cat', 'Dog', 'Other']" :key="opt">
                                        <button type="button" class="admin-co-dd-option" :class="{ 'is-active': species === opt }" @click="species = opt; openSpecies = false" x-text="opt"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-breed-' + @js($pet['id'])">Breed</label>
                            <input :id="'pet-breed-' + @js($pet['id'])" type="text" x-model="breed">
                        </div>

                        <div class="admin-co-edit-field" :class="{ 'is-open': openGender }">
                            <label>Gender</label>
                            <div class="admin-co-dd" @click.outside="openGender = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openGender = !openGender">
                                    <span class="admin-co-dd-value" x-text="gender"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openGender" x-cloak>
                                    <template x-for="opt in ['Female (intact)', 'Female (spayed)', 'Male (intact)', 'Male (neutered)', 'Unknown']" :key="opt">
                                        <button type="button" class="admin-co-dd-option" :class="{ 'is-active': gender === opt }" @click="gender = opt; openGender = false" x-text="opt"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="admin-co-pet-details-form-row">
                            <div class="admin-co-edit-field" :class="{ 'is-open': openAge }">
                                <label>Age</label>
                                <div class="admin-co-dd" @click.outside="openAge = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openAge = !openAge">
                                        <span class="admin-co-dd-value" x-text="age"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openAge" x-cloak>
                                        <template x-for="opt in ['Under 1 year', '1 year', '2 years', '3 years', '4 years', '5 years', '6 years', '7 years', '8 years', '9 years', '10 years', '11 years', '12 years', '13 years', '14 years', '15 years', '16 years', '17 years', '18 years', '19 years', '20+ years']" :key="opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': age === opt }" @click="age = opt; openAge = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-co-edit-field" :class="{ 'is-open': openUnit }">
                                <label :for="'pet-weight-' + @js($pet['id'])">Weight</label>
                                <div class="admin-co-weight-field">
                                    <div class="admin-co-weight-num">
                                        <input :id="'pet-weight-' + @js($pet['id'])" type="number" min="0" step="0.1" x-model.number="weight">
                                        <div class="admin-co-weight-spinners" aria-hidden="true">
                                            <button type="button" class="admin-co-weight-spin" @click="weight = Math.round((Number(weight || 0) + 1) * 10) / 10" tabindex="-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none">
                                                    <path d="M1 4L4 1L7 4" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                            <button type="button" class="admin-co-weight-spin" @click="weight = Math.max(0, Math.round((Number(weight || 0) - 1) * 10) / 10)" tabindex="-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none">
                                                    <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="admin-co-dd admin-co-dd--unit" @click.outside="openUnit = false">
                                        <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openUnit = !openUnit">
                                            <span class="admin-co-dd-value" x-text="weightUnit"></span>
                                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <div class="admin-co-dd-menu" x-show="openUnit" x-cloak>
                                            <template x-for="opt in ['kg', 'lb']" :key="opt">
                                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': weightUnit === opt }" @click="weightUnit = opt; openUnit = false" x-text="opt"></button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-co-pet-details-form-row">
                            <div class="admin-co-edit-field" :class="{ 'is-open': openCoat }">
                                <label>Coat Type</label>
                                <div class="admin-co-dd" @click.outside="openCoat = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openCoat = !openCoat">
                                        <span class="admin-co-dd-value" x-text="coat"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openCoat" x-cloak>
                                        <template x-for="opt in ['Shorthair', 'Longhair', 'Double coat', 'Curly', 'Wiry', 'Hairless']" :key="opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': coat === opt }" @click="coat = opt; openCoat = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-co-edit-field" :class="{ 'is-open': openColour }">
                                <label>Colour</label>
                                <div class="admin-co-dd" @click.outside="openColour = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openColour = !openColour">
                                        <span class="admin-co-dd-value" x-text="colour"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openColour" x-cloak>
                                        <template x-for="opt in ['Grey', 'Black', 'White', 'Brown', 'Cream', 'Ginger', 'Mixed', 'Other']" :key="opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': colour === opt }" @click="colour = opt; openColour = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-co-edit-field" :class="{ 'is-open': openChip }">
                            <label>Microchipped</label>
                            <div class="admin-co-dd" @click.outside="openChip = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openChip = !openChip">
                                    <span class="admin-co-dd-value" x-text="microchipped"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openChip" x-cloak>
                                    <template x-for="opt in ['Yes', 'No', 'Unknown']" :key="opt">
                                        <button type="button" class="admin-co-dd-option" :class="{ 'is-active': microchipped === opt }" @click="microchipped = opt; openChip = false" x-text="opt"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            @php
            $petHealth = collect($pet['health'] ?? []);
            $petHealthVal = fn (string $label, string $fallback = '') => data_get($petHealth->firstWhere('label', $label), 'value', $fallback);
            $vaxRaw = (string) $petHealthVal('Vaccination status', 'Up to date');
            $vaxStatus = 'Up to date';
            $vaxExpiry = 'Apr 2028';
            if (preg_match('/^(.*?)\s*\(expires\s+(.+?)\)$/i', $vaxRaw, $m)) {
            $vaxStatus = trim($m[1]);
            $vaxExpiry = trim($m[2]);
            } elseif (strcasecmp($vaxRaw, 'N/A') === 0 || strcasecmp($vaxRaw, 'Not applicable') === 0) {
            $vaxStatus = 'Not applicable';
            $vaxExpiry = '';
            } else {
            $vaxStatus = $vaxRaw;
            }
            $vetRaw = (string) $petHealthVal('Vet name', '');
            $vetName = $vetRaw;
            $vetAddress = '';
            if (str_contains($vetRaw, ',')) {
            [$vetName, $vetAddress] = array_map('trim', explode(',', $vetRaw, 2));
            if ($vetAddress !== '' && ! str_contains(strtolower($vetAddress), 'london')) {
            $vetAddress = 'London, ' . $vetAddress;
            }
            }
            $monthYears = [];
            for ($y = (int) date('Y') + 5; $y >= (int) date('Y') - 10; $y--) {
            foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $mo) {
            $monthYears[] = $mo . ' ' . $y;
            }
            }
            $statusOptions = ['Up to date', 'Overdue', 'Unknown', 'Not applicable'];
            @endphp
            <section
                class="admin-card admin-co-panel"
                :class="{ 'is-editing': editingHealth }"
                x-data="{
                    editingHealth: false,
                    openVaxStatus: false,
                    openLastVax: false,
                    openVaxExpiry: false,
                    openFlea: false,
                    vaxStatus: @js($vaxStatus),
                    lastVaccinated: @js($petHealthVal('Last vaccinated', 'Jan 2025')),
                    vaxExpiry: @js($vaxExpiry),
                    fleaTreatment: @js($petHealthVal('Flea treatment', 'Up to date')),
                    conditions: @js($petHealthVal('Known conditions', '')),
                    allergies: @js($petHealthVal('Allergies', '')),
                    medication: @js($petHealthVal('Medication', '')),
                    vetName: @js($vetName),
                    vetAddress: @js($vetAddress !== '' ? $vetAddress : 'London, SW11'),
                    vetPhone: @js($petHealthVal('Vet phone', '')),
                    statusOptions: @js($statusOptions),
                    monthYears: @js($monthYears),
                    snapshot: null,
                    closeMenus() {
                        this.openVaxStatus = false;
                        this.openLastVax = false;
                        this.openVaxExpiry = false;
                        this.openFlea = false;
                    },
                    startEdit() {
                        this.snapshot = {
                            vaxStatus: this.vaxStatus,
                            lastVaccinated: this.lastVaccinated,
                            vaxExpiry: this.vaxExpiry,
                            fleaTreatment: this.fleaTreatment,
                            conditions: this.conditions,
                            allergies: this.allergies,
                            medication: this.medication,
                            vetName: this.vetName,
                            vetAddress: this.vetAddress,
                            vetPhone: this.vetPhone,
                        };
                        this.closeMenus();
                        this.editingHealth = true;
                    },
                    cancelEdit() {
                        if (this.snapshot) Object.assign(this, this.snapshot);
                        this.closeMenus();
                        this.editingHealth = false;
                    },
                    saveEdit() {
                        this.snapshot = null;
                        this.closeMenus();
                        this.editingHealth = false;
                    },
                    vaxDisplay() {
                        if (this.vaxStatus === 'Up to date' && this.vaxExpiry) {
                            return this.vaxStatus + ' (expires ' + this.vaxExpiry + ')';
                        }
                        return this.vaxStatus;
                    },
                    vetDisplay() {
                        const parts = [this.vetName, this.vetAddress].filter(Boolean);
                        return parts.join(', ');
                    },
                    healthRows() {
                        return [
                            { label: 'Vaccination status', value: this.vaxDisplay(), ok: this.vaxStatus === 'Up to date' },
                            { label: 'Last vaccinated', value: this.lastVaccinated, ok: false },
                            { label: 'Flea treatment', value: this.fleaTreatment, ok: this.fleaTreatment === 'Up to date' },
                            { label: 'Known conditions', value: this.conditions, ok: false },
                            { label: 'Allergies', value: this.allergies, ok: false },
                            { label: 'Medication', value: this.medication, ok: false },
                            { label: 'Vet name', value: this.vetDisplay(), ok: false },
                            { label: 'Vet phone', value: this.vetPhone, ok: false },
                        ];
                    },
                }">
                <div x-show="!editingHealth">
                    <x-admin.customer.section-header title="Health & Medical">
                        <button type="button" class="admin-co-link-btn" @click="startEdit()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Edit
                        </button>
                    </x-admin.customer.section-header>
                    <dl class="admin-co-details">
                        <template x-for="row in healthRows()" :key="row.label">
                            <div class="admin-co-details-row">
                                <dt x-text="row.label"></dt>
                                <dd>
                                    <template x-if="row.ok">
                                        <span class="admin-co-pet-health-ok">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                                <path d="M4.196 9L0 4.734L1.049 3.668L4.196 6.867L10.951 0L12 1.066L4.196 9Z" fill="#A7C569" />
                                            </svg>
                                            <span x-text="row.value"></span>
                                        </span>
                                    </template>
                                    <template x-if="!row.ok">
                                        <span x-text="row.value"></span>
                                    </template>
                                </dd>
                            </div>
                        </template>
                    </dl>
                </div>

                <div x-show="editingHealth" x-cloak>
                    <div class="admin-co-section-head">
                        <h3 class="admin-co-section-title admin-co-edit-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Health & Medical
                        </h3>
                        <div class="admin-co-section-action-wrap">
                            <button type="button" class="admin-co-form-btn is-cancel" @click="cancelEdit()">Cancel</button>
                            <button type="button" class="admin-co-form-btn is-save" @click="saveEdit()">Save changes</button>
                        </div>
                    </div>

                    <div class="admin-co-pet-health-notice" role="note">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <path d="M5 9.5C7.48528 9.5 9.5 7.48528 9.5 5C9.5 2.51472 7.48528 0.5 5 0.5C2.51472 0.5 0.5 2.51472 0.5 5C0.5 7.48528 2.51472 9.5 5 9.5Z" stroke="#A6BBC9" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5 6.79995V4.99995M5 3.19995H5.0045" stroke="#A6BBC9" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>Health and medical changes are visible to groomers before every booking involving this pet. Make sure information is accurate and up to date.</p>
                    </div>

                    <form class="admin-co-pet-details-form" @submit.prevent>
                        <div class="admin-co-pet-health-grid">
                            <div class="admin-co-edit-field" :class="{ 'is-open': openVaxStatus }">
                                <label>Vaccination status</label>
                                <div class="admin-co-dd" @click.outside="openVaxStatus = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openVaxStatus = !openVaxStatus">
                                        <span class="admin-co-dd-value" x-text="vaxStatus"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openVaxStatus" x-cloak>
                                        <template x-for="opt in statusOptions" :key="opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': vaxStatus === opt }" @click="vaxStatus = opt; openVaxStatus = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-co-edit-field" :class="{ 'is-open': openLastVax }">
                                <label>Last vaccinated</label>
                                <div class="admin-co-dd" @click.outside="openLastVax = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openLastVax = !openLastVax">
                                        <span class="admin-co-dd-value" x-text="lastVaccinated"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openLastVax" x-cloak>
                                        <template x-for="opt in monthYears" :key="'last-' + opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': lastVaccinated === opt }" @click="lastVaccinated = opt; openLastVax = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-co-edit-field" :class="{ 'is-open': openVaxExpiry }">
                                <label>Vaccination expiry</label>
                                <div class="admin-co-dd" @click.outside="openVaxExpiry = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openVaxExpiry = !openVaxExpiry">
                                        <span class="admin-co-dd-value" x-text="vaxExpiry || '—'"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openVaxExpiry" x-cloak>
                                        <template x-for="opt in monthYears" :key="'exp-' + opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': vaxExpiry === opt }" @click="vaxExpiry = opt; openVaxExpiry = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="admin-co-edit-field" :class="{ 'is-open': openFlea }">
                                <label>Flea treatment</label>
                                <div class="admin-co-dd" @click.outside="openFlea = false">
                                    <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openFlea = !openFlea">
                                        <span class="admin-co-dd-value" x-text="fleaTreatment"></span>
                                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <div class="admin-co-dd-menu" x-show="openFlea" x-cloak>
                                        <template x-for="opt in statusOptions" :key="'flea-' + opt">
                                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': fleaTreatment === opt }" @click="fleaTreatment = opt; openFlea = false" x-text="opt"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-conditions-' + @js($pet['id'])">Known conditions</label>
                            <input :id="'pet-conditions-' + @js($pet['id'])" type="text" x-model="conditions">
                            <p class="admin-co-field-hint">Separate multiple conditions with a comma</p>
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-allergies-' + @js($pet['id'])">Allergies</label>
                            <input :id="'pet-allergies-' + @js($pet['id'])" type="text" x-model="allergies">
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-medication-' + @js($pet['id'])">Current medication</label>
                            <input :id="'pet-medication-' + @js($pet['id'])" type="text" x-model="medication">
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-vet-name-' + @js($pet['id'])">Vet name</label>
                            <input :id="'pet-vet-name-' + @js($pet['id'])" type="text" x-model="vetName">
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-vet-address-' + @js($pet['id'])">Vet address</label>
                            <input :id="'pet-vet-address-' + @js($pet['id'])" type="text" x-model="vetAddress">
                        </div>

                        <div class="admin-co-edit-field">
                            <label :for="'pet-vet-phone-' + @js($pet['id'])">Vet phone number</label>
                            <input :id="'pet-vet-phone-' + @js($pet['id'])" type="text" x-model="vetPhone">
                        </div>
                    </form>
                </div>
            </section>
        </div>

        {{-- Health & behaviour flags --}}
        <section
            class="admin-card admin-co-panel"
            :class="{ 'is-editing': editingFlags }"
            x-data="{
                editingFlags: false,
                openFlagTone: false,
                flags: @js($pet['flags'] ?? []),
                draftFlags: [],
                newFlagText: '',
                newFlagTone: 'info',
                toneOptions: [
                    { value: 'info', label: 'Info (grey)' },
                    { value: 'ok', label: 'Positive (green)' },
                    { value: 'warn', label: 'Caution (amber)' },
                    { value: 'danger', label: 'Warning (red)' },
                ],
                toneLabel() {
                    const found = this.toneOptions.find((o) => o.value === this.newFlagTone);
                    return found ? found.label : 'Info (grey)';
                },
                startEdit() {
                    this.draftFlags = this.flags.map((f) => ({ ...f }));
                    this.newFlagText = '';
                    this.newFlagTone = 'info';
                    this.openFlagTone = false;
                    this.editingFlags = true;
                },
                cancelEdit() {
                    this.draftFlags = [];
                    this.newFlagText = '';
                    this.newFlagTone = 'info';
                    this.openFlagTone = false;
                    this.editingFlags = false;
                },
                saveEdit() {
                    this.flags = this.draftFlags.map((f) => ({ ...f }));
                    this.draftFlags = [];
                    this.newFlagText = '';
                    this.newFlagTone = 'info';
                    this.openFlagTone = false;
                    this.editingFlags = false;
                },
                removeFlag(index) {
                    this.draftFlags.splice(index, 1);
                },
                addFlag() {
                    const label = (this.newFlagText || '').trim();
                    if (!label) return;
                    this.draftFlags.push({ label, tone: this.newFlagTone });
                    this.newFlagText = '';
                    this.openFlagTone = false;
                },
            }">
            <div x-show="!editingFlags">
                <x-admin.customer.section-header title="Health & behaviour flags">
                    <button type="button" class="admin-co-link-btn" @click="startEdit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                <div class="admin-co-pet-flags">
                    <template x-for="(flag, index) in flags" :key="'view-flag-' + index + '-' + flag.label">
                        <span class="admin-co-pet-flag" :class="'is-' + flag.tone" x-text="flag.label"></span>
                    </template>
                </div>
            </div>

            <div x-show="editingFlags" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title admin-co-edit-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Health & behaviour flags
                    </h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="cancelEdit()">Cancel</button>
                        <button type="button" class="admin-co-form-btn is-save" @click="saveEdit()">Save changes</button>
                    </div>
                </div>

                <div class="admin-co-pet-health-notice" role="note">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="7.25" stroke="#8CBCDD" stroke-width="1.5" />
                        <path d="M8 7.25V11.5" stroke="#8CBCDD" stroke-width="1.5" stroke-linecap="round" />
                        <circle cx="8" cy="5" r="0.85" fill="#8CBCDD" />
                    </svg>
                    <p>These flags are shown to groomers before every booking. Click the × to remove a flag. Use the type selector to set how urgent each flag is.</p>
                </div>

                <div class="admin-co-pet-flags is-editing">
                    <template x-for="(flag, index) in draftFlags" :key="'edit-flag-' + index + '-' + flag.label">
                        <span class="admin-co-pet-flag is-removable" :class="'is-' + flag.tone">
                            <span x-text="flag.label"></span>
                            <button type="button" class="admin-co-pet-flag-remove" :aria-label="'Remove ' + flag.label" @click="removeFlag(index)">×</button>
                        </span>
                    </template>
                </div>

                <div class="admin-co-pet-flag-add">
                    <p class="admin-co-pet-flag-add-title">Add a new flag</p>
                    <div class="admin-co-pet-flag-add-row">
                        <input
                            type="text"
                            class="admin-co-pet-flag-input"
                            placeholder="Describe health & behaviour flag, e.g anxious in new environements"
                            x-model="newFlagText"
                            @keydown.enter.prevent="addFlag()">
                        <div class="admin-co-dd admin-co-dd--flag-tone" @click.outside="openFlagTone = false">
                            <button type="button" class="admin-co-dd-trigger" @click="openFlagTone = !openFlagTone">
                                <span class="admin-co-dd-value" x-text="toneLabel()"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openFlagTone" x-cloak>
                                <template x-for="opt in toneOptions" :key="opt.value">
                                    <button
                                        type="button"
                                        class="admin-co-dd-option"
                                        :class="{ 'is-active': newFlagTone === opt.value }"
                                        @click="newFlagTone = opt.value; openFlagTone = false"
                                        x-text="opt.label"></button>
                                </template>
                            </div>
                        </div>
                        <button type="button" class="admin-co-form-btn is-add-flag" @click="addFlag()">+ Add flag</button>
                    </div>
                    <p class="admin-co-field-hint">Use caution (amber) for care notes, warning (red) for health risks that affect grooming safety</p>
                </div>
            </div>
        </section>

        {{-- Grooming preferences --}}
        @php
        $petGrooming = collect($pet['grooming'] ?? []);
        $groomVal = fn (string $label, string $fallback = '') => data_get($petGrooming->firstWhere('label', $label), 'value', $fallback);
        $parseYesNo = function (string $raw) {
        if (preg_match('/^(Yes|No)\s*[—\-–]\s*(.+)$/iu', $raw, $m)) {
        return ['choice' => $m[1], 'note' => $m[2]];
        }
        if (preg_match('/^(Yes|No)$/iu', trim($raw), $m)) {
        return ['choice' => $m[1], 'note' => ''];
        }
        return ['choice' => 'Yes', 'note' => $raw];
        };
        $nail = $parseYesNo((string) $groomVal('Nail trim', 'Yes — short but not too quick'));
        $ear = $parseYesNo((string) $groomVal('Ear cleaning', 'Yes — prone to build-up'));
        $blow = $parseYesNo((string) $groomVal('Blow dry', 'No — does not tolerate'));
        @endphp
        <section
            class="admin-card admin-co-panel grooming-preferences"
            :class="{ 'is-editing': editingGrooming }"
            x-data="{
                editingGrooming: false,
                openNail: false,
                openEar: false,
                openBlow: false,
                preferredStyle: @js($groomVal('Preferred style', '')),
                nailChoice: @js($nail['choice']),
                nailNote: @js($nail['note']),
                earChoice: @js($ear['choice']),
                earNote: @js($ear['note']),
                blowChoice: @js($blow['choice']),
                blowNote: @js($blow['note']),
                handlingNotes: @js($groomVal('Handling notes', '')),
                products: @js($groomVal('Products', '')),
                snapshot: null,
                closeMenus() {
                    this.openNail = false;
                    this.openEar = false;
                    this.openBlow = false;
                },
                startEdit() {
                    this.snapshot = {
                        preferredStyle: this.preferredStyle,
                        nailChoice: this.nailChoice,
                        nailNote: this.nailNote,
                        earChoice: this.earChoice,
                        earNote: this.earNote,
                        blowChoice: this.blowChoice,
                        blowNote: this.blowNote,
                        handlingNotes: this.handlingNotes,
                        products: this.products,
                    };
                    this.closeMenus();
                    this.editingGrooming = true;
                },
                cancelEdit() {
                    if (this.snapshot) Object.assign(this, this.snapshot);
                    this.closeMenus();
                    this.editingGrooming = false;
                },
                saveEdit() {
                    this.snapshot = null;
                    this.closeMenus();
                    this.editingGrooming = false;
                },
                pairValue(choice, note) {
                    const n = (note || '').trim();
                    return n ? (choice + ' — ' + n) : choice;
                },
                groomingRows() {
                    return [
                        { label: 'Preferred style', value: this.preferredStyle },
                        { label: 'Nail trim', value: this.pairValue(this.nailChoice, this.nailNote) },
                        { label: 'Ear cleaning', value: this.pairValue(this.earChoice, this.earNote) },
                        { label: 'Blow dry', value: this.pairValue(this.blowChoice, this.blowNote) },
                        { label: 'Handling notes', value: this.handlingNotes },
                        { label: 'Products', value: this.products },
                    ];
                },
            }">
            <div x-show="!editingGrooming">
                <x-admin.customer.section-header title="Grooming preferences">
                    <button type="button" class="admin-co-link-btn" @click="startEdit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                <dl class="admin-co-details">
                    <template x-for="row in groomingRows()" :key="row.label">
                        <div class="admin-co-details-row">
                            <dt x-text="row.label"></dt>
                            <dd x-text="row.value"></dd>
                        </div>
                    </template>
                </dl>
            </div>

            <div x-show="editingGrooming" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title admin-co-edit-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Grooming preferences
                    </h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="cancelEdit()">Cancel</button>
                        <button type="button" class="admin-co-form-btn is-save" @click="saveEdit()">Save changes</button>
                    </div>
                </div>

                <div class="admin-co-grooming-edit">
                    <div class="admin-co-grooming-edit-row">
                        <label class="admin-co-grooming-edit-label" :for="'groom-style-' + @js($pet['id'])">Preferred style</label>
                        <input :id="'groom-style-' + @js($pet['id'])" type="text" class="admin-co-grooming-edit-input" x-model="preferredStyle">
                    </div>

                    <div class="admin-co-grooming-edit-row" :class="{ 'is-open': openNail }">
                        <span class="admin-co-grooming-edit-label">Nail trim</span>
                        <div class="admin-co-grooming-edit-controls">
                            <div class="admin-co-dd admin-co-dd--yesno" @click.outside="openNail = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openNail = !openNail">
                                    <span class="admin-co-dd-value" x-text="nailChoice"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openNail" x-cloak>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': nailChoice === 'Yes' }" @click="nailChoice = 'Yes'; openNail = false">Yes</button>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': nailChoice === 'No' }" @click="nailChoice = 'No'; openNail = false">No</button>
                                </div>
                            </div>
                            <input type="text" class="admin-co-grooming-edit-input" x-model="nailNote" aria-label="Nail trim notes">
                        </div>
                    </div>

                    <div class="admin-co-grooming-edit-row" :class="{ 'is-open': openEar }">
                        <span class="admin-co-grooming-edit-label">Ear cleaning</span>
                        <div class="admin-co-grooming-edit-controls">
                            <div class="admin-co-dd admin-co-dd--yesno" @click.outside="openEar = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openEar = !openEar">
                                    <span class="admin-co-dd-value" x-text="earChoice"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openEar" x-cloak>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': earChoice === 'Yes' }" @click="earChoice = 'Yes'; openEar = false">Yes</button>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': earChoice === 'No' }" @click="earChoice = 'No'; openEar = false">No</button>
                                </div>
                            </div>
                            <input type="text" class="admin-co-grooming-edit-input" x-model="earNote" aria-label="Ear cleaning notes">
                        </div>
                    </div>

                    <div class="admin-co-grooming-edit-row" :class="{ 'is-open': openBlow }">
                        <span class="admin-co-grooming-edit-label">Blow dry</span>
                        <div class="admin-co-grooming-edit-controls">
                            <div class="admin-co-dd admin-co-dd--yesno" @click.outside="openBlow = false">
                                <button type="button" class="admin-co-dd-trigger" @click="closeMenus(); openBlow = !openBlow">
                                    <span class="admin-co-dd-value" x-text="blowChoice"></span>
                                    <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <div class="admin-co-dd-menu" x-show="openBlow" x-cloak>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': blowChoice === 'Yes' }" @click="blowChoice = 'Yes'; openBlow = false">Yes</button>
                                    <button type="button" class="admin-co-dd-option" :class="{ 'is-active': blowChoice === 'No' }" @click="blowChoice = 'No'; openBlow = false">No</button>
                                </div>
                            </div>
                            <input type="text" class="admin-co-grooming-edit-input" x-model="blowNote" aria-label="Blow dry notes">
                        </div>
                    </div>

                    <div class="admin-co-grooming-edit-row is-notes">
                        <label class="admin-co-grooming-edit-label" :for="'groom-notes-' + @js($pet['id'])">Handling notes</label>
                        <textarea :id="'groom-notes-' + @js($pet['id'])" class="admin-co-grooming-edit-textarea" rows="3" x-model="handlingNotes"></textarea>
                    </div>

                    <div class="admin-co-grooming-edit-row">
                        <label class="admin-co-grooming-edit-label" :for="'groom-products-' + @js($pet['id'])">Products</label>
                        <input :id="'groom-products-' + @js($pet['id'])" type="text" class="admin-co-grooming-edit-input" x-model="products">
                    </div>
                </div>
            </div>
        </section>

        {{-- Booking History --}}
        @php
        $petBookings = $pet['bookings'] ?? [];
        $bookingsTotal = $pet['bookings_total'] ?? count($petBookings);
        @endphp
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Booking History">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('bookings')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                    </svg>
                    View All {{ $bookingsTotal }}
                </button>
            </x-admin.customer.section-header>
            <div class="admin-table-wrap">
                <table class="admin-live-table admin-co-bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Service · Groomer / Space Host</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($petBookings as $booking)
                        <tr>
                            <td>{{ $booking['id'] }}</td>
                            <td>
                                <span class="admin-co-booking-service">{{ $booking['service'] }}</span>
                                <span class="admin-co-booking-sep">·</span>
                                <span class="admin-co-booking-provider">{{ $booking['provider'] }}</span>
                            </td>
                            <td>{{ $booking['date'] }}</td>
                            <td>{{ $booking['amount'] }}</td>
                            <td>
                                <span class="admin-co-pet-stars" aria-label="{{ $booking['rating'] }} out of 5 stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                        <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="{{ $i <= ($booking['rating'] ?? 0) ? '#FFC97A' : '#D4D2CF' }}" />
                                        </svg>
                                        @endfor
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="admin-co-panel-footnote mb-0">
                Showing {{ count($petBookings) }} of {{ $bookingsTotal }} booking sessions
                @if (($pet['avg_rating'] ?? '—') !== '—')
                · avg. rating {{ $pet['avg_rating'] }}
                @endif
            </p>
        </section>

        {{-- Admin Notes --}}
        @php $petNotes = $pet['notes'] ?? []; @endphp
        <section
            class="admin-card admin-co-panel"
            :class="{ 'is-editing': editingNotes }"
            x-data="{
                editingNotes: false,
                newNote: '',
                editIndex: -1,
                notes: @js($petNotes),
            }">
            <div x-show="!editingNotes">
                <x-admin.customer.section-header title="Admin notes">
                    <button type="button" class="admin-co-link-btn" @click="editingNotes = true; newNote = ''; editIndex = -1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Add Notes
                    </button>
                </x-admin.customer.section-header>
                <div class="admin-co-notes">
                    <template x-for="(note, index) in notes" :key="index">
                        <article class="admin-co-note">
                            <p class="admin-co-note-text" x-text="note.text"></p>
                            <div class="admin-co-note-foot">
                                <span x-text="note.author"></span>
                                <span class="admin-co-note-dot">·</span>
                                <span x-text="note.time"></span>
                                <span class="admin-co-note-dot" x-show="note.tag" x-cloak>·</span>
                                <span class="admin-co-note-tag" x-show="note.tag" x-cloak x-text="note.tag"></span>
                            </div>
                        </article>
                    </template>
                    <p class="admin-section-label mb-0" x-show="notes.length === 0" x-cloak>No notes yet.</p>
                </div>
            </div>

            <div x-show="editingNotes" x-cloak>
                <div class="admin-co-section-head admin-co-notes-edit-head">
                    <h3 class="admin-co-section-title">Admin notes</h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="editingNotes = false; newNote = ''; editIndex = -1">Cancel</button>
                        <button type="button" class="admin-co-form-btn is-save" @click="editingNotes = false; newNote = ''; editIndex = -1">Save changes</button>
                    </div>
                </div>

                <div class="admin-co-note-compose">
                    <label class="admin-co-note-compose-label" for="pet-note-{{ $profile['id'] }}-{{ $pet['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                    <textarea
                        id="pet-note-{{ $profile['id'] }}-{{ $pet['id'] }}"
                        class="admin-co-note-textarea"
                        rows="4"
                        placeholder="Write an internal note about this pet - only visible to the admin team members."
                        x-model="newNote"
                        x-ref="noteInput"></textarea>
                    <div class="admin-co-note-compose-actions">
                        <button
                            type="button"
                            class="admin-co-form-btn is-add-note"
                            @click="
                                newNote.trim() && (
                                    editIndex >= 0
                                        ? (notes[editIndex].text = newNote.trim(), editIndex = -1, newNote = '')
                                        : (notes.unshift({ text: newNote.trim(), author: 'Admin', time: 'Just now', tag: 'Internal only' }), newNote = '')
                                )
                            "
                            x-text="editIndex >= 0 ? 'Update note' : 'Add note'"></button>
                    </div>
                </div>

                <div class="admin-co-notes admin-co-notes-edit">
                    <template x-for="(note, index) in notes" :key="index">
                        <article class="admin-co-note" :class="{ 'is-editing-note': editIndex === index }">
                            <p class="admin-co-note-text" x-text="note.text"></p>
                            <div class="admin-co-note-foot">
                                <span x-text="note.author"></span>
                                <span class="admin-co-note-dot">·</span>
                                <span x-text="note.time"></span>
                                <span class="admin-co-note-dot" x-show="note.tag" x-cloak>·</span>
                                <span class="admin-co-note-tag" x-show="note.tag" x-cloak x-text="note.tag"></span>
                            </div>
                            <div class="admin-co-note-actions">
                                <button type="button" class="admin-co-note-action" @click="newNote = note.text; editIndex = index; $refs.noteInput.focus()">Edit</button>
                                <button
                                    type="button"
                                    class="admin-co-note-action"
                                    @click="
                                        notes.splice(index, 1);
                                        editIndex === index && (editIndex = -1, newNote = '');
                                        editIndex > index && (editIndex = editIndex - 1);
                                    ">Delete</button>
                            </div>
                        </article>
                    </template>
                </div>
            </div>
        </section>

        {{-- Recent Activity --}}
        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    Full Log
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-activity">
                @foreach ($pet['activity'] ?? [] as $event)
                <li class="admin-co-activity-item">
                    <span class="admin-co-activity-dot is-{{ $event['type'] }}" aria-hidden="true"></span>
                    <div class="admin-co-activity-body">
                        <p class="admin-co-activity-title">{{ $event['title'] }}</p>
                        <time class="admin-co-activity-time">{{ $event['time'] }}</time>
                    </div>
                </li>
                @endforeach
            </ul>
        </section>
    </div>
    @endforeach
</div>