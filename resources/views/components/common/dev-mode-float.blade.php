@php
    $devModeEmail = 'dev@dev.com';
    $authUser = auth()->user();

    if (!$authUser && auth('groomer_spacer')->check()) {
        $authUser = auth('groomer_spacer')->user();
    }

    $showDevMode = $authUser && ($authUser->email ?? null) === $devModeEmail;
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
            border-radius: 4px;
            background: #fff;
            position: relative;
            flex-shrink: 0;
        }

        .dev-mode-checkbox__box--checked {
            background: #ffedd1;
            border-color: #ffb54a;
        }

        .dev-mode-checkbox__box--checked::after {
            content: "";
            position: absolute;
            left: 4px;
            top: 1px;
            width: 4px;
            height: 8px;
            border: solid #7a4a00;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
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
    </style>

    <div class="dev-mode-float-wrap"
        x-data="{
            open: false,
            userTypeOpen: false,
            accountTypeOpen: false,
            selectedUserType: '{{ $authUser->user_type ?? '' }}',
            selectedAccountType: '{{ $authUser->account_type ?? '' }}'
        }"
        @click.outside="open = false; userTypeOpen = false; accountTypeOpen = false"
        @keydown.escape.window="open = false; userTypeOpen = false; accountTypeOpen = false">
        <button type="button" class="dev-mode-float" aria-label="Dev mode menu" @click="open = !open" :aria-expanded="open.toString()">
            <span class="dev-mode-float__dot" aria-hidden="true"></span>
            <span>DEV MODE</span>
            <span class="dev-mode-float__caret" aria-hidden="true">&#9662;</span>
        </button>

        <div class="dev-mode-dropdown" x-show="open" x-cloak x-transition.opacity.scale.95>
            <div class="dev-mode-dropdown__label">Developer Menu</div>

            <button type="button" class="dev-mode-dropdown__meta" @click="userTypeOpen = !userTypeOpen">
                <span>User Type:</span></span>
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
                <span>Account Type:</span></span>
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
            <div class="dev-mode-dropdown__actions">
                <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="dev-mode-dropdown__logout">Logout</button>
                </form>
                <form method="POST" action="{{ route('dev-mode.update-meta') }}" style="flex: 1;">
                    @csrf
                    <input type="hidden" name="user_type" x-model="selectedUserType">
                    <input type="hidden" name="account_type" x-model="selectedAccountType">
                    <button type="submit" class="dev-mode-dropdown__save">Update</button>
                </form>
            </div>
        </div>
    </div>
@endif
