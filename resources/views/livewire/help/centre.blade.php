<?php

use App\Models\FaqArticle;
use App\Models\FaqCategory;
use App\Models\SupportTicket;
use App\Models\SupportTicketAttachment;
use App\Notifications\SupportTicketSubmitted;
use App\Support\HelpCentre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Url(as: 'q', except: '')]
    public string $query = '';

    #[Url(as: 'topic', except: '')]
    public string $category = '';

    public int $resultsLimit = 6;

    public ?int $openFaqId = null;

    /** @var list<int> */
    public array $openFaqIds = [];

    public bool $showTicketModal = false;

    public bool $categoryMenuOpen = false;

    public string $ticketSubject = '';

    public string $ticketCategory = '';

    public string $ticketBookingReference = '';

    public string $ticketDescription = '';

    public string $guestEmail = '';

    public array $ticketFiles = [];

    public ?string $submittedTicketNumber = null;

    public function mount(): void
    {
        $audience = HelpCentre::audience();

        if ($this->category === '') {
            $this->category = (string) (FaqCategory::query()
                ->forAudience($audience)
                ->orderBy('sort_order')
                ->value('slug') ?? 'bookings');
        }

        if ($audience === HelpCentre::AUDIENCE_BUSINESS && $this->openFaqIds === []) {
            $this->openFaqIds = FaqCategory::query()
                ->forAudience($audience)
                ->with(['articles' => fn($query) => $query->published()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get()
                ->map(fn(FaqCategory $category) => $category->articles->first()?->id)
                ->filter()
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();
        }
    }

    public function with(): array
    {
        $audience = HelpCentre::audience();
        $categories = FaqCategory::query()
            ->forAudience($audience)
            ->with(['articles' => fn($query) => $query->published()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $searching = filled(trim($this->query));
        $results = collect();
        $resultsTotal = 0;

        if ($searching) {
            $resultsQuery = FaqArticle::query()
                ->published()
                ->forAudience($audience)
                ->search($this->query)
                ->with('category')
                ->orderBy('sort_order');

            $resultsTotal = (clone $resultsQuery)->count();
            $results = $resultsQuery->limit($this->resultsLimit)->get();
        }

        return [
            'audience' => $audience,
            'isBusiness' => $audience === HelpCentre::AUDIENCE_BUSINESS,
            'categories' => $categories,
            'searching' => $searching,
            'results' => $results,
            'resultsTotal' => $resultsTotal,
            'isAuthenticated' => $this->isAuthenticated(),
            'isChatOnline' => HelpCentre::isChatOnline(),
            'ticketCategories' => HelpCentre::TICKET_CATEGORIES,
            'ticketCategoryLabel' => HelpCentre::TICKET_CATEGORIES[$this->ticketCategory] ?? 'Select a category',
        ];
    }

    public function updatedQuery(): void
    {
        $this->resultsLimit = 6;
        $this->openFaqId = null;
    }

    public function selectTopic(string $slug): void
    {
        $this->category = $slug;
        $this->openFaqId = null;
        $this->query = '';
        $this->resultsLimit = 6;
    }

    public function openArticle(int $id): void
    {
        $article = FaqArticle::query()
            ->published()
            ->forAudience(HelpCentre::audience())
            ->with('category')
            ->find($id);

        if (!$article?->category) {
            return;
        }

        $this->query = '';
        $this->category = $article->category->slug;
        $this->openFaqId = $article->id;
        $this->resultsLimit = 6;

        if (!in_array($article->id, $this->openFaqIds, true)) {
            $this->openFaqIds[] = $article->id;
        }
    }

    public function loadMore(): void
    {
        $this->resultsLimit += 6;
    }

    public function openTicketModal(): void
    {
        $this->resetErrorBag();
        $this->categoryMenuOpen = false;
        $this->showTicketModal = true;
    }

    public function closeTicketModal(): void
    {
        $this->showTicketModal = false;
        $this->categoryMenuOpen = false;
        $this->resetErrorBag();
        $this->reset([
            'ticketSubject',
            'ticketCategory',
            'ticketBookingReference',
            'ticketDescription',
            'guestEmail',
            'ticketFiles',
        ]);
    }

    public function selectTicketCategory(string $value): void
    {
        if (!array_key_exists($value, HelpCentre::TICKET_CATEGORIES)) {
            return;
        }

        $this->ticketCategory = $value;
        $this->categoryMenuOpen = false;
        $this->resetErrorBag('ticketCategory');
    }

    public function removeFile(int $index): void
    {
        unset($this->ticketFiles[$index]);
        $this->ticketFiles = array_values($this->ticketFiles);
    }

    /**
     * @param  array<int, string>  $names
     */
    public function keepTicketFiles(array $names): void
    {
        $remaining = array_values($names);

        $this->ticketFiles = array_values(array_filter($this->ticketFiles, function ($file) use (&$remaining) {
            $index = array_search($file->getClientOriginalName(), $remaining, true);

            if ($index === false) {
                return false;
            }

            unset($remaining[$index]);
            $remaining = array_values($remaining);

            return true;
        }));
    }

    public function submitRequest(): void
    {
        $this->categoryMenuOpen = false;

        $validated = $this->validate([
            'ticketSubject' => ['required', 'string', 'min:3', 'max:120'],
            'ticketCategory' => ['required', Rule::in(array_keys(HelpCentre::TICKET_CATEGORIES))],
            'ticketBookingReference' => ['nullable', 'string', 'max:80'],
            'ticketDescription' => ['required', 'string', 'min:10', 'max:5000'],
            'guestEmail' => [
                Rule::requiredIf(fn () => !$this->isAuthenticated() && HelpCentre::audience() !== HelpCentre::AUDIENCE_BUSINESS),
                'nullable',
                'email',
                'max:255',
            ],
            'ticketFiles' => ['nullable', 'array', 'max:20'],
            'ticketFiles.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx'],
        ]);

        $submitter = $this->submitter($validated['guestEmail'] ?? null);

        $ticket = SupportTicket::create([
            'ticket_number' => 'TMP-' . Str::lower((string) Str::ulid()),
            'user_id' => $submitter['user_id'],
            'goormer_spacer_id' => $submitter['goormer_spacer_id'],
            'email' => $submitter['email'],
            'subject' => $validated['ticketSubject'],
            'category' => $validated['ticketCategory'],
            'booking_reference' => $validated['ticketBookingReference'] ?: null,
            'description' => $validated['ticketDescription'],
            'status' => 'open',
            'audience' => HelpCentre::audience(),
        ]);

        $ticket->forceFill([
            'ticket_number' => 'FG-' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT),
        ])->save();

        foreach ($this->ticketFiles as $file) {
            $path = $file->store('support-tickets/' . $ticket->id, 'public');

            SupportTicketAttachment::create([
                'support_ticket_id' => $ticket->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => (int) $file->getSize(),
            ]);
        }

        if ($submitter['email'] !== '') {
            Notification::route('mail', $submitter['email'])->notify(new SupportTicketSubmitted($ticket->fresh()));
        }

        $this->submittedTicketNumber = $ticket->ticket_number;
        $this->closeTicketModal();
    }

    public function fileKilobytes(mixed $file): int
    {
        return max(1, (int) round(((int) $file->getSize()) / 1024));
    }

    public function ticketFileIcon(string $name): string
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        $icon = match ($extension) {
            'jpg', 'jpeg' => 'file-jpg.svg',
            'png' => 'file-png.svg',
            'webp' => 'file-webp.svg',
            'doc', 'docx' => 'file-doc.svg',
            default => 'file-pdf.svg',
        };

        return asset('images/help/' . $icon) . '?v=2';
    }

    private function isAuthenticated(): bool
    {
        return Auth::guard('web')->check() || Auth::guard('groomer_spacer')->check();
    }

    /**
     * @return array{user_id: int|null, goormer_spacer_id: int|null, email: string}
     */
    private function submitter(?string $guestEmail): array
    {
        $groomer = Auth::guard('groomer_spacer')->user();
        if ($groomer) {
            return [
                'user_id' => null,
                'goormer_spacer_id' => $groomer->id,
                'email' => (string) $groomer->email,
            ];
        }

        $user = Auth::guard('web')->user();
        if ($user) {
            return [
                'user_id' => $user->id,
                'goormer_spacer_id' => null,
                'email' => (string) $user->email,
            ];
        }

        return [
            'user_id' => null,
            'goormer_spacer_id' => null,
            'email' => (string) $guestEmail,
        ];
    }
}; ?>

