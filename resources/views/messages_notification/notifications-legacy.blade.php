@extends('layouts.dashboard')

@section('title', 'Fursgo - Notifications')

@section('content')
    @php
        $noteIcon = fn(string $file) => asset('images/notifications/' . $file);
        $noteProfile = Auth::guard('groomer_spacer')->user();
        $noteIsSpace = $noteProfile instanceof \App\Models\GroomerSpacerProfile && strtolower((string) ($noteProfile->user_type ?? '')) === 'space';
    @endphp

    <section class="biz-notes{{ $noteIsSpace ? ' biz-notes--space' : '' }}" aria-labelledby="biz-notes-title">
        <h1 class="biz-notes-title" id="biz-notes-title">Notifications</h1>
        <p class="biz-notes-lead">Everything happening across your FursGo business.</p>

        <div class="biz-notes-pills" role="tablist" aria-label="Notification filters">
            <button type="button" class="biz-notes-pill is-active" data-filter="all" role="tab" aria-selected="true">All (10)</button>
            <button type="button" class="biz-notes-pill" data-filter="bookings" role="tab" aria-selected="false">Bookings (2)</button>
            <button type="button" class="biz-notes-pill" data-filter="reviews" role="tab" aria-selected="false">Reviews (1)</button>
            <button type="button" class="biz-notes-pill" data-filter="payments" role="tab" aria-selected="false">Payments (2)</button>
            <button type="button" class="biz-notes-pill" data-filter="system" role="tab" aria-selected="false">System (1)</button>
        </div>

        <div class="biz-notes-tools">
            <label class="biz-notes-search">
                <span class="visually-hidden">Search notifications</span>
                <input type="search" placeholder="Search by client name, pets, client number ..." autocomplete="off">
                <img src="{{ $noteIcon('icon-search.svg') }}" alt="" width="16" height="16">
            </label>
            <div class="biz-notes-sort">
                <img src="{{ $noteIcon('icon-sort.svg') }}" alt="" width="15" height="11">
                <span><strong>Sort:</strong> Newest first</span>
            </div>
            <div class="biz-notes-more-wrap">
                <button type="button" class="biz-notes-more" aria-label="Notification actions" aria-expanded="false" aria-controls="biz-notes-menu">
                    <img src="{{ $noteIcon('icon-more.svg') }}" alt="" width="36" height="36">
                </button>
                <div class="biz-notes-menu" id="biz-notes-menu" hidden>
                    <button type="button" class="biz-notes-menu-item is-current" data-action="read">
                        <span>Mark All Read</span>
                        <img src="{{ $noteIcon('icon-mark-read.svg') }}" alt="" width="13" height="13">
                    </button>
                    <button type="button" class="biz-notes-menu-item" data-action="clear">
                        <span>Clear All</span>
                        <img src="{{ $noteIcon('icon-clear.svg') }}" alt="" width="12" height="14">
                    </button>
                </div>
            </div>
        </div>

        <div class="biz-notes-list">
            <article class="biz-notes-row is-unread" data-category="bookings">
                <span class="biz-notes-tile biz-notes-tile--booking">
                    <img src="{{ $noteIcon('note-calendar.svg') }}" alt="" width="22" height="20">
                </span>
                <p class="biz-notes-copy">
                    <strong>David E.</strong> confirmed her booking for 27 Nov, at 15:30.
                    <span class="biz-notes-time">20m ago</span>
                </p>
                <img class="biz-notes-chevron" src="{{ $noteIcon('chevron-unread.svg') }}" alt="" width="10" height="11">
            </article>
            <hr class="biz-notes-rule">
            <article class="biz-notes-row is-unread" data-category="reviews">
                <img class="biz-notes-tile-img" src="{{ $noteIcon('note-review.svg') }}" alt="" width="50" height="50">
                <p class="biz-notes-copy">
                    <strong>Jane S.</strong> left you a 5-star review - "Rebecca is wonderful with Pip — so patient."
                    <span class="biz-notes-time">20m ago</span>
                </p>
                <img class="biz-notes-chevron" src="{{ $noteIcon('chevron-unread.svg') }}" alt="" width="10" height="11">
            </article>
            <hr class="biz-notes-rule">
            <article class="biz-notes-row is-unread" data-category="system">
                <span class="biz-notes-tile biz-notes-tile--settings">
                    <img src="{{ $noteIcon('note-settings.svg') }}" alt="" width="21" height="19">
                </span>
                <p class="biz-notes-copy">
                    <strong>Settings System</strong> update scheduled for Monday 12 Oct at 02:00...
                    <span class="biz-notes-time">20m ago</span>
                </p>
                <img class="biz-notes-chevron" src="{{ $noteIcon('chevron-read.svg') }}" alt="" width="10" height="11">
            </article>
            <hr class="biz-notes-rule">
            <article class="biz-notes-row" data-category="payments">
                <img class="biz-notes-tile-img" src="{{ $noteIcon('note-payout.svg') }}" alt="" width="50" height="50">
                <p class="biz-notes-copy">
                    Pay-out of <strong>£425.00</strong> is on its way to your bank
                    <span class="biz-notes-time">20m ago</span>
                </p>
                <img class="biz-notes-chevron" src="{{ $noteIcon('chevron-read.svg') }}" alt="" width="10" height="11">
            </article>
            <hr class="biz-notes-rule">
            <article class="biz-notes-row" data-category="account">
                <span class="biz-notes-tile biz-notes-tile--account">
                    <img src="{{ $noteIcon('note-bell.svg') }}" alt="" width="19" height="20">
                </span>
                <p class="biz-notes-copy">
                    <strong>Account update</strong> Your profile has been updated successfully.
                    <span class="biz-notes-time">20m ago</span>
                </p>
                <img class="biz-notes-chevron" src="{{ $noteIcon('chevron-read.svg') }}" alt="" width="10" height="11">
            </article>
        </div>

        <p class="biz-notes-empty" hidden>No notifications to show.</p>
        <button type="button" class="biz-notes-load">Load More</button>
    </section>
