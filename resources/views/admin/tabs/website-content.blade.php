@php
$wcSections = [
['id' => 'homepage', 'label' => 'Homepage & Marketing'],
['id' => 'static', 'label' => 'Static Pages'],
['id' => 'help', 'label' => 'Help Centre'],
['id' => 'legal', 'label' => 'Legal & Policy Documents'],
['id' => 'footer', 'label' => 'Global Footer'],
];

$editingTargets = [
['id' => 'customer', 'label' => 'Customer Homepage - Pet Owners', 'url' => 'fursgo.com/'],
['id' => 'groomers', 'label' => 'For Groomers & Hosts', 'url' => 'fursgo.com/'],
['id' => 'business', 'label' => 'Business Homepage - FursGo Business', 'url' => 'fursgo.com/'],
];

$groomersBanner = [
'headline' => 'Set your own schedule, pricing & preferences.',
'description' => [
'Join thousands of professionals earning more with FursGo.',
'You control when you work, what you charge, and who you serve.',
],
'photo' => 'images/business-homepage-groomer-space-owner-hero-section.png',
];

$businessBanner = [
'headline' => 'Step into a new opportunity — built around your love for animals.',
'description' => 'Begin your application.',
'photo' => 'images/blp-hero-section-imagerectangle-293.png',
];

$featureItems = [
['title' => 'Booked in Minutes', 'desc' => 'No calls, no quotes.'],
['title' => 'Trusted Professionals', 'desc' => 'Verified & insured.'],
['title' => 'Happy Pets', 'desc' => 'Gentle grooming, stress-free service.'],
];

$howItWorks = [
[
'title' => 'Search',
'desc' => 'No more guessing or endless calling around. We help you find the right groomer with total confidence.',
'icon' => 'images/admin/website-content/how-search.png',
],
[
'title' => 'Book & Pay',
'desc' => 'Simple, secure, and built to save you time. Book in seconds, pay safely, and manage everything in one place.',
'icon' => 'images/admin/website-content/how-book-pay.png',
],
[
'title' => 'Relax',
'desc' => 'Calm, clear, and stress-free appointments so your pet feels comfortable and you stay informed.',
'icon' => 'images/admin/website-content/how-relax.png',
],
];

$popularServices = [
[
'title' => 'Full Groom',
'desc' => 'Bath, trim, blow-dry, and essential hygiene.',
'image' => 'images/admin/provider/gallery/1.jpg',
],
[
'title' => 'Bath & Tidy',
'desc' => 'A quick refresh to keep your pet clean between full grooms.',
'image' => 'images/admin/provider/gallery/2.jpg',
],
[
'title' => 'Nail Trim',
'desc' => 'Quality and careful nail care focused on comfort and safety.',
'image' => 'images/admin/provider/gallery/3.jpg',
],
[
'title' => 'Pet Spa',
'desc' => 'Soothing coat and skin treatments to nourish & refresh.',
'image' => null,
],
[
'title' => 'Mobile Grooming',
'desc' => 'Grooming at home, for a stress-free experience.',
'image' => null,
],
];

$testimonials = [
[
'name' => 'Sarah M.',
'when' => '1 day ago',
'quote' => 'The easiest booking I\'ve ever made! My dog looked amazing and the groomer was so kind.',
'rating' => 5,
'avatar' => 'images/profile_image.png',
],
[
'name' => 'Sarah M.',
'when' => '1 day ago',
'quote' => 'The easiest booking I\'ve ever made! My dog looked amazing and the groomer was so kind.',
'rating' => 5,
'avatar' => null,
],
];

$cities = ['London', 'Manchester', 'Birmingham', 'Leeds', 'Bristol', 'Edinburgh'];

$versionHistory = [
['text' => 'Ben M published Hero Section update', 'when' => '18 Apr 2025 · 14:32'],
['text' => 'Michelle M published Testimonials', 'when' => '18 Apr 2025 · 11:32'],
];
@endphp

