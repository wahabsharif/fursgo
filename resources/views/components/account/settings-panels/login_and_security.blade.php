<h1 class="large-font">Login & Security</h1>

<div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
    <p class="bold-font">Current Password</p>

    <div class="d-flex align-items-center justify-content-between gap-25">
        <p style="color: #9D9B98" wire:key="password-updated-label">{{ $passwordUpdatedLabel }}</p>
        <button type="button" class="link-tag account-settings-link-btn" wire:click="openPasswordModal"
            wire:loading.attr="disabled" wire:target="openPasswordModal">
            Update Password
        </button>
    </div>
</div>

<div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
    <div class="d-flex flex-column gap-25">
        <p class="bold-font">2FA & Login Devices</p>
        <p style="color: #9D9B98"
            x-text="preferences.two_factor_enabled
                ? 'Two-factor authentication is enabled.'
                : 'Two-factor authentication is disabled.'">
        </p>
    </div>
    <div>
        <div class="toggle-switch" :class="{ 'on': preferences.two_factor_enabled }" role="switch" tabindex="0"
            :aria-checked="preferences.two_factor_enabled" @click.stop="togglePreference('two_factor_enabled')"
            @keydown.enter.prevent="togglePreference('two_factor_enabled')">
            <div class="toggle-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27"
                    fill="none">
                    <path
                        d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z"
                        fill="white" />
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="active-sessions mt-5">
    <p class="bold-font">Active Sessions</p>

    <div class="user-sessions mt-5" wire:key="active-sessions-list">
        @forelse ($activeSessions as $session)
            <div class="logged-devices d-flex align-items-center justify-content-between mt-3 pb-3"
                wire:key="login-session-{{ $session['id'] }}">
                <div class="d-flex align-items-center gap-20">
                    @if (in_array($session['device_type'], ['iphone', 'ipad', 'mobile'], true))
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="33" viewBox="0 0 23 33"
                            fill="none" aria-hidden="true">
                            <path
                                d="M16.71 0.75H5.79C3.00648 0.75 0.75 3.00648 0.75 5.79V26.79C0.75 29.5735 3.00648 31.83 5.79 31.83H16.71C19.4935 31.83 21.75 29.5735 21.75 26.79V5.79C21.75 3.00648 19.4935 0.75 16.71 0.75Z"
                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9.56982 25.9497H12.9298" stroke="#3B3731" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @elseif ($session['device_type'] === 'android')
                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="26" viewBox="0 0 23 26"
                            fill="none" aria-hidden="true">
                            <path
                                d="M0.75 10.35V18.35M21.75 10.35V18.35M3.98077 8.75H18.5192M3.98077 8.75V19.15C3.98077 19.7865 4.23606 20.397 4.69047 20.8471C5.14489 21.2971 5.76121 21.55 6.40385 21.55H16.0962C17.4369 21.55 18.5192 20.478 18.5192 19.15V8.75M3.98077 8.75C3.98077 4.766 7.22769 2.35 11.25 2.35C15.2723 2.35 18.5192 4.766 18.5192 8.75M5.59615 0.75L7.21154 3.15M16.9038 0.75L15.2885 3.15M7.21154 21.55V24.75M15.2885 21.55V24.75"
                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="17" viewBox="0 0 21 17"
                            fill="none" aria-hidden="true">
                            <path
                                d="M6.77246 16.2041H14.2275C14.2319 16.2041 14.2347 16.2054 14.2363 16.2061C14.2384 16.207 14.2408 16.2086 14.2432 16.2109C14.2457 16.2135 14.2471 16.2166 14.248 16.2188C14.2488 16.2204 14.25 16.223 14.25 16.2275C14.25 16.2319 14.2487 16.2347 14.248 16.2363C14.2471 16.2384 14.2456 16.2408 14.2432 16.2432C14.2408 16.2456 14.2384 16.2471 14.2363 16.248C14.2347 16.2487 14.2319 16.25 14.2275 16.25H6.77246C6.76811 16.25 6.76532 16.2487 6.76367 16.248C6.7616 16.2471 6.75922 16.2456 6.75684 16.2432C6.75445 16.2408 6.75291 16.2384 6.75195 16.2363C6.75126 16.2347 6.75003 16.2319 6.75 16.2275C6.75 16.223 6.75124 16.2204 6.75195 16.2188C6.75288 16.2166 6.75427 16.2135 6.75684 16.2109C6.75922 16.2086 6.76163 16.207 6.76367 16.2061C6.76532 16.2054 6.76811 16.2041 6.77246 16.2041ZM1 0.75H20C20.1381 0.75 20.25 0.861929 20.25 1V12.9092C20.25 13.0472 20.138 13.1592 20 13.1592H1C0.861958 13.1592 0.750048 13.0472 0.75 12.9092V1C0.75 0.861929 0.861929 0.75 1 0.75Z"
                                stroke="#3B3731" stroke-width="1.5" />
                        </svg>
                    @endif
                    <p>
                        Logged in on {{ $session['device_label'] }}
                        @if ($session['is_current'])
                            <span style="color:#9D9B98">(This device)</span>
                        @endif
                        -
                        <span style="color:#9D9B98">{{ $session['last_active_label'] }}</span>
                    </p>
                </div>
                @if ($session['is_current'])
                    <span class="small-link-tag" style="text-decoration: none; color: #9D9B98;">Current</span>
                @else
                    <button type="button" class="link-tag account-settings-link-btn"
                        wire:click="revokeLoginSession({{ $session['id'] }})" wire:confirm="Sign out this device?">
                        Sign out
                    </button>
                @endif
            </div>

            <hr style="border-top: 1px solid #E2E2E2;">
        @empty
            <p style="color: #9D9B98">No active sessions found yet. Browse the app and refresh this page.</p>
        @endforelse
    </div>