@endsection

@push('script')
    <script>
        (function() {
            if (window.__bizNotesBooted) {
                return;
            }
            window.__bizNotesBooted = true;

            const readChevron = @json(asset('images/notifications/chevron-read.svg'));

            function closeMenu(root) {
                const menu = root.querySelector('.biz-notes-menu');
                const more = root.querySelector('.biz-notes-more');
                if (!menu || !more) return;
                menu.hidden = true;
                more.setAttribute('aria-expanded', 'false');
            }

            function initBizNotes() {
                const root = document.querySelector('.biz-notes');
                if (!root || root.dataset.notesReady === '1') return;
                root.dataset.notesReady = '1';

                const pills = [...root.querySelectorAll('.biz-notes-pill')];
                const rows = [...root.querySelectorAll('.biz-notes-row')];
                const rules = [...root.querySelectorAll('.biz-notes-rule')];
                const search = root.querySelector('.biz-notes-search input');
                const more = root.querySelector('.biz-notes-more');
                const menu = root.querySelector('.biz-notes-menu');
                const empty = root.querySelector('.biz-notes-empty');
                let filter = 'all';
                let cleared = false;

                const syncRules = () => {
                    const visible = rows.filter((row) => !row.hidden);
                    rules.forEach((rule) => {
                        rule.hidden = true;
                    });
                    visible.forEach((row) => {
                        row.style.marginTop = '';
                    });
                    visible.forEach((row, index) => {
                        if (index === 0) return;
                        const previous = visible[index - 1];
                        const rule = previous.nextElementSibling?.classList.contains('biz-notes-rule') ?
                            previous.nextElementSibling :
                            null;
                        if (!rule) {
                            row.style.marginTop = '20px';
                            return;
                        }
                        rule.hidden = false;
                    });
                    if (empty) empty.hidden = visible.length > 0;
                };

                const apply = () => {
                    const query = search.value.trim().toLowerCase();
                    rows.forEach((row) => {
                        const matchesFilter = filter === 'all' || row.dataset.category === filter;
                        const matchesQuery = query === '' || row.textContent.toLowerCase().includes(query);
                        row.hidden = cleared || !matchesFilter || !matchesQuery;
                    });
                    syncRules();
                };

                pills.forEach((pill) => {
                    pill.addEventListener('click', () => {
                        filter = pill.dataset.filter;
                        pills.forEach((item) => {
                            const active = item === pill;
                            item.classList.toggle('is-active', active);
                            item.setAttribute('aria-selected', active ? 'true' : 'false');
                        });
                        apply();
                    });
                });

                search.addEventListener('input', apply);

                more.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    const open = menu.hidden;
                    menu.hidden = !open;
                    more.setAttribute('aria-expanded', open ? 'true' : 'false');
                });

                menu.addEventListener('click', (event) => {
                    const action = event.target.closest('[data-action]')?.dataset.action;
                    if (!action) return;
                    if (action === 'read') {
                        rows.forEach((row) => {
                            row.classList.remove('is-unread');
                            const chevron = row.querySelector('.biz-notes-chevron');
                            if (chevron) chevron.src = readChevron;
                        });
                        syncRules();
                    }
                    if (action === 'clear') {
                        cleared = true;
                        apply();
                    }
                    closeMenu(root);
                });
            }

            document.addEventListener('click', (event) => {
                const root = document.querySelector('.biz-notes');
                if (!root) return;
                const wrap = root.querySelector('.biz-notes-more-wrap');
                if (wrap && !wrap.contains(event.target)) {
                    closeMenu(root);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                const root = document.querySelector('.biz-notes');
                if (root) closeMenu(root);
            });

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initBizNotes);
            } else {
                initBizNotes();
            }

            document.addEventListener('livewire:navigated', initBizNotes);
        })();
    </script>
@endpush
