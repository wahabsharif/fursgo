@php
    // Load from parent Livewire page, or fall back to the DB helper.
    $page = $groomersHosts ?? \App\Support\GroomersHostsContent::page() ?? [];

    $hero = $page['hero'] ?? [];
    $flexibility = $page['flexibility'] ?? [];
    $safety = $page['safety'] ?? [];
    $tools = $page['tools'] ?? [];
    $howItWorks = $page['how_it_works'] ?? [];
    $testimonials = $page['testimonials'] ?? [];
    $services = $page['services'] ?? [];

    $heroPrimaryUrl = $hero['primary_cta_url'] ?? '/business-landing-page';
    $heroPrimaryUrl = str_starts_with((string) $heroPrimaryUrl, 'http')
        ? $heroPrimaryUrl
        : url($heroPrimaryUrl);

    $heroSecondaryUrl = $hero['secondary_cta_url'] ?? null;
    if (filled($heroSecondaryUrl) && ! str_starts_with((string) $heroSecondaryUrl, 'http')) {
        $heroSecondaryUrl = url($heroSecondaryUrl);
    }

    $testimonialSlides = collect($testimonials['items'] ?? [])->map(fn ($item) => [
        'date' => $item['meta']['date_label'] ?? '',
        'text' => $item['description'] ?? '',
        'name' => $item['title'] ?? '',
        'role' => $item['meta']['role'] ?? '',
        'img' => $item['image'] ?? asset('images/ellipse-68.svg'),
    ])->values()->all();

    $firstTestimonial = $testimonialSlides[0] ?? null;
@endphp