<div class="row help-centre-page {{ $isBusiness ? 'is-business' : 'is-pet-owner' }}" x-data="{
    topic: @entangle('category'),
    openFaqId: @entangle('openFaqId'),
    openFaqIds: @entangle('openFaqIds'),
    showTicket: @entangle('showTicketModal'),
    ticketCategory: @entangle('ticketCategory'),
    isBusiness: @js($isBusiness),
    activeTopic: @js($category),
    categoryMenuOpen: false,
    subjectReady: {{ strlen(trim($ticketSubject)) >= 3 ? 'true' : 'false' }},
    ticketCategories: @js($ticketCategories),
    ignoreScrollSpy: false,
    scrollSpyRaf: null,
    scrollSpyReleaseTimer: null,
    init() {
        this._onCategoryMenuReposition = () => {
            if (this.categoryMenuOpen) {
                this.placeCategoryMenu();
            }
        };
        this.$watch('showTicket', (open) => {
            document.body.classList.toggle('help-modal-open', !!open);
            if (open) {
                this.categoryMenuOpen = false;
                window.addEventListener('resize', this._onCategoryMenuReposition);
                this.$nextTick(() => {
                    (this.$refs.ticketFirstInput || this.$refs.categoryTrigger)?.focus();
                    this.$refs.helpModalPanel?.addEventListener('scroll', this._onCategoryMenuReposition, { passive: true });
                });
            } else {
                window.removeEventListener('resize', this._onCategoryMenuReposition);
                this.$refs.helpModalPanel?.removeEventListener('scroll', this._onCategoryMenuReposition);
                this.$dispatch('help-ticket-reset-files');
            }
        });
        this.$watch('topic', (slug) => {
            if (slug) {
                this.activeTopic = slug;
            }
        });
        if (this.isBusiness) {
            this.bindTopicSpy();
        }
        this.$watch('categoryMenuOpen', (open) => {
            if (open) {
                this.$nextTick(() => this.placeCategoryMenu());
            }
        });
    },
    bindTopicSpy() {
        this._onTopicScroll = () => {
            if (this.ignoreScrollSpy) {
                return;
            }

            cancelAnimationFrame(this.scrollSpyRaf);
            this.scrollSpyRaf = requestAnimationFrame(() => {
                this.scrollSpyRaf = null;
                this.syncActiveTopicFromScroll();
            });
        };

        window.addEventListener('scroll', this._onTopicScroll, { passive: true });
        this.$nextTick(() => this.syncActiveTopicFromScroll());
        document.addEventListener('livewire:navigating', () => this.unbindTopicSpy(), { once: true });
    },
    unbindTopicSpy() {
        if (this._onTopicScroll) {
            window.removeEventListener('scroll', this._onTopicScroll);
            this._onTopicScroll = null;
        }
        if (this.scrollSpyRaf) {
            cancelAnimationFrame(this.scrollSpyRaf);
            this.scrollSpyRaf = null;
        }
        if (this.scrollSpyReleaseTimer) {
            clearTimeout(this.scrollSpyReleaseTimer);
            this.scrollSpyReleaseTimer = null;
        }
    },
    topicSlugFromSection(section) {
        const prefix = 'help-topic-';
        return section && section.id && section.id.indexOf(prefix) === 0
            ? section.id.slice(prefix.length)
            : '';
    },
    syncActiveTopicFromScroll() {
        if (!this.isBusiness || (this.$wire.query || '').trim()) {
            return;
        }

        const sections = Array.from(this.$el.querySelectorAll('.help-faq-section'));
        if (!sections.length) {
            return;
        }

        const tabs = this.$el.querySelector('.help-support-tabs-sticky');
        const line = (tabs ? tabs.getBoundingClientRect().bottom : 180) + 16;
        let active = sections[0];

        for (const section of sections) {
            if (section.getBoundingClientRect().top - line < 1) {
                active = section;
            }
        }

        const slug = this.topicSlugFromSection(active);
        if (slug && this.activeTopic !== slug) {
            this.activeTopic = slug;
        }
    },
    releaseScrollSpyAfterScroll() {
        if (this.scrollSpyReleaseTimer) {
            clearTimeout(this.scrollSpyReleaseTimer);
        }

        let settled = false;
        const finish = () => {
            if (settled) {
                return;
            }
            settled = true;
            window.removeEventListener('scrollend', finish);
            if (this.scrollSpyReleaseTimer) {
                clearTimeout(this.scrollSpyReleaseTimer);
                this.scrollSpyReleaseTimer = null;
            }
            this.ignoreScrollSpy = false;
            this.syncActiveTopicFromScroll();
        };

        window.addEventListener('scrollend', finish, { once: true });
        this.scrollSpyReleaseTimer = setTimeout(finish, 900);
    },
    selectTopic(slug) {
        this.topic = slug;
        this.activeTopic = slug;
        if (!this.isBusiness) {
            this.openFaqId = null;
        }
        if (($wire.query || '').trim() !== '') {
            $wire.selectTopic(slug);
        }
        if (this.isBusiness) {
            this.ignoreScrollSpy = true;
            this.$nextTick(() => {
                document.getElementById('help-topic-' + slug)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                this.releaseScrollSpyAfterScroll();
            });
        }
    },
    isFaqOpen(id) {
        if (this.isBusiness) {
            return this.openFaqIds.map(Number).includes(Number(id));
        }

        return this.openFaqId === id;
    },
    toggleFaq(id) {
        if (this.isBusiness) {
            const ids = this.openFaqIds.map(Number);
            const index = ids.indexOf(Number(id));
            if (index >= 0) {
                ids.splice(index, 1);
            } else {
                ids.push(Number(id));
            }
            this.openFaqIds = ids;
            this.$nextTick(() => this.syncActiveTopicFromScroll());
            return;
        }

        this.openFaqId = this.openFaqId === id ? null : id;
    },
    openTicket() {
        this.showTicket = true;
    },
    closeTicket() {
        if (!this.showTicket) {
            return;
        }
        this.showTicket = false;
        this.categoryMenuOpen = false;
        this.subjectReady = false;
        this.$dispatch('help-ticket-reset-files');
        $wire.closeTicketModal();
    },
    toggleCategoryMenu() {
        if (this.categoryMenuOpen) {
            this.categoryMenuOpen = false;
            return;
        }

        this.placeCategoryMenu();
        this.categoryMenuOpen = true;
        this.$nextTick(() => this.placeCategoryMenu());
    },
    placeCategoryMenu() {
        const trigger = this.$refs.categoryTrigger;
        const menu = this.$refs.categoryMenu;
        if (!trigger || !menu) {
            return;
        }

        const rect = trigger.getBoundingClientRect();
        const width = 235;
        const gap = 16;
        const openRight = window.innerWidth - rect.right >= width + gap + 16;

        menu.style.position = 'fixed';
        menu.style.zIndex = '2100';
        menu.style.width = width + 'px';
        menu.style.right = 'auto';

        if (openRight) {
            menu.style.top = rect.top + 'px';
            menu.style.left = (rect.right + gap) + 'px';
        } else {
            menu.style.top = (rect.bottom + 8) + 'px';
            menu.style.left = Math.max(16, rect.right - width) + 'px';
        }
    },
    closeCategoryMenuFromOutside(event) {
        if (!this.categoryMenuOpen) {
            return;
        }
        if (event.target.closest('.help-category-select') || event.target.closest('.help-category-options')) {
            return;
        }
        this.categoryMenuOpen = false;
    },
    chooseTicketCategory(value) {
        this.ticketCategory = value;
        this.categoryMenuOpen = false;
    },
    ticketCategoryLabel() {
        return this.ticketCategories[this.ticketCategory] || 'Select a category';
    }
}">
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
        @if ($submittedTicketNumber)
            <div class="help-ticket-banner mb-4" role="status">
                Done! Your request has been submitted (ticket {{ $submittedTicketNumber }}).
                Check your email inbox for a copy of this reference.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="top-head d-flex flex-column align-items-center justify-content-center">
                    <h1 class="large-font">
                        {{ $isBusiness ? 'Business Help & Support Centre' : 'Help & Support Center' }}
                    </h1>
                    <form wire:submit.prevent>
                        <div class="search-wrapper">
                            <input type="text" wire:model.live.debounce.400ms="query"
                                placeholder="{{ $isBusiness ? 'Find answers, manage issues, or contact the FursGo team.' : 'Search for topics like refunds, bookings, payments.' }}"
                                class="normal-font-weight" name="q" autocomplete="off">
                            <button class="search-btn" type="submit" aria-label="Search">
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
                        @foreach ($categories as $topic)
                            <p class="bg cursor {{ $category === $topic->slug && !$searching ? 'is-selected' : '' }}"
                                :class="{ 'is-selected': (isBusiness ? activeTopic : topic) === @js($topic->slug) && !($wire.query || '').trim() }"
                                @click.prevent="selectTopic(@js($topic->slug))" role="button">
                                {{ $topic->chip_label ?: $topic->name }}
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($searching)
                <div class="col-lg-12">
                    <div class="d-flex align-items-center mt-5">
                        <h1 class="large-font">Results</h1>
                    </div>
                </div>
                <div class="col-lg-12">
                    @if ($results->isEmpty())
                        <p class="simple-font mt-5 text-center">No articles matched “{{ $query }}”. Try another topic or
                            submit a request below.</p>
                    @else
                        <div class="help-results-grid mt-5">
                            @foreach ($results as $index => $article)
                                <div class="bg-div d-flex flex-column gap-30 help-result-card {{ $index === 0 ? '' : 'no-bg' }}"
                                    style="animation-delay: {{ $index * 60 }}ms">
                                    <div>
                                        <p class="normal-font-bold">{{ $article->question }}</p>
                                        <p class="simple-font mt-3">{{ $article->excerptText() }}</p>
                                    </div>
                                    <button type="button" class="underline normal-font-bold help-read-more"
                                        wire:click="openArticle({{ $article->id }})">Read more</button>
                                </div>
                            @endforeach
                        </div>
                        @if ($results->count() < $resultsTotal)
                            <div class="d-flex justify-content-center mt-5">
                                <button type="button" class="btn-custom btn-no-bg" wire:click="loadMore">Load More</button>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="col-lg-12">
                    <div class="d-flex align-items-center justify-content-center mt-5">
                        <h1 class="large-font">FAQs</h1>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="help-faq-layout">
                        <div class="help-support-tabs-sticky">
                            <div class="support-tabs d-flex flex-nowrap align-items-center justify-content-between">
                                @foreach ($categories as $tab)
                                    <button type="button"
                                        class="{{ $tab->slug }}-tab help-tabs tab-btn d-flex align-items-center flex-column cursor {{ $category === $tab->slug ? 'active' : '' }}"
                                        :class="{ active: (isBusiness ? activeTopic : topic) === @js($tab->slug) }"
                                        :aria-current="(isBusiness ? activeTopic : topic) === @js($tab->slug) ? 'true' : null"
                                        @click.prevent="selectTopic(@js($tab->slug))">
                                        <x-help.category-icon :icon="$tab->icon" />
                                        <p class="normal-font-bold mt-2">{{ $tab->name }}</p>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="{{ $isBusiness ? 'help-faq-stack' : 'help-panel tab-panels' }}">
                            @foreach ($categories as $tab)
                                <section
                                    class="{{ $isBusiness ? 'help-faq-section' : 'tab-panel help-tab-panel' }} {{ !$isBusiness && $category === $tab->slug ? 'active' : '' }}"
                                    id="help-topic-{{ $tab->slug }}" @unless ($isBusiness) x-show="topic === @js($tab->slug)"
                                        x-cloak x-transition:enter="help-tab-enter" x-transition:enter-start="help-tab-enter-start"
                                    x-transition:enter-end="help-tab-enter-end" @endunless wire:key="help-tab-{{ $tab->slug }}">
                                    <h2 class="large-font">{{ $tab->name }}</h2>
                                    <h3 class="normal-font-weight help-faq-subtitle">{{ $tab->subtitle }}</h3>
                                    @forelse ($tab->articles as $article)
                                        <div class="help-faq-block" style="animation-delay: {{ $loop->index * 50 }}ms">
                                            <button type="button"
                                                class="medium-font help-faq-item {{ $isBusiness ? (in_array($article->id, $openFaqIds, true) ? 'acc-active' : '') : ($openFaqId === $article->id ? 'acc-active' : '') }}"
                                                :class="{ 'acc-active': isFaqOpen({{ $article->id }}) }"
                                                @click.prevent="toggleFaq({{ $article->id }})">{{ $article->question }}</button>
                                            <div class="panel {{ $isBusiness ? (in_array($article->id, $openFaqIds, true) ? 'is-open' : '') : ($openFaqId === $article->id ? 'is-open' : '') }}"
                                                :class="{ 'is-open': isFaqOpen({{ $article->id }}) }">
                                                <p class="simple-font" style="white-space: pre-line;">{{ $article->answer }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="simple-font mt-4">No articles in this topic yet.</p>
                                    @endforelse
                                </section>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
        <x-ui.contact-support :$isChatOnline :$isBusiness />
    </div>
    <div class="col-lg-1"></div>

    <div class="help-request-modal" id="request_modal" x-show="showTicket" x-cloak
        x-transition:enter="help-modal-backdrop-enter" x-transition:enter-start="help-modal-backdrop-enter-start"
        x-transition:enter-end="help-modal-backdrop-enter-end" x-transition:leave="help-modal-backdrop-leave"
        x-transition:leave-start="help-modal-backdrop-leave-start"
        x-transition:leave-end="help-modal-backdrop-leave-end" @click.self="closeTicket()"
        @click="closeCategoryMenuFromOutside($event)" @keydown.escape.window="showTicket && closeTicket()" role="dialog"
        aria-modal="true" aria-labelledby="help-ticket-title" :aria-hidden="!showTicket">
        <div class="help-modal-panel" x-ref="helpModalPanel" @click.stop="closeCategoryMenuFromOutside($event)">
            <button type="button" class="help-modal-close" @click="closeTicket()" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                    <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                    <path d="M12.8 24.0008L24 12.8008M12.8 12.8008L24 24.0008" stroke="#3B3731" stroke-width="1.5"
                        stroke-linecap="round" />
                </svg>
            </button>

            <div class="help-modal-header">
                <h1 class="large-font" id="help-ticket-title">Submit a Request</h1>
                <p>Please provide as much detail as possible so our support team can help quickly.</p>
            </div>

            <form class="help-modal-form" wire:submit.prevent="submitRequest">
                @unless ($isAuthenticated || $isBusiness)
                    <div class="form-field">
                        <label>Email</label>
                        <div class="input-wrapper">
                            <input type="email" wire:model="guestEmail" x-ref="ticketFirstInput"
                                placeholder="Where should we send our reply?">
                        </div>
                        @error('guestEmail')
                            <small class="help-field-error">{{ $message }}</small>
                        @enderror
                    </div>
                @endunless

                <div class="form-field">
                    <label>Subject</label>
                    <div class="input-wrapper help-subject-input">
                        <input type="text" wire:model="ticketSubject" @if ($isAuthenticated || $isBusiness) x-ref="ticketFirstInput"
                        @endif @input="subjectReady = $event.target.value.trim().length >= 3"
                            placeholder="A short summary of your issue.">
                        <span class="help-subject-check" x-show="subjectReady" x-cloak aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                fill="none">
                                <path
                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                    fill="#C9DDA0" />
                            </svg>
                        </span>
                    </div>
                    @error('ticketSubject')
                        <small class="help-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-field">
                    <label>Category</label>
                    <div class="custom-select help-category-select"
                        :class="{ 'open': categoryMenuOpen, 'has-value': !!ticketCategory }">
                        <button type="button" class="select-trigger full-width" x-ref="categoryTrigger"
                            @click="toggleCategoryMenu()">
                            <span class="selected-text" x-text="ticketCategoryLabel()"></span>
                            <svg width="13" height="13" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M6 9l6 6 6-6" fill="none" stroke="#3B3731" stroke-width="2" />
                            </svg>
                        </button>
                    </div>
                    @error('ticketCategory')
                        <small class="help-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-field">
                    <label>Booking Reference <span>(Optional)</span></label>
                    <div class="input-wrapper">
                        <input type="text" wire:model="ticketBookingReference" placeholder="Enter your booking ID">
                    </div>
                </div>

                <div class="form-field">
                    <label>Description</label>
                    <div class="input-wrapper">
                        <textarea wire:model="ticketDescription" rows="3"
                            placeholder="Tell us what happened…"></textarea>
                    </div>
                    @error('ticketDescription')
                        <small class="help-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-field help-modal-attachments">
                    <label>Attachments</label>
                    <p class="help-modal-hint">Upload screenshots or documents</p>
                    <div class="upload-box" wire:ignore x-data="{
                            pendingFiles: [],
                            fileBlobs: [],
                            uploadProgress: 0,
                            fileUploading: false,
                            sizeTimer: null,
                            fileSeq: 0,
                            latestUploadIds: [],
                            captureTicketFiles(event) {
                                const files = Array.from(event.target.files || []);
                                event.target.value = '';

                                if (!files.length) {
                                    return;
                                }

                                const append = this.pendingFiles.length > 0;
                                const newItems = files.map((file) => ({
                                    id: ++this.fileSeq,
                                    name: file.name,
                                    sizeKb: Math.max(1, Math.round(file.size / 1024)),
                                    uploadedKb: 0,
                                }));

                                this.latestUploadIds = newItems.map((item) => item.id);
                                newItems.forEach((item) => this.pendingFiles.push(item));
                                files.forEach((file) => this.fileBlobs.push(file));
                                this.uploadProgress = 0;
                                this.fileUploading = true;
                                this.startSizeCounter();

                                this.$nextTick(() => this.uploadTicketFiles(files, append));
                            },
                            uploadTicketFiles(files, append) {
                                const upload = $wire.uploadMultiple || $wire.$uploadMultiple;
                                if (typeof upload !== 'function') {
                                    this.finishSizeCount();
                                    return;
                                }

                                upload(
                                    'ticketFiles',
                                    files,
                                    () => this.finishTicketUpload(),
                                    () => this.finishSizeCount(),
                                    (progress) => this.applyUploadProgress(progress),
                                    () => {},
                                    !!append
                                );
                            },
                            applyUploadProgress(progress) {
                                let percent = 0;
                                if (typeof progress === 'number') {
                                    percent = progress;
                                } else if (progress?.detail?.progress != null) {
                                    percent = progress.detail.progress;
                                } else if (progress?.loaded && progress?.total) {
                                    percent = Math.floor(progress.loaded / progress.total * 100);
                                }

                                this.uploadProgress = Math.max(0, Math.min(100, percent));
                                this.pendingFiles = this.pendingFiles.map((file) => {
                                    if (this.latestUploadIds.length && !this.latestUploadIds.includes(file.id)) {
                                        return file;
                                    }

                                    return {
                                        ...file,
                                        uploadedKb: Math.max(
                                            file.uploadedKb || 0,
                                            Math.round(file.sizeKb * this.uploadProgress / 100)
                                        ),
                                    };
                                });
                            },
                            startSizeCounter() {
                                this.stopSizeCounter();
                                this.sizeTimer = setInterval(() => {
                                    if (!this.fileUploading) {
                                        return;
                                    }

                                    this.pendingFiles = this.pendingFiles.map((file) => {
                                        const cap = Math.max(0, Math.ceil(file.sizeKb * 0.92));
                                        if ((file.uploadedKb || 0) >= cap) {
                                            return file;
                                        }

                                        const step = Math.max(1, Math.ceil(file.sizeKb / 18));
                                        return {
                                            ...file,
                                            uploadedKb: Math.min(cap, (file.uploadedKb || 0) + step),
                                        };
                                    });
                                }, 120);
                            },
                            finishSizeCount() {
                                this.stopSizeCounter();
                                this.sizeTimer = setInterval(() => {
                                    let allDone = true;
                                    this.pendingFiles = this.pendingFiles.map((file) => {
                                        if ((file.uploadedKb || 0) >= file.sizeKb) {
                                            return file;
                                        }

                                        allDone = false;
                                        const remaining = file.sizeKb - (file.uploadedKb || 0);
                                        const step = Math.max(1, Math.ceil(remaining / 4));
                                        return {
                                            ...file,
                                            uploadedKb: Math.min(file.sizeKb, (file.uploadedKb || 0) + step),
                                        };
                                    });

                                    if (allDone) {
                                        this.stopSizeCounter();
                                        this.fileUploading = false;
                                        $wire.keepTicketFiles(this.pendingFiles.map((file) => file.name));
                                    }
                                }, 70);
                            },
                            stopSizeCounter() {
                                if (this.sizeTimer) {
                                    clearInterval(this.sizeTimer);
                                    this.sizeTimer = null;
                                }
                            },
                            ticketFileLabel(name) {
                                const extension = (String(name).split('.').pop() || '').toLowerCase();
                                const labels = {
                                    pdf: 'PDF',
                                    jpg: 'JPG',
                                    jpeg: 'JPG',
                                    png: 'PNG',
                                    webp: 'WEBP',
                                    doc: 'DOC',
                                    docx: 'DOC',
                                };
                                return labels[extension] || 'PDF';
                            },
                            ticketFileStatus(file) {
                                const uploaded = Math.min(file.sizeKb, Math.max(0, file.uploadedKb || 0));

                                if (this.fileUploading && uploaded < file.sizeKb) {
                                    return uploaded + ' KB of ' + file.sizeKb + ' KB • Uploading...';
                                }

                                return file.sizeKb + ' KB of ' + file.sizeKb + ' KB';
                            },
                            finishTicketUpload() {
                                this.uploadProgress = 100;
                                this.finishSizeCount();
                            },
                            removeTicketFile(index) {
                                this.pendingFiles.splice(index, 1);
                                this.fileBlobs.splice(index, 1);
                                if (!this.fileUploading) {
                                    $wire.removeFile(index);
                                }
                            },
                            resetTicketFilesUi() {
                                this.stopSizeCounter();
                                this.pendingFiles = [];
                                this.fileBlobs = [];
                                this.latestUploadIds = [];
                                this.uploadProgress = 0;
                                this.fileUploading = false;
                                if (this.$refs.ticketFileInput) {
                                    this.$refs.ticketFileInput.value = '';
                                }
                            }
                        }" :class="{ 'has-files': fileUploading || pendingFiles.length > 0 }"
                        @help-ticket-reset-files.window="resetTicketFilesUi()">
                        <div class="upload-header">
                            <input type="file" id="helpTicketFiles" x-ref="ticketFileInput" multiple hidden
                                accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" @change="captureTicketFiles($event)">
                            <label class="help-upload-trigger" for="helpTicketFiles">
                                <span class="help-upload-icon">
                                    <img src="{{ asset('images/help/attach.svg') }}" width="11" height="12" alt="">
                                </span>
                                Attach
                            </label>
                            <span class="help-upload-divider" aria-hidden="true"></span>
                            <label class="help-upload-trigger" for="helpTicketFiles">
                                <span class="help-upload-icon help-upload-icon--image">
                                    <img src="{{ asset('images/help/upload.svg') }}" width="12" height="12" alt="">
                                </span>
                                Upload
                            </label>
                        </div>

                        <div class="help-file-list">
                            <template x-for="(file, index) in pendingFiles" :key="file.id">
                                <div class="file-item is-visible">
                                    <div class="file-left">
                                        <span class="help-file-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="25"
                                                viewBox="0 0 21 25" fill="none" aria-hidden="true">
                                                <path
                                                    d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z"
                                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355"
                                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                <text x="10.5" y="20.4" text-anchor="middle" fill="#9D9B98"
                                                    font-family="Lato, Arial, sans-serif" font-weight="700"
                                                    :font-size="ticketFileLabel(file.name).length > 3 ? 5.5 : 7"
                                                    x-text="ticketFileLabel(file.name)"></text>
                                            </svg>
                                        </span>
                                        <div class="file-info">
                                            <div class="help-file-name" x-text="file.name"></div>
                                            <div class="file-size" x-text="ticketFileStatus(file)"></div>
                                        </div>
                                    </div>
                                    <button class="remove-btn" type="button" @click="removeTicketFile(index)"
                                        aria-label="Remove file">
                                        <span class="help-file-remove-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                viewBox="0 0 11.5 11.5" fill="none" aria-hidden="true">
                                                <path d="M0.75 10.75L10.75 0.75M0.75 0.75L10.75 10.75" stroke="#3B3731"
                                                    stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('ticketFiles.*')
                        <small class="help-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="help-modal-actions">
                    <button type="button" class="help-modal-cancel" @click="closeTicket()">Cancel</button>
                    <button type="submit" class="help-modal-submit" wire:loading.attr="disabled"
                        wire:target="submitRequest">
                        <span wire:loading.remove wire:target="submitRequest">Submit Request</span>
                        <span wire:loading wire:target="submitRequest">Submitting…</span>
                    </button>
                </div>
            </form>
        </div>
        <ul class="select-options help-category-options" x-ref="categoryMenu" x-show="categoryMenuOpen" x-cloak
            x-transition:enter="help-category-menu-enter" x-transition:enter-start="help-category-menu-enter-start"
            x-transition:enter-end="help-category-menu-enter-end" x-transition:leave="help-category-menu-leave"
            x-transition:leave-start="help-category-menu-leave-start"
            x-transition:leave-end="help-category-menu-leave-end" @click.stop>
            @foreach ($ticketCategories as $value => $label)
                <li :class="{ 'is-active': ticketCategory === @js($value) }" @click="chooseTicketCategory(@js($value))"
                    role="option">{{ $label }}</li>
            @endforeach
        </ul>
    </div>
</div>