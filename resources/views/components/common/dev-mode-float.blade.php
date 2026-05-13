@php
    $devModeEmail = 'dev@dev.com';
    $groomerSpacerUser = auth('groomer_spacer')->user();
    $webUser = auth()->user();
    $authUser = $groomerSpacerUser ?? $webUser;

    $showDevMode = $authUser && ($authUser->email ?? null) === $devModeEmail;
    // Avoid resolving optional profile relations for normal users/tests.
    $metaUser = $showDevMode ? $groomerSpacerUser ?? ($webUser?->groomerSpacerProfile ?? $webUser) : $authUser;
@endphp

@if ($showDevMode)
    <style>
        .dev-mode-float-wrap {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 2000;
            display: inline-block;
        }

        .dev-mode-float-wrap.is-dragging {
            user-select: none;
        }

        .dev-mode-float {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #ffb54a;
            background: #fff8ea;
            color: #7a4a00;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            user-select: none;
        }

        .dev-mode-float:hover {
            filter: brightness(0.98);
        }

        .dev-mode-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 200px;
            display: inline-flex;
            flex-direction: column;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid #f0d6a8;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .dev-mode-dropdown--left {
            right: auto;
            left: 0;
        }

        .dev-mode-dropdown__label {
            padding: 6px 8px;
            color: #7a4a00;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #f4ede1;
            margin-bottom: 6px;
        }

        .dev-mode-dropdown__item {
            display: block;
            padding: 8px;
            border-radius: 8px;
            color: #3b3731;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .dev-mode-dropdown__item:hover {
            background: #fff8ea;
        }

        .dev-mode-dropdown__hint {
            padding: 6px 8px 2px;
            color: #8a857f;
            font-size: 12px;
            font-weight: 500;
        }

        .dev-mode-dropdown__save {
            width: 100%;
            border: 0;
            border-radius: 8px;
            background: #ffb54a;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 10px;
            cursor: pointer;
        }

        .dev-mode-dropdown__save:hover {
            filter: brightness(0.96);
        }

        .dev-mode-dropdown__actions {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .dev-mode-dropdown__logout {
            width: 100%;
            border: 1px solid #f0d6a8;
            border-radius: 8px;
            background: #fff8ea;
            color: #7a4a00;
            font-size: 12px;
            font-weight: 700;
            padding: 8px 10px;
            cursor: pointer;
        }

        .dev-mode-dropdown__logout:hover {
            filter: brightness(0.98);
        }

        .dev-mode-dropdown__meta {
            padding: 4px 8px;
            color: #5e5a55;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 5px 0;
        }

        .dev-mode-dropdown__meta:hover {
            background: #fff8ea;
        }

        .dev-mode-dropdown__meta-caret {
            font-size: 10px;
            color: #8a857f;
        }

        .dev-mode-dropdown__meta-panel {
            padding: 4px 8px 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .dev-mode-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #3b3731;
            font-size: 12px;
            font-weight: 600;
            width: 100%;
            border: 0;
            background: transparent;
            padding: 2px 0;
            text-align: left;
            cursor: pointer;
        }

        .dev-mode-checkbox__box {
            width: 14px;
            height: 14px;
            border: 1px solid #d6b27a;
            border-radius: 50%;
            background: #fff;
            position: relative;
            flex-shrink: 0;
            transition: border-color 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .dev-mode-checkbox__box::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #ffb54a;
            transform: scale(0);
            opacity: 0;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.18s ease;
        }

        .dev-mode-checkbox__box--checked {
            border-color: #ffb54a;
            box-shadow: 0 0 0 2px rgba(255, 181, 74, 0.15);
        }

        .dev-mode-checkbox__box--checked::after {
            transform: scale(1);
            opacity: 1;
        }

        .dev-mode-float__dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #ff9800;
        }

        .dev-mode-float__caret {
            margin-left: 2px;
            font-size: 10px;
            line-height: 1;
        }

        .dev-mode-float__grip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #c08a3a;
            margin-right: 2px;
            padding: 2px 4px;
            margin-left: -4px;
            line-height: 0;
            opacity: 0.85;
            cursor: grab;
            touch-action: none;
            border-radius: 4px;
        }

        .dev-mode-float__grip:hover {
            opacity: 1;
            background: rgba(192, 138, 58, 0.12);
        }

        .dev-mode-float-wrap.is-dragging .dev-mode-float__grip,
        .dev-mode-float__grip:active {
            cursor: grabbing;
        }

        .dev-mode-float__grip svg {
            display: block;
            pointer-events: none;
        }

        .dev-mode-float__right {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
            padding-left: 8px;
        }
    </style>

    <div class="dev-mode-float-wrap" x-data="{
        open: false,
        userTypeOpen: false,
        accountTypeOpen: false,
        selectedUserType: '{{ $metaUser->user_type ?? '' }}',
        selectedAccountType: '{{ $metaUser->account_type ?? '' }}',
        saveBusy: false,
        saveError: '',
        async submitMeta() {
            this.saveBusy = true;
            this.saveError = '';
            try {
                const response = await fetch('{{ route('dev-mode.update-meta') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        user_type: this.selectedUserType,
                        account_type: this.selectedAccountType,
                    }),
                });
    
                if (!response.ok) {
                    let message = 'Update failed';
                    try {
                        const data = await response.json();
                        message = data.message || message;
                    } catch (e) {}
                    this.saveError = message;
                    return;
                }
    
                window.location.reload();
            } catch (e) {
                this.saveError = 'Network error while updating';
            } finally {
                this.saveBusy = false;
            }
        }
    }"
        @click.outside="open = false; userTypeOpen = false; accountTypeOpen = false"
        @keydown.escape.window="open = false; userTypeOpen = false; accountTypeOpen = false">
        <button type="button" class="dev-mode-float" aria-label="Dev mode menu (drag to move)" @click="open = !open"
            :aria-expanded="open.toString()">
            <span class="dev-mode-float__grip" aria-hidden="true" title="Drag to move">
                <svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="2" cy="2" r="1.3" />
                    <circle cx="8" cy="2" r="1.3" />
                    <circle cx="2" cy="7" r="1.3" />
                    <circle cx="8" cy="7" r="1.3" />
                    <circle cx="2" cy="12" r="1.3" />
                    <circle cx="8" cy="12" r="1.3" />
                </svg>
            </span>
            <span class="dev-mode-float__right">
                <span class="dev-mode-float__dot" aria-hidden="true"></span>
                <span>DEV MODE</span>
                <span class="dev-mode-float__caret" aria-hidden="true">&#9662;</span>
            </span>
        </button>

        <div class="dev-mode-dropdown" x-show="open" x-cloak x-transition.opacity.scale.95>
            <div class="dev-mode-dropdown__label">Developer Menu</div>

            <button type="button" class="dev-mode-dropdown__meta" @click="userTypeOpen = !userTypeOpen">
                <span>User Type:</span>
                <span class="dev-mode-dropdown__meta-caret" x-text="userTypeOpen ? '▲' : '▼'"></span>
            </button>
            <div class="dev-mode-dropdown__meta-panel" x-show="userTypeOpen" x-transition.opacity>
                <button type="button" class="dev-mode-checkbox" @click="selectedUserType = 'groomer'">
                    <span class="dev-mode-checkbox__box"
                        :class="{ 'dev-mode-checkbox__box--checked': selectedUserType === 'groomer' }"></span>
                    <span>Groomer</span>
                </button>
                <button type="button" class="dev-mode-checkbox" @click="selectedUserType = 'space'">
                    <span class="dev-mode-checkbox__box"
                        :class="{ 'dev-mode-checkbox__box--checked': selectedUserType === 'space' }"></span>
                    <span>Space</span>
                </button>
            </div>

            <button type="button" class="dev-mode-dropdown__meta" @click="accountTypeOpen = !accountTypeOpen">
                <span>Account Type:</span>
                <span class="dev-mode-dropdown__meta-caret" x-text="accountTypeOpen ? '▲' : '▼'"></span>
            </button>
            <div class="dev-mode-dropdown__meta-panel" x-show="accountTypeOpen" x-transition.opacity>
                <button type="button" class="dev-mode-checkbox" @click="selectedAccountType = 'freelance'">
                    <span class="dev-mode-checkbox__box"
                        :class="{ 'dev-mode-checkbox__box--checked': selectedAccountType === 'freelance' }"></span>
                    <span>Freelancer</span>
                </button>
                <button type="button" class="dev-mode-checkbox" @click="selectedAccountType = 'registered_business'">
                    <span class="dev-mode-checkbox__box"
                        :class="{ 'dev-mode-checkbox__box--checked': selectedAccountType === 'registered_business' }"></span>
                    <span>Business</span>
                </button>
            </div>

            <div class="dev-mode-dropdown__hint">Logged in as {{ $devModeEmail }}</div>
            <div class="dev-mode-dropdown__hint" x-show="saveError" x-text="saveError" style="color: #b42318;"></div>
            <div class="dev-mode-dropdown__actions">
                <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="dev-mode-dropdown__logout">Logout</button>
                </form>
                <button type="button" class="dev-mode-dropdown__save" style="flex: 1;" :disabled="saveBusy"
                    @click="submitMeta()" x-text="saveBusy ? 'Updating...' : 'Update'"></button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const STORAGE_KEY = 'devModeFloatPos';
            const THRESHOLD = 4;

            function init() {
                const wrap = document.querySelector('.dev-mode-float-wrap');
                if (!wrap) return;
                const handle = wrap.querySelector('.dev-mode-float__grip');
                if (!handle) return;

                let dragging = false;
                let moved = false;
                let offsetX = 0,
                    offsetY = 0;
                let startX = 0,
                    startY = 0;

                function applyPosition(x, y) {
                    const w = wrap.offsetWidth || 130;
                    const h = wrap.offsetHeight || 36;
                    const maxX = Math.max(4, window.innerWidth - w - 4);
                    const maxY = Math.max(4, window.innerHeight - h - 4);
                    const cx = Math.min(Math.max(4, x), maxX);
                    const cy = Math.min(Math.max(4, y), maxY);
                    wrap.style.left = cx + 'px';
                    wrap.style.top = cy + 'px';
                    wrap.style.right = 'auto';
                    const dropdown = wrap.querySelector('.dev-mode-dropdown');
                    if (dropdown) {
                        if ((cx + w / 2) < (window.innerWidth / 2)) {
                            dropdown.classList.add('dev-mode-dropdown--left');
                        } else {
                            dropdown.classList.remove('dev-mode-dropdown--left');
                        }
                    }
                }

                try {
                    const saved = localStorage.getItem(STORAGE_KEY);
                    if (saved) {
                        const p = JSON.parse(saved);
                        if (p && typeof p.x === 'number' && typeof p.y === 'number') {
                            applyPosition(p.x, p.y);
                        }
                    }
                } catch (e) {}

                function getPoint(e) {
                    if (e.touches && e.touches.length) return e.touches[0];
                    if (e.changedTouches && e.changedTouches.length) return e.changedTouches[0];
                    return e;
                }

                function onMove(e) {
                    if (!dragging) return;
                    const p = getPoint(e);
                    const dx = p.clientX - startX;
                    const dy = p.clientY - startY;
                    if (!moved && (Math.abs(dx) > THRESHOLD || Math.abs(dy) > THRESHOLD)) {
                        moved = true;
                        wrap.classList.add('is-dragging');
                    }
                    if (moved) {
                        applyPosition(p.clientX - offsetX, p.clientY - offsetY);
                        if (e.cancelable) e.preventDefault();
                    }
                }

                function onUp() {
                    if (!dragging) return;
                    dragging = false;
                    window.removeEventListener('mousemove', onMove);
                    window.removeEventListener('mouseup', onUp);
                    window.removeEventListener('touchmove', onMove);
                    window.removeEventListener('touchend', onUp);
                    window.removeEventListener('touchcancel', onUp);
                    wrap.classList.remove('is-dragging');
                    if (moved) {
                        const r = wrap.getBoundingClientRect();
                        try {
                            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                                x: r.left,
                                y: r.top
                            }));
                        } catch (e) {}
                        const suppress = function(ev) {
                            ev.stopPropagation();
                            ev.preventDefault();
                            window.removeEventListener('click', suppress, true);
                        };
                        window.addEventListener('click', suppress, true);
                        setTimeout(function() {
                            window.removeEventListener('click', suppress, true);
                            moved = false;
                        }, 50);
                    }
                }

                function onDown(e) {
                    if (e.type === 'mousedown' && e.button !== 0) return;
                    const p = getPoint(e);
                    const rect = wrap.getBoundingClientRect();
                    offsetX = p.clientX - rect.left;
                    offsetY = p.clientY - rect.top;
                    startX = p.clientX;
                    startY = p.clientY;
                    moved = false;
                    dragging = true;
                    if (e.type === 'touchstart' && e.cancelable) e.preventDefault();
                    window.addEventListener('mousemove', onMove);
                    window.addEventListener('mouseup', onUp);
                    window.addEventListener('touchmove', onMove, {
                        passive: false
                    });
                    window.addEventListener('touchend', onUp);
                    window.addEventListener('touchcancel', onUp);
                }

                handle.addEventListener('mousedown', onDown);
                handle.addEventListener('touchstart', onDown, {
                    passive: false
                });

                handle.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    ev.preventDefault();
                });

                window.addEventListener('resize', function() {
                    const r = wrap.getBoundingClientRect();
                    applyPosition(r.left, r.top);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endif