<!-- Business Homepage Content (DB-backed) -->
<div>
    {{-- Hero / Page Banner --}}
    <section class="container">
        <div class="bgs-business-groomer ">
            <div class="bgs-left">
                @if (filled($hero['eyebrow'] ?? null))
                    <p class="bgs-subtitle">{{ $hero['eyebrow'] }}</p>
                @endif

                <h1>{!! nl2br(e($hero['headline'] ?? '')) !!}</h1>

                <p class="bgs-description">
                    {!! nl2br(e($hero['description'] ?? '')) !!}
                </p>

                <div class="bgs-buttons">
                    @if (filled($hero['primary_cta'] ?? null))
                        <a href="{{ $heroPrimaryUrl }}" wire:navigate class="bgs-btn primary"
                            style="text-decoration: none; display: flex; justify-content: center; align-items: center;">
                            {{ $hero['primary_cta'] }}
                        </a>
                    @endif

                    @if (filled($hero['secondary_cta'] ?? null))
                        @if (filled($heroSecondaryUrl))
                            <a href="{{ $heroSecondaryUrl }}" class="bgs-btn secondary"
                                style="text-decoration: none;">{{ $hero['secondary_cta'] }}</a>
                        @else
                            <button type="button" class="bgs-btn secondary">{{ $hero['secondary_cta'] }}</button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="bgs-right">
                <img src="{{ $hero['photo'] ?? asset('images/business-homepage-groomer-space-owner-hero-section.png') }}"
                    alt="Hero Image">
            </div>
        </div>
    </section>

    {{-- Banner 1 — Control & flexibility --}}
    <section class="container">
        <div class="bgs-gf-section">
            <h2 class="bgs-main-title">{{ $flexibility['title'] ?? 'Control & flexibility' }}</h2>

            <div class="bgs-cards-container">
                @foreach ($flexibility['items'] ?? [] as $card)
                    @php($cardClass = $card['meta']['card_class'] ?? 'card-orange')
                    <div class="bgs-card {{ $cardClass }}">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['description'] }}</p>
                        <div class="bgs-icon-circle">
                            @include('partials.business-hub.business-homepage-flexibility-icon', ['icon' => $cardClass])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Banner 2 — Guaranteed safety & support --}}
    <section class="bgs-safety-section">
        <div class="bgs-safety-container">

            <div class="bgs-safety-left">
                <h2 class="bgs-safety-title">{!! nl2br(e($safety['title'] ?? 'Guaranteed safety & support')) !!}</h2>
                <div class="bgs-shield-image-container">
                    @include('partials.business-hub.business-homepage-safety-shield')
                </div>
            </div>

            <div class="bgs-safety-right">
                @foreach ($safety['items'] ?? [] as $index => $benefit)
                    <div class="bgs-feature-item" @if ($index === 0) style="border: none;" @endif>
                        <h3>{{ $benefit['title'] }}</h3>
                        <p>{{ $benefit['description'] }}</p>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- Banner 3 — Professional tools & CRM --}}
    <section class="container">
        <div class="bgs-crm-section">
            <h2 class="bgs-crm-main-title">{{ $tools['title'] ?? 'Professional tools & CRM' }}</h2>

            <div class="bgs-crm-cards-wrapper">
                @foreach ($tools['items'] ?? [] as $tool)
                    <div class="bgs-crm-card">
                        <h3>{{ $tool['title'] }}</h3>
                        <p>{!! nl2br(e($tool['description'] ?? '')) !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Banner 4 — How it works --}}
    <section class="works-section">
        <div class="container">
            <div class="header-container">
                <h2 class="main-title">{{ $howItWorks['title'] ?? 'How it works' }}</h2>
                @if (filled($howItWorks['intro'] ?? null))
                    <p class="sub-header-text">{{ $howItWorks['intro'] }}</p>
                @endif
            </div>

            <div class="steps-wrapper">
                @foreach ($howItWorks['steps'] ?? [] as $howStep)
                    <div class="step-card">
                        <div class="card-content">
                            <h3>
                                <strong>{{ $howStep['title'] }}</strong>
                                @if (filled($howStep['meta']['summary'] ?? null))
                                    – {{ $howStep['meta']['summary'] }}
                                @endif
                            </h3>
                            <p>{!! nl2br(e($howStep['description'] ?? '')) !!}</p>
                        </div>
                        <div class="step-footer">
                            <span>Step</span>
                            <span>{{ $howStep['meta']['step'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="container">

        <div class="testimonial-header">
            <header class="bgs-header">
                <h2 class="bgs-header-title">{{ $testimonials['title'] ?? 'Testimonials & trust' }}</h2>
                @if (filled($testimonials['intro'] ?? null))
                    <p class="bgs-header-subtitle">{{ $testimonials['intro'] }}</p>
                @endif
            </header>
        </div>

        <div class="bgs-testimonial-section">

            <div class="left-img-section">
                <span class="bgs-t-quote-icon-left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="35" viewBox="0 0 41 35"
                        fill="none">
                        <path
                            d="M10.0516 35C8.11183 35 6.52473 34.5833 5.29032 33.75C4.05591 32.9167 3.04193 31.8519 2.24839 30.5556C1.36667 28.9815 0.749462 27.3611 0.396774 25.6945C0.132258 23.9352 0 22.5 0 21.3889C0 16.8519 1.10215 12.7315 3.30645 9.02779C5.51075 5.32408 8.94946 2.31482 13.6226 0L14.8129 2.5C12.0796 3.70371 9.69893 5.60185 7.67097 8.19445C5.73118 10.787 4.76129 13.4259 4.76129 16.1111C4.76129 17.2222 4.89355 18.1944 5.15806 19.0278C6.56882 17.8241 8.2 17.2222 10.0516 17.2222C12.3441 17.2222 14.328 18.0093 16.0032 19.5833C17.6785 21.1574 18.5161 23.3333 18.5161 26.1111C18.5161 28.7037 17.6785 30.8333 16.0032 32.5C14.328 34.1667 12.3441 35 10.0516 35ZM32.5355 35C30.5957 35 29.0086 34.5833 27.7742 33.75C26.5398 32.9167 25.5258 31.8519 24.7323 30.5556C23.8505 28.9815 23.2333 27.3611 22.8806 25.6945C22.6161 23.9352 22.4839 22.5 22.4839 21.3889C22.4839 16.8519 23.586 12.7315 25.7903 9.02779C27.9946 5.32408 31.4333 2.31482 36.1064 0L37.2968 2.5C34.5634 3.70371 32.1828 5.60185 30.1548 8.19445C28.2151 10.787 27.2452 13.4259 27.2452 16.1111C27.2452 17.2222 27.3774 18.1944 27.6419 19.0278C29.0527 17.8241 30.6839 17.2222 32.5355 17.2222C34.828 17.2222 36.8118 18.0093 38.4871 19.5833C40.1624 21.1574 41 23.3333 41 26.1111C41 28.7037 40.1624 30.8333 38.4871 32.5C36.8118 34.1667 34.828 35 32.5355 35Z"
                            fill="#3B3731" />
                    </svg>
                </span>
                <img src="{{ asset('images/ellipse-65.svg') }}" class="float-img img-l-1" alt="User">
                <img src="{{ asset('images/ellipse-67.svg') }}" class="float-img img-l-2" alt="User">
                <img src="{{ asset('images/ellipse-66.svg') }}" class="float-img img-l-3" alt="User">
            </div>
            <div class="bgs-testimonial-card">
                <div class="slider-wrapper">
                    <div class="slider-content">
                        <div class="card side-card"></div>
                        <div class="card active-card">
                            @if ($firstTestimonial)
                                <p class="date">{{ $firstTestimonial['date'] }}</p>
                                <p class="testimonial-text">{{ $firstTestimonial['text'] }}</p>

                                <div class="user-info">
                                    <img src="{{ $firstTestimonial['img'] }}" alt="{{ $firstTestimonial['name'] }}">
                                    <div class="user-details">
                                        <h4>{{ $firstTestimonial['name'] }}</h4>
                                        <p>{{ $firstTestimonial['role'] }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="card side-card"></div>
                    </div>
                </div>
                <div class="controls">
                    <div class="dots">
                        @foreach ($testimonialSlides as $index => $slide)
                            <span class="dot {{ $index === 0 ? 'active' : '' }}"
                                onclick="currentSlide({{ $index }})"></span>
                        @endforeach
                    </div>
                    <div class="arrows">
                        <button class="arrow-btn" onclick="changeSlide(-1)">&lsaquo;</button>
                        <button class="arrow-btn" onclick="changeSlide(1)">&rsaquo;</button>
                    </div>
                </div>
            </div>
            <div class="right-img-section">
                <img src="{{ asset('images/ellipse-68.svg') }}" class="float-img img-r-1" alt="User">
                <img src="{{ asset('images/ellipse-70.svg') }}" class="float-img img-r-2" alt="User">
                <img src="{{ asset('images/ellipse-69.svg') }}" class="float-img img-r-3" alt="User">
                <span class="bgs-t-quote-icon-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="35" viewBox="0 0 41 35"
                        fill="none">
                        <path
                            d="M30.9484 0C32.8882 0 34.4753 0.416664 35.7097 1.25C36.9441 2.08333 37.9581 3.14815 38.7516 4.44444C39.6333 6.01852 40.2505 7.63889 40.6032 9.30555C40.8677 11.0648 41 12.5 41 13.6111C41 18.1481 39.8978 22.2685 37.6935 25.9722C35.4892 29.6759 32.0505 32.6852 27.3774 35L26.1871 32.5C28.9204 31.2963 31.3011 29.3981 33.329 26.8056C35.2688 24.213 36.2387 21.5741 36.2387 18.8889C36.2387 17.7778 36.1065 16.8056 35.8419 15.9722C34.4312 17.1759 32.8 17.7778 30.9484 17.7778C28.6559 17.7778 26.672 16.9907 24.9968 15.4167C23.3215 13.8426 22.4839 11.6667 22.4839 8.88889C22.4839 6.29629 23.3215 4.16666 24.9968 2.5C26.672 0.833328 28.6559 0 30.9484 0ZM8.46452 0C10.4043 0 11.9914 0.416664 13.2258 1.25C14.4602 2.08333 15.4742 3.14815 16.2677 4.44444C17.1495 6.01852 17.7667 7.63889 18.1194 9.30555C18.3839 11.0648 18.5161 12.5 18.5161 13.6111C18.5161 18.1481 17.414 22.2685 15.2097 25.9722C13.0054 29.6759 9.56667 32.6852 4.89355 35L3.70322 32.5C6.43656 31.2963 8.8172 29.3981 10.8452 26.8056C12.7849 24.213 13.7548 21.5741 13.7548 18.8889C13.7548 17.7778 13.6226 16.8056 13.3581 15.9722C11.9473 17.1759 10.3161 17.7778 8.46452 17.7778C6.17204 17.7778 4.18817 16.9907 2.5129 15.4167C0.837631 13.8426 0 11.6667 0 8.88889C0 6.29629 0.837631 4.16666 2.5129 2.5C4.18817 0.833328 6.17204 0 8.46452 0Z"
                            fill="#3B3731" />
                    </svg>
                </span>
            </div>

        </div>
    </section>

    {{-- Banner 5 — Service categories --}}
    <section class="container">
        <div class="bgs-services-section">

            <div class="bgs-services-header">
                <div>
                    <h2>{{ $services['title'] ?? 'Service categories' }}</h2>
                    <p>→ Choose what you want to offer.</p>
                </div>
                @if (filled($services['intro'] ?? null))
                    <div class="bgs-info">
                        @foreach (preg_split('/(?<=\.)\s+/', trim((string) $services['intro'])) as $line)
                            @if (filled($line))
                                <p>{{ $line }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bgs-services-grid">
                @foreach ($services['items'] ?? [] as $service)
                    @php($isImageFirst = ($service['meta']['layout'] ?? '') === 'image_first')
                    <div class="bgs-services-card {{ $isImageFirst ? 'image-card' : '' }}">
                        @if ($isImageFirst && filled($service['image'] ?? null))
                            <img src="{{ $service['image'] }}" alt="">
                        @endif
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                        @if (! $isImageFirst && filled($service['image'] ?? null))
                            <img src="{{ $service['image'] }}" alt="">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        let currentIdx = 0;

        const testimonials = @json($testimonialSlides);

        function updateSlider() {
            if (!testimonials.length) {
                return;
            }

            const activeCard = document.querySelector('.active-card');
            if (!activeCard) {
                return;
            }

            const dateEl = activeCard.querySelector('.date');
            const textEl = activeCard.querySelector('.testimonial-text');
            const imgEl = activeCard.querySelector('.user-info img');
            const nameEl = activeCard.querySelector('.user-details h4');
            const roleEl = activeCard.querySelector('.user-details p');
            const dots = document.querySelectorAll('.dot');
            const data = testimonials[currentIdx];

            activeCard.classList.remove('testimonial-slide-in');
            void activeCard.offsetWidth;

            if (dateEl) dateEl.textContent = data.date;
            if (textEl) textEl.textContent = data.text;
            if (imgEl) {
                imgEl.src = data.img;
                imgEl.alt = data.name;
            }
            if (nameEl) nameEl.textContent = data.name;
            if (roleEl) roleEl.textContent = data.role;
            activeCard.classList.add('testimonial-slide-in');

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIdx);
            });
        }

        function changeSlide(direction) {
            if (!testimonials.length) {
                return;
            }
            currentIdx = (currentIdx + direction + testimonials.length) % testimonials.length;
            updateSlider();
        }

        function currentSlide(index) {
            if (!testimonials.length) {
                return;
            }
            currentIdx = index % testimonials.length;
            updateSlider();
        }

        document.addEventListener('DOMContentLoaded', updateSlider);
        document.addEventListener('bgs-homepage-mounted', updateSlider);
    </script>

</div>