<div
    class="admin-website-content"
    x-data="{
        section: 'homepage',
        editingOpen: false,
        editingId: 'customer',
        targets: @js($editingTargets),
        get editingLabel() {
            const t = this.targets.find(x => x.id === this.editingId);
            return t ? t.label : '';
        },
        get editingUrl() {
            const t = this.targets.find(x => x.id === this.editingId);
            return t ? t.url : '';
        },
        selectEditing(id) {
            this.editingId = id;
            this.editingOpen = false;
        }
    }"
    @keydown.escape.window="editingOpen = false">
    <div class="admin-wc-separator" aria-hidden="true"></div>

    <div class="admin-po-sections-bar admin-wc-sections-bar">
        <nav class="admin-po-sections admin-wc-sections" aria-label="Website content sections">
            @foreach ($wcSections as $item)
            <button
                type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === @js($item['id']) }"
                @click="section = @js($item['id']); editingOpen = false">{{ $item['label'] }}</button>
            @endforeach
        </nav>
    </div>

    {{-- Homepage & Marketing --}}
    <div class="admin-wc-panel" x-show="section === 'homepage'" x-cloak>
        <div class="admin-wc-head">
            <div class="admin-wc-head-top">
                <h1 class="admin-page-title mb-0">Homepage &amp; Marketing</h1>
            </div>

            <div class="admin-wc-editing-actions-row">
                <div class="admin-wc-editing">
                    <span class="admin-wc-editing-label">Editing:</span>
                    <div class="admin-wc-editing-wrap" @click.outside="editingOpen = false">
                        <button
                            type="button"
                            class="admin-wc-editing-btn"
                            :aria-expanded="editingOpen.toString()"
                            @click="editingOpen = !editingOpen">
                            <span x-text="editingLabel"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="admin-wc-editing-menu" x-show="editingOpen" x-cloak x-transition.opacity.duration.150ms>
                            <template x-for="item in targets" :key="item.id">
                                <button
                                    type="button"
                                    class="admin-wc-editing-option"
                                    :class="{ 'is-active': editingId === item.id }"
                                    @click="selectEditing(item.id)"
                                    x-text="item.label"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="admin-wc-head-actions">
                    <button type="button" class="admin-btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                            <path d="M8.5948 0.545867C8.63921 0.647012 8.66142 0.76769 8.66142 0.907901L8.66223 5.11344C8.66223 5.2697 8.61085 5.40242 8.5081 5.5116C8.40535 5.62077 8.26755 5.67723 8.09469 5.68097C7.91702 5.68097 7.77627 5.63067 7.67245 5.53006C7.5681 5.42891 7.51619 5.28977 7.51672 5.11263L7.51672 1.94825L0.974419 8.49055C0.860431 8.60454 0.726642 8.6618 0.573051 8.66234C0.419461 8.66287 0.285671 8.60561 0.171682 8.49055C0.0576938 8.37549 0.00043188 8.2417 -0.00010329 8.08918C-0.000638452 7.93666 0.0566235 7.80287 0.171682 7.68781L6.71398 1.14551L3.5496 1.14551C3.39333 1.14551 3.25954 1.0936 3.14823 0.989779C3.03692 0.885959 2.98153 0.745479 2.98206 0.568342C2.98474 0.394415 3.03584 0.256078 3.13538 0.153328C3.23492 0.0505781 3.37273 -0.000530905 3.54879 4.17661e-06L7.75513 5.50792e-06C7.89427 5.49619e-06 8.01468 0.0224822 8.11636 0.067435C8.21751 0.111853 8.31143 0.17741 8.39813 0.264106C8.48482 0.350801 8.55038 0.444722 8.5948 0.545867Z" fill="#3B3731" />
                        </svg>
                        Preview
                    </button>
                    <button type="button" class="admin-btn-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                            <path d="M3.25 1.6337C2.40259 2.23179 1.76944 2.95369 1.35056 3.7994C0.931667 4.6451 0.722222 5.5452 0.722222 6.49969C0.722222 7.85833 1.12041 9.06609 1.91678 10.123C2.71315 11.1799 3.76928 11.8811 5.08517 12.2266C5.18194 12.2567 5.26139 12.3145 5.3235 12.4002C5.38561 12.4863 5.41667 12.5803 5.41667 12.6822C5.41667 12.7841 5.37815 12.8676 5.30111 12.9325C5.22407 12.9965 5.14318 13.015 5.05844 12.9879C3.56778 12.6301 2.35204 11.8555 1.41122 10.6642C0.470407 9.47294 0 8.08477 0 6.49969C0 5.43641 0.22437 4.4477 0.673111 3.53358C1.12185 2.61945 1.79472 1.81879 2.69172 1.13159H0.902778C0.800222 1.13159 0.714278 1.09615 0.644944 1.02526C0.575611 0.954379 0.541185 0.866757 0.541667 0.762397C0.542148 0.658038 0.576574 0.57017 0.644944 0.498792C0.713315 0.427414 0.799259 0.392218 0.902778 0.393202H3.38867C3.55622 0.393202 3.69537 0.449812 3.80611 0.563032C3.91685 0.676252 3.97222 0.818269 3.97222 0.989083V3.53136C3.97222 3.63621 3.9378 3.72408 3.86894 3.79497C3.80009 3.86585 3.71415 3.90105 3.61111 3.90056C3.50807 3.90006 3.42213 3.86487 3.35328 3.79497C3.28443 3.72506 3.25 3.6372 3.25 3.53136V1.6337ZM5.47083 8.33607L9.31378 4.40635C9.38407 4.33448 9.46689 4.29683 9.56222 4.29338C9.65756 4.29043 9.74518 4.32981 9.82511 4.41152C9.90215 4.49225 9.94115 4.57914 9.94211 4.67217C9.94307 4.76521 9.90407 4.85234 9.82511 4.93356L5.87961 8.96739C5.76261 9.08701 5.62635 9.14682 5.47083 9.14682C5.31531 9.14682 5.1793 9.08701 5.06278 8.96739L3.17489 7.03355C3.10748 6.96463 3.07065 6.8807 3.06439 6.78175C3.05813 6.68281 3.09496 6.59273 3.17489 6.5115C3.25193 6.43274 3.33715 6.39336 3.43056 6.39336C3.52396 6.39336 3.60919 6.43274 3.68622 6.5115L5.47083 8.33607ZM10.3083 11.8818H12.0972C12.2003 11.8818 12.2862 11.917 12.3551 11.9874C12.4239 12.0578 12.4583 12.1457 12.4583 12.251C12.4583 12.3564 12.4239 12.444 12.3551 12.5139C12.2862 12.5838 12.2003 12.6192 12.0972 12.6202H9.61133C9.4433 12.6202 9.30415 12.5636 9.19389 12.4504C9.08315 12.3372 9.02778 12.1949 9.02778 12.0236V9.48131C9.02778 9.37646 9.0622 9.28884 9.13105 9.21844C9.19991 9.14805 9.28585 9.11261 9.38889 9.11211C9.49193 9.11162 9.57787 9.14707 9.64672 9.21844C9.71557 9.28982 9.75 9.37744 9.75 9.48131V11.3797C10.5878 10.7693 11.2185 10.042 11.6422 9.19777C12.0659 8.35354 12.2778 7.45418 12.2778 6.49969C12.2778 5.14105 11.8796 3.93329 11.0832 2.87641C10.2869 1.81953 9.23096 1.1183 7.91556 0.772735C7.8183 0.742707 7.73861 0.687082 7.6765 0.605859C7.61439 0.525128 7.58333 0.433567 7.58333 0.331177C7.58333 0.228787 7.62185 0.143134 7.69889 0.0742177C7.77593 0.00530128 7.85681 -0.0156198 7.94156 0.0114545C9.43222 0.359975 10.648 1.13233 11.5888 2.32852C12.5296 3.52472 13 4.9151 13 6.49969C13 7.56297 12.7756 8.55414 12.3269 9.47319C11.8781 10.3922 11.2053 11.1951 10.3083 11.8818Z" fill="#FDFCF8" />
                        </svg>
                        Publish
                    </button>
                </div>
            </div>

            <div class="admin-wc-status-row">
                <p class="admin-wc-live">
                    <span class="admin-wc-live-dot" aria-hidden="true"></span>
                    <strong>Live</strong>
                    <span class="admin-wc-live-meta">Last published 6 days ago by Ben M</span>
                </p>
                <p class="admin-wc-url" x-text="editingUrl"></p>
            </div>
        </div>

        {{-- Customer Homepage - Pet Owners --}}
        <div class="admin-wc-stack" x-show="editingId === 'customer'" x-cloak>
            {{-- Hero Section --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Hero Section</h2>
                        <p class="admin-wc-card-sub">Headline shown above the search widget</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Book trusted pet grooming in minutes!</p>
                </div>

                <div class="admin-wc-trust-row">
                    <div class="admin-wc-trust">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none" aria-hidden="true">
                            <circle cx="15" cy="15" r="14.5" fill="white" stroke="#F6F6F6" />
                            <path d="M14.0674 8.20765C14.3609 7.26411 15.6391 7.26412 15.9326 8.20766L17.0309 11.7377C17.1622 12.1597 17.5387 12.4454 17.9635 12.4454H21.5175C22.4675 12.4454 22.8624 13.7149 22.0939 14.298L19.2186 16.4797C18.8749 16.7405 18.7311 17.2027 18.8624 17.6247L19.9607 21.1548C20.2542 22.0983 19.2202 22.8829 18.4516 22.2998L15.5764 20.1181C15.2327 19.8573 14.7673 19.8573 14.4236 20.1181L11.5484 22.2998C10.7798 22.8829 9.74579 22.0983 10.0393 21.1548L11.1376 17.6247C11.2689 17.2027 11.1251 16.7405 10.7814 16.4797L7.9061 14.298C7.13758 13.7149 7.53255 12.4454 8.4825 12.4454H12.0365C12.4613 12.4454 12.8379 12.1597 12.9691 11.7377L14.0674 8.20765Z" fill="#FFC97A" />
                        </svg>
                        <div class="admin-wc-trust-copy">
                            <p>4.9 average rating from 5,000 + pet owners.</p>
                            <p>All groomers are ID-verified and insured through Fursgo Protect.</p>
                        </div>
                    </div>
                    <span class="admin-wc-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none" aria-hidden="true">
                            <path d="M3 6V4.5C3 2.84315 4.34315 1.5 6 1.5C7.65685 1.5 9 2.84315 9 4.5V6M2.5 6H9.5C10.0523 6 10.5 6.44772 10.5 7V11.5C10.5 12.0523 10.0523 12.5 9.5 12.5H2.5C1.94772 12.5 1.5 12.0523 1.5 11.5V7C1.5 6.44772 1.94772 6 2.5 6Z" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Auto-calculated
                    </span>
                </div>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Hero Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <img
                    src="{{ asset('images/hero_section.png') }}"
                    alt="Hero photo preview"
                    class="admin-wc-thumb admin-wc-thumb-lg"
                    width="160"
                    height="110">
            </section>

            {{-- Feature Highlight --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Feature Highlight</h2>
                        <p class="admin-wc-card-sub">Lorem ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Groom from anywhere. Book from anywhere. Earn from anywhere.</p>
                </div>

                <ul class="admin-wc-list">
                    @foreach ($featureItems as $item)
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-check" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <circle cx="15" cy="15" r="14.5" fill="white" stroke="#F6F6F6" />
                                <path d="M15 5.00012C9.5 5.00012 5 9.50012 5 15.0001C5 20.5001 9.5 25.0001 15 25.0001C20.5 25.0001 25 20.5001 25 15.0001C25 9.50012 20.5 5.00012 15 5.00012ZM13 20.0001L8 15.0001L9.41 13.5901L13 17.1701L20.59 9.58012L22 11.0001L13 20.0001Z" fill="#D8E8B7" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">{{ $item['title'] }}</p>
                            <p class="admin-wc-list-desc">{{ $item['desc'] }}</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit {{ $item['title'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </li>
                    @endforeach
                </ul>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <img
                    src="{{ asset('images/pet_details_2.png') }}"
                    alt="Banner photo preview"
                    class="admin-wc-thumb"
                    width="96"
                    height="96">
            </section>

            {{-- Banner 1 --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 1</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">How it works</p>
                    <p class="admin-wc-preview-body">Finding the right groomer shouldn't be overwhelming. We've made it simple, safe, and stress-free — so you can book with confidence in just a few steps.</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list">
                    @foreach ($howItWorks as $item)
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-step-icon" aria-hidden="true">
                            <img src="{{ asset($item['icon']) }}" alt="" width="18" height="18">
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">{{ $item['title'] }}</p>
                            <p class="admin-wc-list-desc">{{ $item['desc'] }}</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit {{ $item['title'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </section>

            {{-- Banner 2 --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 2</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Popular Services</p>
                    <p class="admin-wc-preview-body">Our most trusted services — chosen by owners who want comfort, reliability, and peace of mind.</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list">
                    @foreach ($popularServices as $item)
                    <li class="admin-wc-list-item">
                        @if ($item['image'])
                        <img src="{{ asset($item['image']) }}" alt="" class="admin-wc-service-thumb" width="30" height="30">
                        @else
                        <span class="admin-wc-service-placeholder" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                <rect x="0.5" y="0.5" width="29" height="29" rx="4.5" fill="#F3F3F3" stroke="#E2E2E2" />
                                <path d="M18.713 15.4615C18.713 15.7225 18.5015 15.934 18.2405 15.934H15.857V18.4365C15.857 18.7168 15.6298 18.944 15.3495 18.944C15.0692 18.944 14.842 18.7168 14.842 18.4365V15.934H12.4725C12.2115 15.934 12 15.7225 12 15.4615C12 15.2005 12.2115 14.989 12.4725 14.989H14.842V12.5075C14.842 12.2272 15.0692 12 15.3495 12C15.6298 12 15.857 12.2272 15.857 12.5075V14.989H18.2405C18.5015 14.989 18.713 15.2005 18.713 15.4615Z" fill="#E2E2E2" />
                            </svg>
                        </span>
                        @endif
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">{{ $item['title'] }}</p>
                            <p class="admin-wc-list-desc">{{ $item['desc'] }}</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit {{ $item['title'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </li>
                    @endforeach
                </ul>
            </section>

            {{-- Testimonials --}}
            <section class="admin-wc-card is-testimonials">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Testimonials</h2>
                        <p class="admin-wc-card-sub">5 published · 0 pending review</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Loved by Pet Parents</p>
                    <p class="admin-wc-preview-body">Your pet's safety comes first. Every groomer is background-checked, insured, and trained in animal handling.</p>
                </div>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-subhead">
                    <p class="admin-wc-card-title mb-0">Feature Testimonial</p>
                    <button type="button" class="admin-wc-add-link">+ Add testimonial</button>
                </div>

                <div class="admin-wc-testimonial-list">
                    @foreach ($testimonials as $item)
                    @if (! $loop->first)
                    <div class="admin-wc-divider" aria-hidden="true"></div>
                    @endif
                    <article class="admin-wc-testimonial">
                        <div class="admin-wc-testimonial-top">
                            <div class="admin-wc-testimonial-who">
                                @if ($item['avatar'])
                                <span class="admin-wc-avatar-wrap">
                                    <img src="{{ asset($item['avatar']) }}" alt="" class="admin-wc-avatar" width="40" height="40">
                                </span>
                                @else
                                <span class="admin-wc-avatar-wrap is-empty" aria-hidden="true">
                                    <span class="admin-wc-avatar-initial">{{ substr($item['name'], 0, 1) }}</span>
                                </span>
                                @endif
                                <div>
                                    <p class="admin-wc-testimonial-name">{{ $item['name'] }}</p>
                                    <p class="admin-wc-testimonial-when">{{ $item['when'] }}</p>
                                </div>
                            </div>
                            <span class="admin-po-status is-active">Published</span>
                        </div>
                        <p class="admin-wc-testimonial-quote">“{{ $item['quote'] }}”</p>
                        <div class="admin-wc-testimonial-foot">
                            <div class="admin-wc-stars" aria-label="{{ $item['rating'] }} out of 5 stars">
                                @for ($i = 0; $i < $item['rating']; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                    <path d="M7 1.2L8.7 4.6L12.5 5.15L9.75 7.75L10.4 11.5L7 9.7L3.6 11.5L4.25 7.75L1.5 5.15L5.3 4.6L7 1.2Z" fill="#FFC97A" />
                                    </svg>
                                    @endfor
                                    <span>{{ $item['rating'] }} out of 5 stars</span>
                            </div>
                            <div class="admin-wc-testimonial-actions">
                                <button type="button" class="admin-wc-square-btn" aria-label="Edit testimonial">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                        <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" class="admin-wc-square-btn" aria-label="View testimonial">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none" aria-hidden="true">
                                        <path d="M1 6C1 6 3.5 1.5 8 1.5C12.5 1.5 15 6 15 6C15 6 12.5 10.5 8 10.5C3.5 10.5 1 6 1 6Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                        <circle cx="8" cy="6" r="2" stroke="#3B3731" stroke-width="1.2" />
                                    </svg>
                                </button>
                                <button type="button" class="admin-wc-square-btn" aria-label="Delete testimonial">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                        <path d="M2.5 2.5L9.5 9.5M9.5 2.5L2.5 9.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <div class="admin-wc-testimonial-footer">
                    <p class="admin-table-count">SHOWING 1–13 OF 13 TESTIMONIALS</p>
                    <nav class="admin-wc-testimonial-pager" aria-label="Testimonials pagination">
                        <button type="button" class="admin-wc-page-arrow" aria-label="Previous page" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                                <path d="M6.5 1L1.5 6L6.5 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="admin-po-page is-current">1</button>
                        <button type="button" class="admin-po-page">2</button>
                        <button type="button" class="admin-po-page">3</button>
                        <button type="button" class="admin-wc-page-arrow" aria-label="Next page">
                            <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                                <path d="M1.5 1L6.5 6L1.5 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </section>

            {{-- Banner 3 --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 3</h2>
                        <p class="admin-wc-card-sub">Lorem ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Our Mission</p>
                    <p class="admin-wc-preview-body">We’re building a kinder way to care for pets — connecting owners with trusted local groomers and spaces, with transparency, safety, and love at every step.</p>
                </div>

                <div class="admin-wc-cta">
                    <p class="admin-wc-card-title mb-0">CTA Button</p>
                    <p class="admin-wc-cta-value">About us</p>
                </div>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <img
                    src="{{ asset('images/our_mission.png') }}"
                    alt="Banner 3 photo preview"
                    class="admin-wc-thumb admin-wc-thumb-lg"
                    width="160"
                    height="110">
            </section>

            {{-- Service Map --}}
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Service Map</h2>
                        <p class="admin-wc-card-sub">6 cities live</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Fursgo across the UK</p>
                    <p class="admin-wc-preview-body">Now serving pet owners across the UK.</p>
                </div>

                <div class="admin-wc-subhead">
                    <p class="admin-wc-card-title mb-0">Cities live</p>
                    <button type="button" class="admin-wc-add-link">+ Add city</button>
                </div>

                <div class="admin-wc-city-list">
                    @foreach ($cities as $city)
                    <span class="admin-wc-city-chip">
                        {{ $city }}
                        <button type="button" class="admin-wc-city-remove" aria-label="Remove {{ $city }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                <path d="M2 2L8 8M8 2L2 8" stroke="#9C9A97" stroke-width="1.3" stroke-linecap="round" />
                            </svg>
                        </button>
                    </span>
                    @endforeach
                </div>
            </section>

            {{-- Version History --}}
            <section class="admin-wc-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link">
                        Full Log
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M4.5 2.5H2.5C1.94772 2.5 1.5 2.94772 1.5 3.5V9.5C1.5 10.0523 1.94772 10.5 2.5 10.5H8.5C9.05228 10.5 9.5 10.0523 9.5 9.5V7.5M7 1.5H10.5M10.5 1.5V5M10.5 1.5L5 7" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <ul class="admin-wc-history-list">
                    @foreach ($versionHistory as $entry)
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">{{ $entry['text'] }}</p>
                            <p class="admin-wc-history-when">{{ $entry['when'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </section>

        </div>

        <div class="admin-wc-stack admin-wc-business-extra-stack" x-show="editingId === 'business'" x-cloak>

            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 2</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Sign-Up Process Overview</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ol class="admin-wc-list admin-wc-business-signup-steps">
                    @foreach ([
                    ['title' => 'Set Up Your Services', 'desc' => 'Choose what you offer, set your rates, and define your availability.'],
                    ['title' => 'Complete Verification', 'desc' => 'Finish screening and safety checks to maintain platform standards.'],
                    ['title' => 'Create Your Profile', 'desc' => 'Share your experience, upload photos of your work, and introduce yourself professionally.'],
                    ['title' => 'Review & Go Live', 'desc' => 'Once approved, you’ll start receiving booking opportunities.'],
                    ] as $index => $step)
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-step-number" aria-hidden="true">{{ $index + 1 }}</span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">{{ $step['title'] }}</p>
                            <p class="admin-wc-list-desc">{{ $step['desc'] }}</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit {{ $step['title'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </li>
                    @endforeach
                </ol>
            </section>

            <section class="admin-wc-history admin-wc-business-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M4.5 2.5H2.5C1.94772 2.5 1.5 2.94772 1.5 3.5V9.5C1.5 10.0523 1.94772 10.5 2.5 10.5H8.5C9.05228 10.5 9.5 10.0523 9.5 9.5V7.5M7 1.5H10.5M10.5 1.5V5M10.5 1.5L5 7" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Full Log
                    </button>
                </div>
                <ul class="admin-wc-history-list">
                    @foreach ($versionHistory as $entry)
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">{{ $entry['text'] }}</p>
                            <p class="admin-wc-history-when">{{ $entry['when'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <div class="admin-wc-stack admin-wc-groomer-banner-two-stack" x-show="editingId === 'groomers'" x-cloak>
            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 2</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Guaranteed safety &amp; support</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list admin-wc-safety-benefits">
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Manage bookings, CRM, marketing &amp; payments</p>
                            <p class="admin-wc-list-desc">Automated scheduling, reminders, and secure payouts.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit bookings and payments"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Protected by FursGo Insurance</p>
                            <p class="admin-wc-list-desc">Every booking is protected with industry-leading insurance.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit insurance details"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Secure payments &amp; ID verification</p>
                            <p class="admin-wc-list-desc">No cash handling. Payments are guaranteed and on time.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit payment and verification details"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Dedicated support team</p>
                            <p class="admin-wc-list-desc">Real support from humans &mdash; whenever you need it.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit support details"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Hero Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <div class="admin-wc-safety-thumb">
                    <img src="{{ asset('images/admin/website-content/safety-support-shield.svg') }}" alt="Guaranteed safety and support shield" width="102" height="100">
                </div>
            </section>

            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 3</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Professional tools &amp; CRM</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list admin-wc-safety-benefits">
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Professional CRM tools</p>
                            <p class="admin-wc-list-desc">Send follow-ups, manage clients, and recover abandoned bookings.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit professional CRM tools"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Real-time analytics dashboard</p>
                            <p class="admin-wc-list-desc">Track earnings, returning customers, and growth instantly.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit analytics dashboard"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>

            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 4</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">How it works</p>
                    <p class="admin-wc-preview-body admin-wc-how-subtitle">A simple 3-step process to start earning.</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ol class="admin-wc-list admin-wc-how-steps">
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-step-number" aria-hidden="true">1</span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Create your profile -</p>
                            <p class="admin-wc-step-summary">Build your profile with FursGo&rsquo;s onboarding flow.</p>
                            <p class="admin-wc-list-desc">Build a professional profile in minutes. Add photos, services, prices, and availability.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit create your profile step"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-step-number" aria-hidden="true">2</span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Accept Bookings -</p>
                            <p class="admin-wc-step-summary">Set your availability and choose clients.</p>
                            <p class="admin-wc-list-desc">Fill your calendar with bookings from real customers. Decide when you want to work and who you accept.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit accept bookings step"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-step-number" aria-hidden="true">3</span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Get paid -</p>
                            <p class="admin-wc-step-summary">Secure payouts straight to your bank.</p>
                            <p class="admin-wc-list-desc">Fast, secure payouts directly to your bank. Paid automatically after each completed booking.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit get paid step"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ol>
            </section>

            <section class="admin-wc-card is-testimonials admin-wc-groomer-testimonials">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Testimonials</h2>
                        <p class="admin-wc-card-sub">5 published &middot; 0 pending review</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Testimonials &amp; trust</p>
                    <p class="admin-wc-preview-body admin-wc-how-subtitle">&rarr; Hear how providers are earning more with FursGo.</p>
                </div>

                <div class="admin-wc-subhead admin-wc-testimonial-add-row">
                    <span aria-hidden="true"></span>
                    <button type="button" class="admin-wc-add-link">+&nbsp; Add testimonial</button>
                </div>

                <article class="admin-wc-testimonial">
                    <div class="admin-wc-testimonial-top">
                        <div class="admin-wc-testimonial-who">
                            <span class="admin-wc-avatar-wrap">
                                <img src="{{ asset($testimonials[0]['avatar']) }}" alt="" class="admin-wc-avatar" width="40" height="40">
                            </span>
                            <div>
                                <p class="admin-wc-testimonial-name">{{ $testimonials[0]['name'] }}</p>
                                <p class="admin-wc-testimonial-when">{{ $testimonials[0]['when'] }}</p>
                            </div>
                        </div>
                        <span class="admin-po-status is-active">Published</span>
                    </div>

                    <p class="admin-wc-testimonial-quote">&ldquo;{{ $testimonials[0]['quote'] }}&rdquo;</p>

                    <div class="admin-wc-testimonial-foot">
                        <div class="admin-wc-stars" aria-label="{{ $testimonials[0]['rating'] }} out of 5 stars">
                            @for ($i = 0; $i < $testimonials[0]['rating']; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                <path d="M7 1.2L8.7 4.6L12.5 5.15L9.75 7.75L10.4 11.5L7 9.7L3.6 11.5L4.25 7.75L1.5 5.15L5.3 4.6L7 1.2Z" fill="#FFC97A" /></svg>
                                @endfor
                                <span>{{ $testimonials[0]['rating'] }} out of 5 stars</span>
                        </div>
                        <div class="admin-wc-testimonial-actions">
                            <button type="button" class="admin-wc-square-btn" aria-label="Edit testimonial">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                    <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" class="admin-wc-square-btn" aria-label="View testimonial">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none" aria-hidden="true">
                                    <path d="M1 6C1 6 3.5 1.5 8 1.5C12.5 1.5 15 6 15 6C15 6 12.5 10.5 8 10.5C3.5 10.5 1 6 1 6Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                    <circle cx="8" cy="6" r="2" stroke="#3B3731" stroke-width="1.2" />
                                </svg>
                            </button>
                            <button type="button" class="admin-wc-square-btn" aria-label="Delete testimonial">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <path d="M2.5 2.5L9.5 9.5M9.5 2.5L2.5 9.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </article>

                <div class="admin-wc-testimonial-footer">
                    <p class="admin-table-count">SHOWING 1&ndash;13 OF 13 TESTIMONIALS</p>
                    <nav class="admin-wc-testimonial-pager" aria-label="Testimonials pagination">
                        <button type="button" class="admin-wc-page-arrow" aria-label="Previous page" disabled><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                                <path d="M6.5 1L1.5 6L6.5 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                        <button type="button" class="admin-po-page is-current">1</button>
                        <button type="button" class="admin-po-page">2</button>
                        <button type="button" class="admin-po-page">3</button>
                        <button type="button" class="admin-wc-page-arrow" aria-label="Next page"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                                <path d="M1.5 1L6.5 6L1.5 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </nav>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-groomer-banner admin-wc-service-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 5</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">Service categories</p>
                    <p class="admin-wc-preview-body admin-wc-how-subtitle">Earning potential varies by service and location. Top groomers earn &pound;2,000&ndash;&pound;4,000/month</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list admin-wc-service-category-list">
                    <li class="admin-wc-list-item">
                        <img src="{{ asset('images/servecies-rectangle-325.png') }}" alt="" class="admin-wc-service-thumb" width="30" height="30">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Grooming</p>
                            <p class="admin-wc-list-desc">Full grooming services including bathing, trimming, styling, and coat care.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Grooming"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <img src="{{ asset('images/services-rectangle-331.png') }}" alt="" class="admin-wc-service-thumb" width="30" height="30">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Boarding</p>
                            <p class="admin-wc-list-desc">Overnight or short-term care for pets in a safe, home-style environment.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Boarding"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <img src="{{ asset('images/services-rectangle 330.png') }}" alt="" class="admin-wc-service-thumb" width="30" height="30">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Daycare</p>
                            <p class="admin-wc-list-desc">Daytime care for dogs while owners are at work or away.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Daycare"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-service-placeholder" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M6 2.5V9.5M2.5 6H9.5" stroke="#D4D4D4" stroke-width="1" stroke-linecap="round" />
                            </svg></span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Training</p>
                            <p class="admin-wc-list-desc">Basic obedience or behaviour-focused training sessions.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Training"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>

            <section class="admin-wc-history admin-wc-groomer-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M4.5 2.5H2.5C1.94772 2.5 1.5 2.94772 1.5 3.5V9.5C1.5 10.0523 1.94772 10.5 2.5 10.5H8.5C9.05228 10.5 9.5 10.0523 9.5 9.5V7.5M7 1.5H10.5M10.5 1.5V5M10.5 1.5L5 7" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Full Log
                    </button>
                </div>
                <ul class="admin-wc-history-list">
                    @foreach ($versionHistory as $entry)
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">{{ $entry['text'] }}</p>
                            <p class="admin-wc-history-when">{{ $entry['when'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- For Groomers & Hosts --}}
        <div class="admin-wc-stack admin-wc-groomer-main-stack" x-show="editingId === 'groomers'" x-cloak>
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">{{ $groomersBanner['headline'] }}</p>
                </div>

                <div class="admin-wc-cta">
                    <p class="admin-wc-card-title mb-0">Description</p>
                    <div class="admin-wc-preview admin-wc-hero-preview">
                        @foreach ($groomersBanner['description'] as $line)
                        <p class="admin-wc-preview-body">{{ $line }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Hero Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <img
                    src="{{ asset($groomersBanner['photo']) }}"
                    alt="Groomers hero photo preview"
                    class="admin-wc-thumb admin-wc-thumb-lg"
                    width="160"
                    height="110">
            </section>

            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 1</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Control &amp; flexibility</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list admin-wc-groomer-benefits">
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20" viewBox="0 0 22 20" fill="none">
                                <path d="M0.550049 9.6444C0.550049 6.03458 0.550049 4.22919 1.72205 3.10824C2.89405 1.98729 4.77905 1.98633 8.55005 1.98633H12.55C16.321 1.98633 18.207 1.98633 19.378 3.10824C20.549 4.23014 20.55 6.03458 20.55 9.6444V11.5589C20.55 15.1687 20.55 16.9741 19.378 18.0951C18.206 19.216 16.321 19.217 12.55 19.217H8.55005C4.77905 19.217 2.89305 19.217 1.72205 18.0951C0.551049 16.9732 0.550049 15.1687 0.550049 11.5589V9.6444Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M1.35147 7.50293C1.31067 8.23824 1.31067 9.11304 1.31067 10.164V11.8263C1.31067 14.9606 1.31159 16.5273 2.39294 17.5014C3.4743 18.4756 5.21591 18.4756 8.69821 18.4756H12.392C15.8743 18.4756 17.615 18.4747 18.6973 17.5014C19.7795 16.5282 19.7795 14.9606 19.7795 11.8263V10.164C19.7795 9.11295 19.7794 8.23819 19.7385 7.50293H1.35147Z" fill="#FFC97A" fill-opacity="0.125" />
                                <path d="M7.28589 12.7237L9.59677 15.0346L13.7564 10.875L13.1047 10.2188L9.59677 13.7266L7.93756 12.0721L7.28589 12.7237Z" fill="#FFC97A" />
                                <path d="M5.39897 1.97884V0.549805M15.6912 1.97884V0.549805M0.767456 6.74228H20.3227" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Set your own schedule &amp; prices.</p>
                            <p class="admin-wc-list-desc">Work on your terms, choose your availability and rates.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit schedule and prices">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path d="M12.7197 0.893623C14.2917 0.371964 15.8424 0.386176 16.8001 1.33969C16.8028 1.34236 16.8058 1.3452 16.8084 1.34793L18.0841 2.68527C18.0959 2.6977 18.1066 2.71092 18.1167 2.72428C18.1373 2.73873 18.1571 2.75495 18.1755 2.77327C19.1332 3.7269 19.1475 5.27075 18.6235 6.83601C18.09 8.42943 16.9587 10.1945 15.3721 11.774C13.7857 13.3537 12.013 14.4802 10.4126 15.0113C8.94761 15.4975 7.50172 15.5176 6.53641 14.7465C6.44185 14.7296 6.35163 14.6832 6.28259 14.6064L4.9477 13.1222C3.99924 12.1678 3.98697 10.6291 4.50927 9.06895C5.04275 7.47556 6.17369 5.71046 7.76027 4.13098C9.34667 2.55125 11.1194 1.42475 12.7197 0.893623Z" fill="white" />
                                <path d="M6.27943 14.3679C7.88596 15.9744 11.7933 14.6723 15.0063 11.4587C18.2199 8.24566 19.522 4.33833 17.9155 2.7318M11.0065 1.64054L11.7336 2.36822M8.46141 4.18611L9.18856 4.91327M6.27891 7.09526L7.00607 7.82242M5.55176 10.7316L6.27891 11.4587M15.0063 0.549805L15.7335 1.27696M14.2792 4.91379L15.7335 6.3681M11.7341 7.45936L13.1884 8.91368M8.82499 9.64083L10.2793 11.0952" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6.18184 16.5501C6.78433 15.9476 6.78433 14.9707 6.18184 14.3682C5.57935 13.7658 4.60253 13.7658 4.00004 14.3682L1.09098 17.2773C0.488495 17.8798 0.488495 18.8566 1.09098 19.4591C1.69347 20.0616 2.67029 20.0616 3.27278 19.4591L6.18184 16.5501Z" fill="#FFF8EE" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Choose the services you offer.</p>
                            <p class="admin-wc-list-desc">Offer full grooms, bath &amp; brush, add-ons &amp; more.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit offered services"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                <path d="M10.0004 6.63135C11.5494 6.63151 12.7109 7.46925 13.475 8.56689L13.476 8.56787C14.2392 9.65804 14.6224 11.0179 14.6224 12.1001C14.6224 13.2362 13.9621 14.0457 13.0707 14.561L13.0697 14.562C12.1833 15.0751 11.0433 15.3119 10.0004 15.312C8.95761 15.312 7.81631 15.0734 6.92908 14.561H6.9281C6.03668 14.0478 5.37732 13.2369 5.37732 12.1001C5.37738 11.0856 5.71424 9.82744 6.38513 8.77588L6.5238 8.56787C7.28995 7.47054 8.45285 6.63135 10.0004 6.63135Z" fill="#FFF1DD" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M2.06934 7.10156C2.79989 7.10183 3.58777 7.89262 3.58789 9.1377C3.58789 10.3829 2.79994 11.1736 2.06934 11.1738C1.33862 11.1738 0.549805 10.3831 0.549805 9.1377C0.549925 7.89241 1.33868 7.10156 2.06934 7.10156Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M17.9314 7.10156C18.6619 7.10183 19.4498 7.89262 19.45 9.1377C19.45 10.3829 18.662 11.1736 17.9314 11.1738C17.2007 11.1738 16.4119 10.3831 16.4119 9.1377C16.412 7.89241 17.2007 7.10156 17.9314 7.10156Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M6.55225 0.549805C7.2828 0.550073 8.07068 1.34086 8.0708 2.58594C8.0708 3.83117 7.28285 4.6218 6.55225 4.62207C5.82153 4.62207 5.03271 3.83139 5.03271 2.58594C5.03283 1.34065 5.82159 0.549805 6.55225 0.549805Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M13.4485 0.549805C14.179 0.550073 14.9669 1.34086 14.967 2.58594C14.967 3.83117 14.1791 4.6218 13.4485 4.62207C12.7178 4.62207 11.929 3.83139 11.929 2.58594C11.9291 1.34065 12.7178 0.549805 13.4485 0.549805Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Select size, age &amp; pet preferences</p>
                            <p class="admin-wc-list-desc">Only accept pets you are comfortable handling.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit pet preferences"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path d="M9.30723 0.612928C9.45824 0.529033 9.64186 0.529033 9.79287 0.612928L18.2929 5.33515C18.4516 5.42334 18.55 5.59065 18.55 5.77223V15.1838C18.55 15.3654 18.4516 15.5327 18.2929 15.6209L9.79287 20.3431C9.64186 20.427 9.45824 20.427 9.30723 20.3431L0.807228 15.6209C0.648495 15.5327 0.550049 15.3654 0.550049 15.1838V5.77223C0.550049 5.59065 0.648495 5.42334 0.807227 5.33515L9.30723 0.612928Z" fill="#FFF1DD" />
                                <path d="M9.55005 10.478L18.05 5.97803M9.55005 10.478V19.978M9.55005 10.478L1.05005 5.97803M0.550049 5.77223V15.1838C0.550049 15.3654 0.648495 15.5327 0.807228 15.6209L9.30723 20.3431C9.45824 20.427 9.64186 20.427 9.79287 20.3431L18.2929 15.6209C18.4516 15.5327 18.55 15.3654 18.55 15.1838V5.77223C18.55 5.59065 18.4516 5.42334 18.2929 5.33515L9.79287 0.612928C9.64186 0.529033 9.45824 0.529033 9.30723 0.612928L0.807227 5.33515C0.648495 5.42334 0.550049 5.59065 0.550049 5.77223Z" stroke="#3B3731" stroke-width="1.1" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">All-in-one platform</p>
                            <p class="admin-wc-list-desc">Everything you need to run your business in one place.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit platform details"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>
        </div>

        {{-- Business Homepage - FursGo Business --}}
        <div class="admin-wc-stack admin-wc-business-main-stack" x-show="editingId === 'business'" x-cloak>
            <section class="admin-wc-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-hero-preview admin-wc-preview-block">
                    <p class="admin-wc-preview-headline">{{ $businessBanner['headline'] }}</p>
                </div>

                <div class="admin-wc-cta admin-wc-business-description">
                    <p class="admin-wc-card-title mb-0">Description</p>
                    <div class="admin-wc-preview">
                        <p class="admin-wc-preview-body">{{ $businessBanner['description'] }}</p>
                    </div>
                </div>

                <div class="admin-wc-divider"></div>

                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Hero Photo</h2>
                        <p class="admin-wc-card-sub">Custom photo uploaded</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit photo
                    </button>
                </div>
                <img
                    src="{{ asset($businessBanner['photo']) }}"
                    alt="Business hero photo preview"
                    class="admin-wc-thumb admin-wc-business-hero-thumb"
                    width="274"
                    height="106">
            </section>

            <section class="admin-wc-card admin-wc-groomer-banner">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 1</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>

                <div class="admin-wc-preview admin-wc-preview-block admin-wc-business-feature-preview">
                    <p class="admin-wc-preview-headline">Build It Around Your Life</p>
                    <p class="admin-wc-preview-body">Choose your availability, decide who you work with, and grow at a pace that fits you.</p>
                </div>

                <div class="admin-wc-divider"></div>

                <ul class="admin-wc-list admin-wc-groomer-benefits">
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20" viewBox="0 0 22 20" fill="none">
                                <path d="M0.550049 9.6444C0.550049 6.03458 0.550049 4.22919 1.72205 3.10824C2.89405 1.98729 4.77905 1.98633 8.55005 1.98633H12.55C16.321 1.98633 18.207 1.98633 19.378 3.10824C20.549 4.23014 20.55 6.03458 20.55 9.6444V11.5589C20.55 15.1687 20.55 16.9741 19.378 18.0951C18.206 19.216 16.321 19.217 12.55 19.217H8.55005C4.77905 19.217 2.89305 19.217 1.72205 18.0951C0.551049 16.9732 0.550049 15.1687 0.550049 11.5589V9.6444Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M1.35147 7.50293C1.31067 8.23824 1.31067 9.11304 1.31067 10.164V11.8263C1.31067 14.9606 1.31159 16.5273 2.39294 17.5014C3.4743 18.4756 5.21591 18.4756 8.69821 18.4756H12.392C15.8743 18.4756 17.615 18.4747 18.6973 17.5014C19.7795 16.5282 19.7795 14.9606 19.7795 11.8263V10.164C19.7795 9.11295 19.7794 8.23819 19.7385 7.50293H1.35147Z" fill="#FFC97A" fill-opacity="0.125" />
                                <path d="M7.28589 12.7237L9.59677 15.0346L13.7564 10.875L13.1047 10.2188L9.59677 13.7266L7.93756 12.0721L7.28589 12.7237Z" fill="#FFC97A" />
                                <path d="M5.39897 1.97884V0.549805M15.6912 1.97884V0.549805M0.767456 6.74228H20.3227" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Set your own schedule &amp; prices.</p>
                            <p class="admin-wc-list-desc">Decide when you&rsquo;re available and what your services are worth.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit schedule and prices"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path d="M12.7197 0.893623C14.2917 0.371964 15.8424 0.386176 16.8001 1.33969C16.8028 1.34236 16.8058 1.3452 16.8084 1.34793L18.0841 2.68527C18.0959 2.6977 18.1066 2.71092 18.1167 2.72428C18.1373 2.73873 18.1571 2.75495 18.1755 2.77327C19.1332 3.7269 19.1475 5.27075 18.6235 6.83601C18.09 8.42943 16.9587 10.1945 15.3721 11.774C13.7857 13.3537 12.013 14.4802 10.4126 15.0113C8.94761 15.4975 7.50172 15.5176 6.53641 14.7465C6.44185 14.7296 6.35163 14.6832 6.28259 14.6064L4.9477 13.1222C3.99924 12.1678 3.98697 10.6291 4.50927 9.06895C5.04275 7.47556 6.17369 5.71046 7.76027 4.13098C9.34667 2.55125 11.1194 1.42475 12.7197 0.893623Z" fill="white" />
                                <path d="M6.27943 14.3679C7.88596 15.9744 11.7933 14.6723 15.0063 11.4587C18.2199 8.24566 19.522 4.33833 17.9155 2.7318M11.0065 1.64054L11.7336 2.36822M8.46141 4.18611L9.18856 4.91327M6.27891 7.09526L7.00607 7.82242M5.55176 10.7316L6.27891 11.4587M15.0063 0.549805L15.7335 1.27696M14.2792 4.91379L15.7335 6.3681M11.7341 7.45936L13.1884 8.91368M8.82499 9.64083L10.2793 11.0952" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6.18184 16.5501C6.78433 15.9476 6.78433 14.9707 6.18184 14.3682C5.57935 13.7658 4.60253 13.7658 4.00004 14.3682L1.09098 17.2773C0.488495 17.8798 0.488495 18.8566 1.09098 19.4591C1.69347 20.0616 2.67029 20.0616 3.27278 19.4591L6.18184 16.5501Z" fill="#FFF8EE" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Offer the Services That Suit You</p>
                            <p class="admin-wc-list-desc">From full grooming to bath &amp; brush and add-ons &mdash; shape your service menu around your skills.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit services"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                <path d="M10.0004 6.63135C11.5494 6.63151 12.7109 7.46925 13.475 8.56689L13.476 8.56787C14.2392 9.65804 14.6224 11.0179 14.6224 12.1001C14.6224 13.2362 13.9621 14.0457 13.0707 14.561L13.0697 14.562C12.1833 15.0751 11.0433 15.3119 10.0004 15.312C8.95761 15.312 7.81631 15.0734 6.92908 14.561H6.9281C6.03668 14.0478 5.37732 13.2369 5.37732 12.1001C5.37738 11.0856 5.71424 9.82744 6.38513 8.77588L6.5238 8.56787C7.28995 7.47054 8.45285 6.63135 10.0004 6.63135Z" fill="#FFF1DD" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M2.06934 7.10156C2.79989 7.10183 3.58777 7.89262 3.58789 9.1377C3.58789 10.3829 2.79994 11.1736 2.06934 11.1738C1.33862 11.1738 0.549805 10.3831 0.549805 9.1377C0.549925 7.89241 1.33868 7.10156 2.06934 7.10156Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M17.9314 7.10156C18.6619 7.10183 19.4498 7.89262 19.45 9.1377C19.45 10.3829 18.662 11.1736 17.9314 11.1738C17.2007 11.1738 16.4119 10.3831 16.4119 9.1377C16.412 7.89241 17.2007 7.10156 17.9314 7.10156Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M6.55225 0.549805C7.2828 0.550073 8.07068 1.34086 8.0708 2.58594C8.0708 3.83117 7.28285 4.6218 6.55225 4.62207C5.82153 4.62207 5.03271 3.83139 5.03271 2.58594C5.03283 1.34065 5.82159 0.549805 6.55225 0.549805Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                                <path d="M13.4485 0.549805C14.179 0.550073 14.9669 1.34086 14.967 2.58594C14.967 3.83117 14.1791 4.6218 13.4485 4.62207C12.7178 4.62207 11.929 3.83139 11.929 2.58594C11.9291 1.34065 12.7178 0.549805 13.4485 0.549805Z" fill="white" stroke="#3B3731" stroke-width="1.1" />
                            </svg></span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Choose the Pets You&rsquo;re Comfortable With</p>
                            <p class="admin-wc-list-desc">Set size, age and handling preferences so you always work within your confidence.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit pet preferences"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-groomer-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="24" viewBox="0 0 26 32" fill="none">
                                <path d="M12.607 2.5144C12.8235 2.43702 13.0601 2.43663 13.2769 2.51328L23.3334 6.06921C23.7329 6.21048 24 6.58824 24 7.01201V17.7674C24 23.5313 15.4487 28.3339 13.4254 29.3895C13.1555 29.5303 12.8445 29.5303 12.5746 29.3895C10.5513 28.3339 2 23.5313 2 17.7674V7.0095C2 6.58696 2.26558 6.21002 2.66348 6.06783L12.607 2.5144Z" fill="#FFF8EE" />
                                <path d="M12.7656 0.589844C12.8733 0.551578 12.9908 0.551003 13.0986 0.588867L25.166 4.83496C25.3661 4.90539 25.5 5.09449 25.5 5.30664V18.0781C25.5 21.2679 23.0761 24.278 20.2246 26.6924C17.4015 29.0827 14.2953 30.7754 13.1924 31.3438C13.0667 31.4085 12.9343 31.4084 12.8086 31.3438H12.8076C11.7047 30.7754 8.59854 29.0827 5.77539 26.6924C2.92386 24.278 0.5 21.2679 0.5 18.0781V5.30371C0.50018 5.09246 0.633049 4.90395 0.832031 4.83301L12.7656 0.589844Z" stroke="#3B3731" />
                                <path d="M8.55895 16.8531L11.9614 20.2556L18.5334 13.6836C18.8423 13.3748 18.8431 12.8744 18.5354 12.5645C18.2262 12.2531 17.7227 12.2522 17.4124 12.5625L11.9614 18.0134L9.67607 15.7345C9.36725 15.4265 8.86735 15.4268 8.55895 15.7352C8.25025 16.0439 8.25025 16.5444 8.55895 16.8531Z" fill="#FFC97A" />
                            </svg></span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Everything You Need, In One Place</p>
                            <p class="admin-wc-list-desc">Manage bookings, availability, communication and payouts through a single, simple platform.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit platform details"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.90909 1.43188H2.11869C1.75569 1.43188 1.40756 1.57609 1.15088 1.83276C0.8942 2.08944 0.75 2.43757 0.75 2.80057V12.3814C0.75 12.7444 0.8942 13.0925 1.15088 13.3492C1.40756 13.6059 1.75569 13.7501 2.11869 13.7501H11.6995C12.0625 13.7501 12.4106 13.6059 12.6673 13.3492C12.924 13.0925 13.0682 12.7444 13.0682 12.3814V7.59097" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.2717 1.1752C11.544 0.902948 11.9132 0.75 12.2983 0.75C12.6833 0.75 13.0525 0.902948 13.3248 1.1752C13.597 1.44744 13.75 1.81669 13.75 2.20171C13.75 2.58673 13.597 2.95598 13.3248 3.22823L7.15679 9.39689C6.99429 9.55925 6.79354 9.6781 6.57304 9.74249L4.60692 10.3173C4.54804 10.3345 4.48562 10.3355 4.4262 10.3203C4.36678 10.3051 4.31254 10.2742 4.26916 10.2308C4.22579 10.1874 4.19487 10.1332 4.17965 10.0738C4.16443 10.0143 4.16546 9.95193 4.18263 9.89304L4.75748 7.92692C4.82217 7.7066 4.94125 7.50609 5.10376 7.34386L11.2717 1.1752Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    {{-- Static Pages --}}
    <div
        class="admin-wc-panel"
        x-show="section === 'static'"
        x-data="{
            staticOpen: false,
            staticId: 'mission',
            staticTargets: [
                { id: 'mission', label: 'Our Mission (About us)', url: 'fursgo.com/our_mission' },
                { id: 'contact', label: 'Contact Us', url: 'fursgo.com/contact_us' },
                { id: 'company', label: 'Company Information', url: 'fursgo.com/company_information' }
            ],
            get staticTarget() {
                return this.staticTargets.find(item => item.id === this.staticId) || this.staticTargets[0];
            },
            selectStatic(id) {
                this.staticId = id;
                this.staticOpen = false;
            }
        }"
        @keydown.escape.window="staticOpen = false"
        x-cloak>
        <div class="admin-wc-head">
            <div class="admin-wc-head-top">
                <h1 class="admin-page-title mb-0">Static Pages</h1>
            </div>

            <div class="admin-wc-editing-actions-row">
                <div class="admin-wc-editing">
                    <span class="admin-wc-editing-label">Editing:</span>
                    <div class="admin-wc-editing-wrap" @click.outside="staticOpen = false">
                        <button
                            type="button"
                            class="admin-wc-editing-btn"
                            :aria-expanded="staticOpen.toString()"
                            @click="staticOpen = !staticOpen">
                            <span x-text="staticTarget.label"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="admin-wc-editing-menu" x-show="staticOpen" x-cloak x-transition.opacity.duration.150ms>
                            <template x-for="item in staticTargets" :key="item.id">
                                <button
                                    type="button"
                                    class="admin-wc-editing-option"
                                    :class="{ 'is-active': staticId === item.id }"
                                    @click="selectStatic(item.id)"
                                    x-text="item.label"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="admin-wc-head-actions">
                    <button type="button" class="admin-btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true">
                            <path d="M8.25.75 1 8M3.5.75h4.75V5.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Preview
                    </button>
                    <button type="button" class="admin-btn-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                            <path d="M3.25 1.63A5.77 5.77 0 0 0 .72 6.5a5.78 5.78 0 0 0 4.36 5.73M3.25 1.63H.9M3.25 1.63V3.9M9.75 11.37a5.77 5.77 0 0 0 2.53-4.87A5.78 5.78 0 0 0 7.92.77M9.75 11.37h2.35M9.75 11.37V9.1M3.5 6.75l1.8 1.8 4.2-4.2" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Publish
                    </button>
                </div>
            </div>

            <div class="admin-wc-status-row">
                <p class="admin-wc-live">
                    <span class="admin-wc-live-dot" aria-hidden="true"></span>
                    <strong>Live</strong>
                    <span class="admin-wc-live-meta">Last published 21 Jun 2026 by Michelle M</span>
                </p>
                <p class="admin-wc-url" x-text="staticTarget.url"></p>
            </div>
        </div>

        <div class="admin-wc-stack" x-show="staticId === 'contact'" x-cloak>
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Contact Us</p>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Details</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-contact-intro">
                    <p class="admin-wc-preview-headline">We&rsquo;re Here to Help</p>
                    <p class="admin-wc-preview-body">Get in touch, we&rsquo;d love to hear from you.</p>
                </div>

                <div class="admin-wc-contact-details-head">
                    <div>
                        <h3 class="admin-wc-contact-details-title">Contact Details</h3>
                        <p class="admin-wc-card-sub">Click on icons to edit</p>
                    </div>
                    <button type="button" class="admin-wc-add-link"><span aria-hidden="true">+</span> Add contact detail</button>
                </div>

                <ul class="admin-wc-list admin-wc-contact-list">
                    <li class="admin-wc-list-item">
                        <button type="button" class="admin-wc-contact-icon" aria-label="Edit office address icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M8 14s4-3.75 4-7a4 4 0 1 0-8 0c0 3.25 4 7 4 7Z" stroke="white" stroke-width="1.2" />
                                <circle cx="8" cy="7" r="1.4" stroke="white" stroke-width="1.2" />
                            </svg>
                        </button>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Office Address</p>
                            <p class="admin-wc-list-desc">12 King&rsquo;s Road, London SW3 4JP</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit office address"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M9.89175 1.17974C10.2469 0.824545 10.7287 0.625 11.231 0.625C11.7333 0.625 12.2151 0.824545 12.5703 1.17974C12.9255 1.53493 13.125 2.01667 13.125 2.51899C13.125 3.02131 12.9255 3.50306 12.5703 3.85825L4.52313 11.9063C4.31112 12.1181 4.04921 12.2732 3.76154 12.3572L1.19642 13.1071C1.11959 13.1296 1.03815 13.1309 0.960625 13.111C0.883101 13.0912 0.812341 13.0508 0.755753 12.9942C0.699164 12.9377 0.658828 12.8669 0.638967 12.7894C0.619105 12.7118 0.620449 12.6304 0.642857 12.5536L1.39284 9.98846C1.47724 9.70102 1.6326 9.43942 1.84462 9.22777L9.89175 1.17974Z" stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <button type="button" class="admin-wc-contact-icon" aria-label="Edit email icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <rect x="2.5" y="3.5" width="11" height="9" rx="1.5" stroke="white" stroke-width="1.2" />
                                <path d="m3 5 5 3.5L13 5" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Email Address</p>
                            <p class="admin-wc-list-desc">fursgo@gmail.com</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit email address"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M9.89175 1.17974C10.2469 0.824545 10.7287 0.625 11.231 0.625C11.7333 0.625 12.2151 0.824545 12.5703 1.17974C12.9255 1.53493 13.125 2.01667 13.125 2.51899C13.125 3.02131 12.9255 3.50306 12.5703 3.85825L4.52313 11.9063C4.31112 12.1181 4.04921 12.2732 3.76154 12.3572L1.19642 13.1071C1.11959 13.1296 1.03815 13.1309 0.960625 13.111C0.883101 13.0912 0.812341 13.0508 0.755753 12.9942C0.699164 12.9377 0.658828 12.8669 0.638967 12.7894C0.619105 12.7118 0.620449 12.6304 0.642857 12.5536L1.39284 9.98846C1.47724 9.70102 1.6326 9.43942 1.84462 9.22777L9.89175 1.17974Z" stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 1</h2>
                        <p class="admin-wc-card-sub">Lorem Ipsum dolor sit amet</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">FursGo App Coming Soon</p>
                </div>
                <div class="admin-wc-divider"></div>
                <div class="admin-wc-contact-store-list">
                    <div class="admin-wc-preview admin-wc-contact-store">
                        <p>App Store label</p>
                        <span>Available soon</span>
                    </div>
                    <div class="admin-wc-preview admin-wc-contact-store">
                        <p>Google Play label</p>
                        <span>Available soon</span>
                    </div>
                </div>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link"><span aria-hidden="true">&#8599;</span> Full Log</button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>

        <div class="admin-wc-stack" x-show="staticId === 'mission'" x-cloak>
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Our Mission</p>
                </div>
                <div class="admin-wc-divider"></div>
                <div class="admin-wc-static-fields">
                    <div class="admin-wc-static-field">
                        <div class="admin-wc-static-field-label">Intro <span>(left column)</span></div>
                        <div class="admin-wc-preview admin-wc-static-copy-preview">FursGo is shaped by the people who use it and the pets at the centre of it all.</div>
                    </div>
                    <div class="admin-wc-static-field">
                        <div class="admin-wc-static-field-label">Description <span>(right column)</span></div>
                        <div class="admin-wc-preview admin-wc-static-copy-preview">We aim to make pet care accessible and trusted &mdash; bringing pet owners, groomers, and space hosts together with care, transparency, and trust at the heart of everything we do. Every interaction, booking, and conversation &mdash; from first hellos to freshly groomed tails &mdash; helps move us closer to a more connected, caring way of approaching pet care.</div>
                    </div>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 1</h2>
                        <p class="admin-wc-card-sub">Click a thumbnail to upload a photo</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Who is FursGo for?</p>
                </div>
                <div class="admin-wc-divider"></div>
                <ul class="admin-wc-list admin-wc-static-audience">
                    <li class="admin-wc-list-item">
                        <img src="{{ asset('images/pet_owners1.png') }}" alt="Pet owner" class="admin-wc-static-thumb">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Pet Owners</p>
                            <p class="admin-wc-list-desc">Looking for reliable, caring pet services &mdash; and a little peace of mind while their pets are in safe hands.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Pet Owners"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <img src="{{ asset('images/profile-img-2.png') }}" alt="Pet groomer" class="admin-wc-static-thumb">
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Pet Groomers</p>
                            <p class="admin-wc-list-desc">Offering mobile or independent grooming services, and doing what you do best: caring for pets, one appointment at a time.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Pet Groomers"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-static-thumb admin-wc-static-thumb-placeholder" aria-hidden="true">+</span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Space Hosts</p>
                            <p class="admin-wc-list-desc">Providing professional grooming environments where pets feel comfortable, calm, and welcome.</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Space Hosts"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Banner 2</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline admin-wc-static-wide-headline">Explore pet care near you, or join the FursGo community and be part of something built on care, trust, and a whole lot of love for pets.</p>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">CTA Banner</h2>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Find a Groomer Near You</p>
                </div>
                <div class="admin-wc-divider"></div>
                <ul class="admin-wc-list admin-wc-static-cta-list">
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-static-cta-icon is-groomer" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="13" viewBox="0 0 20 13" fill="none">
                                <rect x="2.83545" y="0.477539" width="11.9329" height="4.2533" rx="2.12665" fill="#FFC97A" />
                                <rect y="4.61328" width="11.9329" height="4.13515" rx="2.06758" fill="#FFC97A" />
                                <rect x="6.02539" y="8.15723" width="11.9329" height="4.2533" rx="2.12665" fill="#FFC97A" />
                                <path d="M8.94978 6.5742C8.17659 6.5742 7.51435 6.29882 6.96305 5.74806C6.41175 5.19729 6.13642 4.536 6.13704 3.76419C6.13767 2.99237 6.413 2.33045 6.96305 1.77844C7.5131 1.22643 8.17534 0.951982 8.94978 0.955104C9.72422 0.958227 10.3865 1.23298 10.9365 1.77938C11.4866 2.32577 11.7619 2.98768 11.7625 3.76512C11.7631 4.54256 11.4878 5.20385 10.9365 5.74899C10.3852 6.29414 9.72297 6.56858 8.94978 6.5742ZM2.38672 11.4383V10.8763C2.38672 10.4786 2.50142 10.113 2.73081 9.7795C2.96021 9.44604 3.26148 9.18596 3.63464 8.99925C4.47596 8.60272 5.34166 8.29175 6.23174 8.06632C7.12181 7.8409 8.02908 7.72787 8.95353 7.72725C9.87799 7.72662 10.784 7.83965 11.6716 8.06632C12.5592 8.293 13.4236 8.60397 14.2649 8.99925C14.6381 9.18533 14.9394 9.44542 15.1688 9.7795C15.3981 10.1136 15.5128 10.4789 15.5128 10.8754V11.4374C15.5128 11.7122 15.4194 11.9432 15.2325 12.1305C15.0456 12.3173 14.8144 12.4106 14.5387 12.4106H3.3618C3.08615 12.4106 2.85488 12.3169 2.66799 12.1296C2.48048 11.9429 2.38672 11.7131 2.38672 11.4383ZM8.94978 5.63753C9.46545 5.63753 9.90705 5.45394 10.2746 5.08677C10.6421 4.71959 10.8256 4.27873 10.8249 3.76419C10.8243 3.24964 10.6409 2.80847 10.2746 2.44067C9.9083 2.07287 9.4667 1.88959 8.94978 1.89084C8.43286 1.89209 7.99158 2.07537 7.62592 2.44067C7.26026 2.80597 7.0765 3.24714 7.07462 3.76419C7.07275 4.28123 7.25651 4.72209 7.62592 5.08677C7.99533 5.45145 8.43661 5.63503 8.94978 5.63753ZM12.1591 9.15661V11.4739H14.5753V10.8679C14.5753 10.6362 14.5003 10.4324 14.3502 10.2563C14.2002 10.0802 14.0227 9.93467 13.8177 9.81977C13.5508 9.68239 13.2783 9.56031 13.0001 9.45353C12.722 9.34675 12.4416 9.24778 12.1591 9.15661ZM6.67802 8.91776V10.0689H11.2215V8.91682C10.849 8.83065 10.4718 8.76695 10.0899 8.72574C9.7086 8.68453 9.32732 8.66392 8.94603 8.66392C8.5635 8.66392 8.18315 8.68453 7.805 8.72574C7.42684 8.76695 7.05118 8.83096 6.67802 8.91776ZM3.3243 11.4739H5.74044V9.15661C5.45792 9.24715 5.17758 9.34613 4.89943 9.45353C4.62129 9.56094 4.34876 9.68302 4.08186 9.81977C3.87747 9.93405 3.70027 10.0795 3.55026 10.2563C3.39962 10.4324 3.3243 10.6362 3.3243 10.8679V11.4739Z" fill="#3B3731" />
                                <path d="M18.9746 2.91029L17.9118 2.40478C17.7195 2.314 17.601 2.58171 17.7865 2.66808L18.8494 3.1736L18.7851 3.30871L17.7223 2.8032C17.532 2.71081 17.4134 2.97831 17.597 3.0665L18.6599 3.57201L18.5942 3.71009L17.5314 3.20458C17.3404 3.11374 17.2219 3.3811 17.4061 3.46788L18.4689 3.9734L18.4064 4.10498L17.3435 3.59947C17.1559 3.51179 17.0333 3.77754 17.2168 3.86588L18.2796 4.37139L18.2169 4.50326L17.1541 3.99774C16.9674 3.90824 16.8411 4.17174 17.0289 4.26105L18.0917 4.76656L18.0291 4.89814L16.9663 4.39263C16.779 4.30425 16.6526 4.56804 16.841 4.65608L17.9038 5.16159L17.8397 5.29642L16.7768 4.79091C16.5906 4.70041 16.4643 4.96392 16.6516 5.05421L17.7144 5.55973L17.652 5.69103L16.5892 5.18551C16.4022 5.09643 16.2795 5.36232 16.4639 5.44896L17.5267 5.95447L16.3072 8.51821C16.122 8.90916 16.768 9.20776 16.9523 8.82504L19.806 2.82548C19.889 2.64615 19.8376 2.34505 19.5768 2.22013L18.2906 1.60837C18.0985 1.51716 17.98 1.78488 18.1654 1.87167L19.2266 2.38043L19.1642 2.51173L18.1014 2.00622C17.91 1.91417 17.7916 2.18175 17.976 2.26967L19.0389 2.77518L18.9746 2.91029ZM15.6768 6.91526C15.8931 6.45894 15.6145 5.72499 14.8518 5.75175L15.6814 4.00758L16.654 0.942838C16.682 0.862813 16.6206 0.811648 16.5743 0.78805C16.5252 0.766267 16.4196 0.738017 16.3729 0.809096L14.6111 3.4985L13.7815 5.24267C13.321 4.63411 12.5908 4.89206 12.3769 5.34137C12.1869 5.74127 12.364 6.2282 12.8155 6.44176C13.2695 6.65888 13.7715 6.44631 13.9428 6.08615L14.8045 4.27435L14.9574 4.34707L14.0957 6.15887C13.9039 6.56209 14.1001 7.02142 14.467 7.2191C14.3555 7.43721 14.2836 7.78977 14.3712 7.98482C14.4739 8.21369 14.7685 8.12358 14.757 7.93061C14.7434 7.74961 14.7036 7.62736 14.9163 7.34745C15.2423 7.36055 15.5392 7.20298 15.6768 6.91526ZM13.0066 6.08509C12.9035 6.03605 12.8226 5.9512 12.7817 5.84921C12.7408 5.74721 12.7433 5.63643 12.7885 5.54123C12.8338 5.44603 12.9182 5.3742 13.0231 5.34156C13.1281 5.30892 13.2449 5.31813 13.348 5.36717C13.5594 5.4691 13.6557 5.71173 13.5633 5.90997C13.5179 6.00446 13.434 6.07581 13.3298 6.10858C13.2256 6.14136 13.1095 6.13292 13.0066 6.08509ZM14.4877 6.34967C14.5331 6.25512 14.617 6.18371 14.7213 6.15087C14.8255 6.11804 14.9417 6.12642 15.0448 6.17421C15.1469 6.22393 15.2268 6.30879 15.2671 6.4104C15.3074 6.512 15.3048 6.62217 15.26 6.71701C15.2148 6.81164 15.1309 6.88313 15.0267 6.91592C14.9224 6.94872 14.8062 6.94016 14.7033 6.89212C14.6012 6.84246 14.5213 6.75769 14.481 6.65616C14.4406 6.55463 14.443 6.44452 14.4877 6.34967Z" fill="#3B3731" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Find a Groomer</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Find a Groomer"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-static-cta-icon is-space" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="0 0 20 14" fill="none">
                                <rect x="3.1582" y="0.236328" width="13.2894" height="4.73681" rx="2.36841" fill="#FFA899" />
                                <rect y="4.8418" width="13.2894" height="4.60524" rx="2.30262" fill="#FFA899" />
                                <rect x="6.71094" y="8.78906" width="13.2894" height="4.73681" rx="2.36841" fill="#FFA899" />
                                <path d="M6.11816 4.99997L9.69564 1.80124C10.6676 0.933497 11.1529 0.5 11.7431 0.5C12.3334 0.5 12.8194 0.933497 13.7906 1.80124L17.3681 4.99997M7.24316 4.24998V13.2499M16.2431 13.2499V4.24998" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M3.49316 8.74998C4.11447 8.74998 4.61815 8.07841 4.61815 7.24999C4.61815 6.42157 4.11447 5.75 3.49316 5.75C2.87184 5.75 2.36816 6.42157 2.36816 7.24999C2.36816 8.07841 2.87184 8.74998 3.49316 8.74998Z" stroke="#3B3731" />
                                <path d="M3.49316 8.75V13.25" stroke="#3B3731" stroke-linecap="round" />
                                <path d="M2.36816 13.2499H17.3681M9.86812 13.2499V10.25C9.86812 9.54272 9.86812 9.18947 10.0879 8.96972C10.3076 8.74997 10.6609 8.74997 11.3681 8.74997H12.1181C12.8253 8.74997 13.1786 8.74997 13.3983 8.96972C13.6181 9.18947 13.6181 9.54272 13.6181 10.25V13.2499M10.9931 6.49999H12.4931M10.9931 4.25H12.4931" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">Find a Space</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit Find a Space"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                </ul>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link">
                        <span aria-hidden="true">&#8599;</span>
                        Full Log
                    </button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item">
                        <span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>

        <div class="admin-wc-stack" x-show="staticId === 'company'" x-cloak>
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Company Information</p>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-company-index">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Index Sections</h2>
                        <p class="admin-wc-card-sub">Platform info anchors</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </div>

                <nav class="admin-wc-company-chips" aria-label="Company information sections">
                    @foreach (['Platform Overview', 'Trust & Safety', 'Mission & Vision', 'Privacy Summary', 'Legal Agreement', 'Compliance Disclosure', 'Terms & Conditions', 'Privacy Policy', 'Cookies Policy'] as $indexLabel)
                    <button type="button" class="admin-wc-company-chip {{ $loop->first ? 'is-active' : '' }}">{{ $indexLabel }}</button>
                    @endforeach
                </nav>

                <div class="admin-wc-company-heading-wrap">
                    <p class="admin-wc-company-field-label">Section Heading</p>
                    <div class="admin-wc-preview admin-wc-company-heading">
                        <p class="admin-wc-preview-headline">Learn how FursGo works</p>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit section heading"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M9.89175 1.17974C10.2469 0.824545 10.7287 0.625 11.231 0.625C11.7333 0.625 12.2151 0.824545 12.5703 1.17974C12.9255 1.53493 13.125 2.01667 13.125 2.51899C13.125 3.02131 12.9255 3.50306 12.5703 3.85825L4.52313 11.9063C4.31112 12.1181 4.04921 12.2732 3.76154 12.3572L1.19642 13.1071C1.11959 13.1296 1.03815 13.1309 0.960625 13.111C0.883101 13.0912 0.812341 13.0508 0.755753 12.9942C0.699164 12.9377 0.658828 12.8669 0.638967 12.7894C0.619105 12.7118 0.620449 12.6304 0.642857 12.5536L1.39284 9.98846C1.47724 9.70102 1.6326 9.43942 1.84462 9.22777L9.89175 1.17974Z" stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </div>
                </div>

                @php
                $companyContentSections = [
                ['title' => 'FursGo Groomers', 'status' => 'Published', 'hidden' => false],
                ['title' => 'FursGo Space Providers', 'status' => 'Hidden', 'hidden' => true],
                ['title' => 'Owners', 'status' => 'Published', 'hidden' => false],
                ];
                @endphp
                <div class="admin-wc-company-content-list">
                    @foreach ($companyContentSections as $contentSection)
                    <article class="admin-wc-company-content {{ $contentSection['hidden'] ? 'is-hidden' : '' }}">
                        <div class="admin-wc-company-content-top">
                            <div class="admin-wc-company-copy">
                                <h3>{{ $contentSection['title'] }}</h3>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.</p>
                                <p>Se consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
                            </div>
                            <span class="admin-wc-company-status {{ $contentSection['hidden'] ? 'is-hidden' : 'is-published' }}">{{ $contentSection['status'] }}</span>
                        </div>
                        <div class="admin-wc-company-actions">
                            @unless ($contentSection['hidden'])
                            <button type="button" class="admin-wc-square-btn" aria-label="Edit {{ $contentSection['title'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M9.89175 1.17974C10.2469 0.824545 10.7287 0.625 11.231 0.625C11.7333 0.625 12.2151 0.824545 12.5703 1.17974C12.9255 1.53493 13.125 2.01667 13.125 2.51899C13.125 3.02131 12.9255 3.50306 12.5703 3.85825L4.52313 11.9063C4.31112 12.1181 4.04921 12.2732 3.76154 12.3572L1.19642 13.1071C1.11959 13.1296 1.03815 13.1309 0.960625 13.111C0.883101 13.0912 0.812341 13.0508 0.755753 12.9942C0.699164 12.9377 0.658828 12.8669 0.638967 12.7894C0.619105 12.7118 0.620449 12.6304 0.642857 12.5536L1.39284 9.98846C1.47724 9.70102 1.6326 9.43942 1.84462 9.22777L9.89175 1.17974Z" stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg></button>
                            @endunless
                            <button type="button" class="admin-wc-square-btn" aria-label="{{ $contentSection['hidden'] ? 'Show' : 'View' }} {{ $contentSection['title'] }}">
                                @if ($contentSection['hidden'])
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="14" viewBox="0 0 17 14" fill="none" aria-hidden="true">
                                    <path d="M1 1l15 12M7.1 2.2c.45-.13.92-.2 1.4-.2 4.5 0 7 5 7 5a11.7 11.7 0 0 1-2.15 2.7M10.6 11.8c-.67.16-1.37.22-2.1.2-4.5 0-7-5-7-5a12.2 12.2 0 0 1 2.25-2.8M6.7 5.7a2.5 2.5 0 0 0 3.6 3.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none" aria-hidden="true">
                                    <path d="M1 6s2.5-4.5 7-4.5S15 6 15 6s-2.5 4.5-7 4.5S1 6 1 6Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                    <circle cx="8" cy="6" r="2" stroke="#3B3731" stroke-width="1.2" />
                                </svg>
                                @endif
                            </button>
                            <button type="button" class="admin-wc-square-btn" aria-label="Delete {{ $contentSection['title'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <path d="m2.5 2.5 7 7m0-7-7 7" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                </svg></button>
                        </div>
                    </article>
                    @endforeach
                </div>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link"><span aria-hidden="true">&#8599;</span> Full Log</button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M drafted Content Sections</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Trust &amp; Safety Section</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    {{-- Help Centre --}}
    <div class="admin-wc-panel" x-show="section === 'help'" x-data="{ openFaq: 1 }" x-cloak>
        <div class="admin-wc-head admin-wc-help-head">
            <div class="admin-wc-head-top">
                <h1 class="admin-page-title mb-0">Help Centre</h1>
                <div class="admin-wc-head-actions">
                    <button type="button" class="admin-btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true">
                            <path d="M8.25.75 1 8M3.5.75h4.75V5.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Preview
                    </button>
                    <button type="button" class="admin-btn-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                            <path d="M3.25 1.63A5.77 5.77 0 0 0 .72 6.5a5.78 5.78 0 0 0 4.36 5.73M3.25 1.63H.9M3.25 1.63V3.9M9.75 11.37a5.77 5.77 0 0 0 2.53-4.87A5.78 5.78 0 0 0 7.92.77M9.75 11.37h2.35M9.75 11.37V9.1M3.5 6.75l1.8 1.8 4.2-4.2" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Publish
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-wc-stack">
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Help &amp; Support Center</p>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-help-faq-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Section Heading</h2>
                        <p class="admin-wc-card-sub">FursGo related questions &amp; answers</p>
                    </div>
                </div>

                <div class="admin-wc-preview admin-wc-help-section-heading">
                    <div>
                        <p class="admin-wc-preview-headline">Bookings</p>
                        <p class="admin-wc-preview-body">View booking-related questions.</p>
                    </div>
                    <button type="button" class="admin-wc-icon-btn" aria-label="Edit section heading"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></button>
                </div>

                <div class="admin-wc-help-categories-row">
                    <div class="admin-wc-help-categories">
                        @foreach (['Bookings (7)', 'Payments (3)', 'Account (2)', 'Pets (10)', 'Safety (5)'] as $category)
                        <button type="button" class="admin-wc-company-chip {{ $loop->first ? 'is-active' : '' }}">{{ $category }}</button>
                        @endforeach
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </div>

                <div class="admin-wc-help-add-row">
                    <button type="button" class="admin-wc-add-link"><span aria-hidden="true">+</span> Add question to Bookings</button>
                </div>

                @php
                $helpQuestions = [
                'How do I book a grooming appointment?',
                'Can I choose my groomer?',
                'What grooming services are available?',
                'Do you offer in-home dog grooming?',
                'Can I cancel or reschedule my booking?',
                'What happens if the groomer cancels?',
                'What if there’s an issue with a booking?',
                ];
                @endphp
                <div class="admin-wc-help-questions">
                    @foreach ($helpQuestions as $question)
                    <article class="admin-wc-help-question">
                        <div class="admin-wc-help-question-row">
                            <button type="button" class="admin-wc-help-question-toggle" @click="openFaq = openFaq === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="(openFaq === {{ $loop->index }}).toString()">
                                <svg :class="{ 'is-open': openFaq === {{ $loop->index }} }" xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                                    <path d="m1.5 1 5 5-5 5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>{{ $question }}</span>
                            </button>
                            <div class="admin-wc-help-question-actions">
                                <button type="button" class="admin-wc-square-btn" aria-label="Edit question"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                        <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="admin-wc-square-btn" aria-label="View question"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none" aria-hidden="true">
                                        <path d="M1 6s2.5-4.5 7-4.5S15 6 15 6s-2.5 4.5-7 4.5S1 6 1 6Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                        <circle cx="8" cy="6" r="2" stroke="#3B3731" stroke-width="1.2" />
                                    </svg></button>
                                <button type="button" class="admin-wc-square-btn" aria-label="Delete question"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                        <path d="m2.5 2.5 7 7m0-7-7 7" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                    </svg></button>
                                <span class="admin-wc-company-status is-published">Published</span>
                            </div>
                        </div>
                        <div class="admin-wc-help-answer" x-show="openFaq === {{ $loop->index }}" x-cloak>
                            Choosing who looks after your dog is an important decision. FursGo allows you to browse groomer profiles, review experience and pricing, and select the groomer that feels right for you and your dog.
                        </div>
                    </article>
                    @endforeach
                </div>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link"><span aria-hidden="true">&#8599;</span> Full Log</button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Hero Section update</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M published Testimonials</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    {{-- Legal & Policy Documents --}}
    <div class="admin-wc-panel" x-show="section === 'legal'" x-cloak>
        <div class="admin-wc-head admin-wc-legal-head">
            <div class="admin-wc-head-top">
                <h1 class="admin-page-title mb-0">Legal &amp; Policy Documents</h1>
                <button type="button" class="admin-btn-dark">+ Add Document</button>
            </div>
        </div>

        <div class="admin-wc-stack">
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Page Banner</h2>
                        <p class="admin-wc-card-sub">Headline shown</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-hero-preview">
                    <p class="admin-wc-preview-headline">Service Policies</p>
                </div>
            </section>

            @php
            $legalDocuments = [
            [
            'group' => 'Refund Policy',
            'title' => 'FursGo Refund Policy (24-Hour Window)',
            'version' => 'v3.2',
            'description' => 'FursGo offers customers a refund window of up to 24 hours after a booking is made, provided the service has not yet taken place.',
            ],
            [
            'group' => 'Animal Welfare Statement',
            'title' => 'Animal Welfare Statement',
            'version' => 'v3.2',
            'description' => 'As a service provider on FursGo, I confirm that I will handle all animals with care, patience, and respect for their wellbeing.',
            ],
            [
            'group' => 'Compliance Declaration',
            'title' => 'FursGo Compliance Declaration',
            'version' => 'v3.2',
            'description' => 'By operating on the FursGo platform, I confirm that my business complies with all relevant local laws, regulations, and professional standards applicable to pet care.',
            ],
            ];
            @endphp
            <section class="admin-wc-card admin-wc-legal-documents">
                <div>
                    <h2 class="admin-wc-card-title">Provider Legal Agreements</h2>
                    <p class="admin-wc-card-sub">3 documents live</p>
                </div>

                <div class="admin-wc-legal-document-list">
                    @foreach ($legalDocuments as $document)
                    <div class="admin-wc-legal-document-group">
                        <h3 class="admin-wc-legal-group-title">{{ $document['group'] }}</h3>
                        <article class="admin-wc-legal-document">
                            <div class="admin-wc-legal-document-main">
                                <h4>{{ $document['title'] }} <span>{{ $document['version'] }}</span></h4>
                                <p>{{ $document['description'] }}</p>
                                <time datetime="2026-01-12">Last updated 12 Jan 2026</time>
                            </div>
                            <div class="admin-wc-legal-document-actions">
                                <button type="button" class="admin-wc-square-btn" aria-label="Edit {{ $document['group'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                        <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="admin-wc-square-btn" aria-label="View {{ $document['group'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none" aria-hidden="true">
                                        <path d="M1 6s2.5-4.5 7-4.5S15 6 15 6s-2.5 4.5-7 4.5S1 6 1 6Z" stroke="#3B3731" stroke-width="1.2" stroke-linejoin="round" />
                                        <circle cx="8" cy="6" r="2" stroke="#3B3731" stroke-width="1.2" />
                                    </svg></button>
                                <button type="button" class="admin-wc-square-btn" aria-label="Delete {{ $document['group'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                        <path d="m2.5 2.5 7 7m0-7-7 7" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                    </svg></button>
                                <span class="admin-wc-company-status is-published">Published</span>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link"><span aria-hidden="true">&#8599;</span> Full Log</button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Who Is FursGo For</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </div>

    {{-- Global Footer --}}
    <div class="admin-wc-panel" x-show="section === 'footer'" x-cloak>
        <div class="admin-wc-head admin-wc-footer-head">
            <div class="admin-wc-head-top">
                <h1 class="admin-page-title mb-0">Global Footer</h1>
            </div>
            <div class="admin-wc-footer-publish-row">
                <div class="admin-wc-status-row">
                    <p class="admin-wc-live">
                        <span class="admin-wc-live-dot" aria-hidden="true"></span>
                        <strong>Live</strong>
                        <span class="admin-wc-live-meta">Last published 21 Jun 2026 by Michelle M</span>
                    </p>
                    <p class="admin-wc-url">fursgo.com/our_mission</p>
                </div>
                <div class="admin-wc-head-actions">
                    <button type="button" class="admin-btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true">
                            <path d="M8.25.75 1 8M3.5.75h4.75V5.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Preview
                    </button>
                    <button type="button" class="admin-btn-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                            <path d="M3.25 1.63A5.77 5.77 0 0 0 .72 6.5a5.78 5.78 0 0 0 4.36 5.73M3.25 1.63H.9M3.25 1.63V3.9M9.75 11.37a5.77 5.77 0 0 0 2.53-4.87A5.78 5.78 0 0 0 7.92.77M9.75 11.37h2.35M9.75 11.37V9.1M3.5 6.75l1.8 1.8 4.2-4.2" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Publish
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-wc-stack">
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Footer CTA Banner</h2>
                        <p class="admin-wc-card-sub">Identical on every page</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <div class="admin-wc-preview admin-wc-footer-cta-preview">
                    <p class="admin-wc-preview-headline">Join thousands of happy pets &amp; owners.</p>
                    <p class="admin-wc-preview-body">Join Fursgo to grow your business with verified bookings and loyal clients.</p>
                </div>
            </section>

            @php
            $footerSocials = [
            ['name' => 'Instagram', 'url' => 'https://www.instagram.com/fursgo', 'icon' => 'instagram'],
            ['name' => 'X (Twitter)', 'url' => 'https://x.com/fursgo', 'icon' => 'x'],
            ['name' => 'Facebook', 'url' => 'https://en-gb.facebook.com/fursgo', 'icon' => 'facebook'],
            ['name' => 'TikTok', 'url' => 'https://www.tiktok.com/fursgo', 'icon' => 'tiktok'],
            ];
            @endphp
            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Social Links</h2>
                        <p class="admin-wc-card-sub">Icons are standard platform glyphs</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit text
                    </button>
                </div>
                <ul class="admin-wc-list admin-wc-footer-socials">
                    @foreach ($footerSocials as $social)
                    <li class="admin-wc-list-item">
                        <span class="admin-wc-footer-social-icon" aria-hidden="true">
                            @if ($social['icon'] === 'instagram')
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                <path d="M4.34961 0.5H10.6504C12.7739 0.500211 14.4998 2.22606 14.5 4.34961V10.6504C14.4999 11.6713 14.094 12.6501 13.3721 13.3721C12.6501 14.094 11.6713 14.4999 10.6504 14.5H4.34961C2.22606 14.4998 0.500211 12.7739 0.5 10.6504V4.34961C0.500104 3.32867 0.906007 2.34985 1.62793 1.62793C2.34985 0.906007 3.32867 0.500104 4.34961 0.5ZM4.2002 1C3.3515 1 2.53762 1.33738 1.9375 1.9375C1.33738 2.53762 1 3.3515 1 4.2002V10.7998C1 12.5684 2.43155 14 4.2002 14H10.7998C11.6485 14 12.4624 13.6626 13.0625 13.0625C13.6626 12.4624 14 11.6485 14 10.7998V4.2002C14 2.43155 12.5684 1 10.7998 1H4.2002ZM7.5 4.25C8.36195 4.25 9.18836 4.59266 9.79785 5.20215C10.4073 5.81164 10.75 6.63805 10.75 7.5C10.75 8.36195 10.4073 9.18836 9.79785 9.79785C9.18836 10.4073 8.36195 10.75 7.5 10.75C6.63805 10.75 5.81164 10.4073 5.20215 9.79785C4.59266 9.18836 4.25 8.36195 4.25 7.5C4.25 6.63805 4.59266 5.81164 5.20215 5.20215C5.81164 4.59266 6.63805 4.25 7.5 4.25ZM7.5 4.75C6.77066 4.75 6.07139 5.03994 5.55566 5.55566C5.03994 6.07139 4.75 6.77066 4.75 7.5C4.75 8.22934 5.03994 8.92861 5.55566 9.44434C6.07139 9.96006 6.77065 10.25 7.5 10.25C8.22934 10.25 8.92861 9.96006 9.44434 9.44434C9.96006 8.92861 10.25 8.22934 10.25 7.5C10.25 6.77065 9.96006 6.07139 9.44434 5.55566C8.92861 5.03994 8.22934 4.75 7.5 4.75ZM11.4375 3.125C11.5535 3.125 11.665 3.17088 11.7471 3.25293C11.8291 3.33498 11.875 3.44647 11.875 3.5625C11.875 3.67853 11.8291 3.79002 11.7471 3.87207C11.665 3.95412 11.5535 4 11.4375 4C11.3215 4 11.21 3.95412 11.1279 3.87207C11.0459 3.79002 11 3.67853 11 3.5625C11 3.44647 11.0459 3.33498 11.1279 3.25293C11.21 3.17088 11.3215 3.125 11.4375 3.125Z" fill="#3B3731" stroke="#3B3731" />
                            </svg>@elseif ($social['icon'] === 'facebook')
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="17" viewBox="0 0 10 17" fill="none">
                                <path d="M9 1H6.81818C5.85376 1 4.92884 1.39509 4.24688 2.09835C3.56493 2.80161 3.18182 3.75544 3.18182 4.75V7H1V10H3.18182V16H6.09091V10H8.27273L9 7H6.09091V4.75C6.09091 4.55109 6.16753 4.36032 6.30392 4.21967C6.44031 4.07902 6.6253 4 6.81818 4H9V1Z" stroke="#3B3731" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>@elseif ($social['icon'] === 'tiktok')
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                <path d="M7.3 6.65698C6.31301 6.37047 5.2584 6.39405 4.28596 6.72436C3.31353 7.05468 2.47271 7.67493 1.88294 8.49703C1.29317 9.31913 0.98444 10.3013 1.0006 11.3039C1.01677 12.3066 1.357 13.2788 1.97298 14.0825C2.58895 14.8862 3.44935 15.4804 4.43196 15.7808C5.41457 16.0813 6.46942 16.0726 7.44668 15.7561C8.42394 15.4396 9.27393 14.8313 9.87589 14.0176C10.4779 13.2039 10.8012 12.2262 10.8 11.2235V6.45251C11.7333 7.36126 13.1333 7.81564 15 7.81564V4.40782C13.1333 4.40782 11.7333 3.27188 10.8 1H7.3V11.2235M7.3 11.2235C7.3 10.9539 7.21789 10.6903 7.06406 10.4661C6.91022 10.242 6.69157 10.0673 6.43575 9.96409C6.17994 9.86092 5.89844 9.83393 5.62687 9.88652C5.3553 9.93912 5.10584 10.0689 4.91005 10.2596C4.71425 10.4502 4.58092 10.6931 4.5269 10.9575C4.47288 11.2219 4.5006 11.496 4.60657 11.7451C4.71253 11.9942 4.89197 12.2071 5.1222 12.3569C5.35243 12.5066 5.6231 12.5866 5.9 12.5866C6.2713 12.5866 6.6274 12.443 6.88995 12.1873C7.1525 11.9317 7.3 11.585 7.3 11.2235Z" stroke="#3B3731" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>@else
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="15" viewBox="0 0 17 15" fill="none">
                                <path d="M14.875 0.5L9.92773 6.01953L9.64941 6.33008L9.9043 6.65918L15.9795 14.5H11.9971L8.03613 9.44824L7.66797 8.97949L7.27051 9.42285L2.71973 14.5H1.45312L6.79688 8.53613L7.07715 8.22363L6.81836 7.89355L1.02734 0.500977H5.13379L8.69531 5.09863L9.0625 5.57324L9.46289 5.12695L13.6113 0.5H14.875ZM2.64746 1.75195L12.0771 13.7861L12.2275 13.9775H14.9355L14.3105 13.1709L4.98535 1.1377L4.83496 0.943359H2.01367L2.64746 1.75195Z" fill="#3B3731" stroke="#3B3731" />
                            </svg>@endif
                        </span>
                        <div class="admin-wc-list-copy">
                            <p class="admin-wc-list-title">{{ $social['name'] }}</p>
                            <p class="admin-wc-list-desc">{{ $social['url'] }}</p>
                        </div>
                        <button type="button" class="admin-wc-icon-btn" aria-label="Edit {{ $social['name'] }}"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                                <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </li>
                    @endforeach
                </ul>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Footer Navigation</h2>
                        <p class="admin-wc-card-sub">Icons are standard platform glyphs</p>
                    </div>
                    <button type="button" class="admin-wc-edit-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.81 2.5l2.47 2.41M8.16 13.75h6.59M1.57 10.53.75 13.75l3.29-.8 9.55-9.32a1.6 1.6 0 0 0 0-2.27l-.15-.14a1.67 1.67 0 0 0-2.32 0l-9.55 9.31Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit navigation
                    </button>
                </div>
                <div class="admin-wc-footer-nav-list">
                    <div class="admin-wc-footer-nav-row">
                        <p><strong>Column 1</strong> <span>&middot; About Us</span></p>
                        <p>About Us &middot; Our Mission &middot; Contact Us</p>
                    </div>
                    <div class="admin-wc-footer-nav-row">
                        <p><strong>Column 2</strong> <span>&middot; Help Centre column</span></p>
                        <p>FAQs &middot; Help &amp; Support &middot; Company Information</p>
                    </div>
                </div>
            </section>

            <section class="admin-wc-card admin-wc-static-card">
                <div class="admin-wc-card-head">
                    <div>
                        <h2 class="admin-wc-card-title">Footer Note</h2>
                        <p class="admin-wc-card-sub">Small text in the footer corner</p>
                    </div>
                </div>
                <div class="admin-wc-preview admin-wc-footer-note">
                    <p>Fursgo App Coming Soon</p>
                    <button type="button" class="admin-wc-icon-btn" aria-label="Edit footer note"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M6.91 1.43H2.12A1.37 1.37 0 0 0 .75 2.8v9.58c0 .76.61 1.37 1.37 1.37h9.58c.76 0 1.37-.61 1.37-1.37V7.59M11.27 1.18a1.45 1.45 0 0 1 2.05 2.05L7.16 9.4c-.16.16-.37.28-.59.34l-1.96.58a.34.34 0 0 1-.43-.43l.58-1.96c.06-.22.18-.42.34-.58l6.17-6.17Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></button>
                </div>
            </section>

            <section class="admin-wc-history admin-wc-static-history">
                <div class="admin-wc-subhead">
                    <h2 class="admin-wc-history-title">Version History</h2>
                    <button type="button" class="admin-wc-edit-link"><span aria-hidden="true">&#8599;</span> Full Log</button>
                </div>
                <ul class="admin-wc-history-list">
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Michelle M published Footer CTA Banner</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 14:32</p>
                        </div>
                    </li>
                    <li class="admin-wc-history-item"><span class="admin-wc-history-dot" aria-hidden="true"></span>
                        <div>
                            <p class="admin-wc-history-text">Ben M published Footer CTA Banner</p>
                            <p class="admin-wc-history-when">18 Apr 2025 &middot; 11:32</p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>