</div>

<div class="toggle-button-content d-flex flex-column justify-content-between mt-5 gap-25">
    <p class="bold-font mt-3">Deactivate your account</p>

    <div class="d-flex align-items-center justify-content-between gap-25">
        <p style="color: #9D9B98">This action will permanently delete your account.</p>
        <a href="" class="small-link-tag">Deactivate Account</a>
    </div>
</div>

@if ($showPasswordModal)
    @teleport('body')
        <div class="account-settings-modal-overlay is-open" role="dialog" aria-modal="true"
            aria-labelledby="update-password-modal-title" wire:click.self="closePasswordModal"
            x-on:keydown.escape.window="$wire.closePasswordModal()">
            <div class="account-settings-modal-card" @click.stop>
                <div class="account-settings-modal-header">
                    <h3 id="update-password-modal-title">Update Password</h3>
                    <button type="button" class="account-settings-modal-close" aria-label="Close"
                        wire:click="closePasswordModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                            fill="none">
                            <path d="M1 1L15 15M15 1L1 15" stroke="#3B3731" stroke-width="1.6" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <form class="account-settings-modal-form" wire:submit="updatePassword" x-data="{
                    password: @entangle('password').live,
                    confirmation: @entangle('password_confirmation').live,
                    get hasLength() { return this.password.length >= 8 },
                    get hasUpper() { return /[A-Z]/.test(this.password) },
                    get hasNumSym() { return /[\d\W]/.test(this.password) },
                    get strengthScore() {
                        return Number(this.hasLength) + Number(this.hasUpper) + Number(this.hasNumSym);
                    },
                    get strengthLabel() {
                        if (this.strengthScore >= 3) return 'Good Password';
                        if (this.strengthScore === 2) return 'Fair Password';
                        return 'Weak Password';
                    },
                    get strengthClass() {
                        if (this.strengthScore >= 3) return 'is-strong';
                        if (this.strengthScore === 2) return 'is-fair';
                        return 'is-weak';
                    },
                    get isStrong() { return this.strengthScore >= 3 },
                    get hasMismatch() {
                        return this.confirmation.length > 0 && this.password !== this.confirmation
                    },
                }">
                    <label class="account-settings-modal-field">
                        <span>Current password</span>
                        <input type="password" autocomplete="current-password" wire:model="current_password"
                            placeholder="Enter current password">
                        @error('current_password')
                            <small class="account-settings-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="account-settings-modal-field">
                        <span>New password</span>
                        <input type="password" autocomplete="new-password" x-model="password"
                            placeholder="At least 8 characters">
                        <div class="account-settings-password-meter" x-show="password.length > 0" x-cloak>
                            <div class="account-settings-password-meter__bar-row" :class="strengthClass">
                                <span class="account-settings-password-meter__label" x-text="strengthLabel"></span>
                                <div class="account-settings-password-meter__track" aria-hidden="true">
                                    <span class="account-settings-password-meter__fill"
                                        :style="`width: ${(strengthScore / 3) * 100}%`"></span>
                                </div>
                            </div>
                            <div class="account-settings-password-meter__rules">
                                <p>Password requirements</p>
                                <span :class="{ 'is-met': hasLength }">• At least 8 characters</span>
                                <span :class="{ 'is-met': hasUpper }">• Includes a capital letter</span>
                                <span :class="{ 'is-met': hasNumSym }">• Includes a number or symbol</span>
                            </div>
                        </div>
                        @error('password')
                            <small class="account-settings-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="account-settings-modal-field">
                        <span>Confirm new password</span>
                        <input type="password" autocomplete="new-password" x-model="confirmation"
                            placeholder="Re-enter new password" :class="{ 'is-invalid': hasMismatch }">
                        <small class="account-settings-modal-error" x-show="hasMismatch" x-cloak>
                            Passwords do not match.
                        </small>
                        @error('password_confirmation')
                            <small class="account-settings-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <div class="account-settings-modal-actions">
                        <button type="button" class="account-settings-modal-btn account-settings-modal-btn--light"
                            wire:click="closePasswordModal">
                            Cancel
                        </button>
                        <button type="submit" class="account-settings-modal-btn account-settings-modal-btn--primary"
                            wire:loading.attr="disabled" wire:target="updatePassword"
                            :disabled="password.length > 0 && (!isStrong || hasMismatch)">
                            <span wire:loading.remove wire:target="updatePassword">Save password</span>
                            <span wire:loading wire:target="updatePassword">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endteleport
@endif
