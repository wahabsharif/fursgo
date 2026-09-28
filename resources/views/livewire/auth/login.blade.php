<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.app')]
    class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public bool $loginFailed = false;

    public ?bool $emailExists = null;

    public string $emailCheckStatus = 'idle';

    public string $emailErrorMessage = '';

    /**
     * Validate email format and whether an account exists (realtime while typing).
     */
    #[Renderless]
    public function checkEmail(): void
    {
        $this->loginFailed = false;
        $email = Str::lower(trim($this->email));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = false;
            $this->emailCheckStatus = $email === '' ? 'idle' : 'invalid';
            $this->emailErrorMessage = '';

            return;
        }

        $this->email = $email;
        $userExists = \DB::table('users')->where('email', $email)->exists();
        $groomerExists = \DB::table('goormer_spacer_profiles')->where('email', $email)->exists();
        $this->emailExists = $userExists || $groomerExists;
        $this->emailCheckStatus = $this->emailExists ? 'valid' : 'invalid';
        $this->emailErrorMessage = $this->emailExists ? '' : 'No account found with this email.';
    }

    public function login(): void
    {
        $this->loginFailed = false;
        $this->emailErrorMessage = '';
        $this->email = Str::lower(trim($this->email));
        $this->validate();
        $this->ensureIsNotRateLimited();

        // Match signup (lowercase email) so lookups and attempt() use the same value as the DB.
        $isGroomerSpacer = \DB::table('goormer_spacer_profiles')->where('email', $this->email)->exists();

        // Authenticate against the appropriate guard
        if ($isGroomerSpacer) {
            // Try to authenticate as groomer_spacer
            if (!Auth::guard('groomer_spacer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                RateLimiter::hit($this->throttleKey());
                $this->loginFailed = true;
                $this->emailCheckStatus = 'invalid';
                $this->emailErrorMessage = __('auth.failed');

                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            RateLimiter::clear($this->throttleKey());
            request()->session()->regenerate();
            Auth::shouldUse('groomer_spacer');

            $default = route('business-hub');
            $target = session()->pull('url.intended', $default);
            $this->redirect($this->safePostLoginUrl($target, $default), navigate: false);

            return;
        } else {
            // Try to authenticate as regular user
            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                RateLimiter::hit($this->throttleKey());
                $this->loginFailed = true;
                $this->emailCheckStatus = 'invalid';
                $this->emailErrorMessage = __('auth.failed');

                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            RateLimiter::clear($this->throttleKey());
            request()->session()->regenerate();

            $default = route('pet-owner-profile');
            $target = session()->pull('url.intended', $default);
            if ($target === route('business-hub')) {
                $target = $default;
            }
            $this->redirect($this->safePostLoginUrl($target, $default), navigate: false);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->emailCheckStatus = 'invalid';
        $this->emailErrorMessage = __('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Ignore leftover auth pages stored as url.intended (for example /verify-email).
     */
    protected function safePostLoginUrl(mixed $target, string $fallback): string
    {
        if (!is_string($target) || $target === '') {
            return $fallback;
        }

        $path = parse_url($target, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? '/' . ltrim($path, '/') : $target;

        $blocked = [
            '/login',
            '/login-signup/login',
            '/login-signup/signup',
            '/login-groomer-space',
            '/signup',
            '/signup-groomer-space',
            '/verify-email',
        ];

        foreach ($blocked as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $fallback;
            }
        }

        return $target;
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<section class="container-fluid mb-5 login-signup-page">
    <div class="row g-0">
        <div class="col-lg-6 p-0">
            <div class="login-image-form h-100">
                <div class="login-image-wrapper h-100">
                    <img src="{{ asset('images/login-page.png') }}" alt="Login Image"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="login-form">
                            <h1 class="heading">Log in to Fursgo</h1>

                <!-- Session Status -->
                <x-auth-session-status :status="session('status')" />

                <form wire:submit="login" class="mt-5"
                    x-data="{
                        email: @entangle('email').live,
                        password: @entangle('password').live,
                        emailExists: @entangle('emailExists').live,
                        emailCheckStatus: @entangle('emailCheckStatus').live,
                        emailErrorMessage: @entangle('emailErrorMessage').live,
                        loginFailed: @entangle('loginFailed').live,
                        emailTimer: null,
                        emailValid() {
                            const value = (this.email || '').trim();
                            return value !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                        },
                        onEmailInput() {
                            clearTimeout(this.emailTimer);
                            this.loginFailed = false;
                            this.emailErrorMessage = '';
                            const value = (this.email || '').trim().toLowerCase();
                            if (value !== this.email) {
                                this.email = value;
                            }
                            if (value === '') {
                                this.emailCheckStatus = 'idle';
                                this.emailExists = null;
                                return;
                            }
                            // Spinner while typing; resolve to tick/cross after typing stops
                            this.emailCheckStatus = 'checking';
                            this.emailTimer = setTimeout(() => {
                                if (!this.emailValid()) {
                                    this.emailCheckStatus = 'invalid';
                                    this.emailExists = false;
                                    return;
                                }
                                $wire.checkEmail();
                            }, 400);
                        },
                    }">
                    <div class="form-inner d-flex flex-column align-items-center justify-content-center">
                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" x-model="email" x-on:input="onEmailInput()"
                                placeholder="email@example.com" required autofocus autocomplete="email">
                            <span class="email-field-status" x-show="emailCheckStatus === 'checking'" x-cloak
                                aria-label="Validating email">
                                <span class="email-field-spinner" aria-hidden="true"></span>
                            </span>
                            <span class="email-field-status" x-show="emailCheckStatus === 'invalid'" x-cloak
                                aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                        fill="#FF6E6E" />
                                </svg>
                            </span>
                            <span class="email-field-status" x-show="emailCheckStatus === 'valid'" x-cloak
                                aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </div>
                        <span class="text-danger" style="font-size: 14px;" x-show="emailErrorMessage"
                            x-text="emailErrorMessage" x-cloak></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" x-model="password" x-on:input="loginFailed = false"
                                placeholder="••••••••••••••••••••" required autocomplete="current-password">
                            <span class="email-field-status" x-show="loginFailed" x-cloak aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                        fill="#FF6E6E" />
                                </svg>
                            </span>
                            <span class="email-field-status" x-show="!loginFailed && password.trim().length > 0" x-cloak
                                aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </div>
                        @error('password')
                            <span class="text-danger" style="font-size: 14px;">{{ $message }}</span>
                        @enderror
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">Forgot Password</a>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn-custom btn-active-bg btn-custom-hover btn-shadow login-width text-center">
                        Log in
                    </button>
                    </div>
                </form>

                <div class="divider">— Or Sign in with —</div>

                <div class="signup-text">
                    Not signed up yet?
                    <a href="{{ route('login-signup.signup') }}">Sign up now</a>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
@endpush