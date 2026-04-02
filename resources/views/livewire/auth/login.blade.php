<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public bool $emailExists = false;

    /**
     * Handle an incoming authentication request.
     */
    public function checkEmail(): void
    {
        $this->loginFailed = false; // reset when user re-checks email

        if ($this->email && filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = \DB::table('users')->where('email', $this->email)->exists();
            if ($this->emailExists) {
                $this->resetValidation('email');
            } else {
                $this->addError('email', 'No account found with this email.');
            }
        } else {
            $this->emailExists = false;
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public bool $loginFailed = false;

    public function login(): void
    {
        $this->loginFailed = false; // reset on each attempt
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            $this->loginFailed = true;

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        request()->session()->regenerate();
        $this->redirectRoute('home', navigate: true);
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

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>


<section class="container-fluid mt-5 mb-5">
    <div class="row" style="gap: 4%;">
        <div class="col-lg-6">
            <div class="login-image-form">
                <div class="login-image-wrapper">
                    <img src="{{ asset('images/login-page.png') }}" alt="Login">
                </div>
            </div>
        </div>
        <div class="col-lg-3 d-flex align-items-center">
            <div class="login-form mt-5">
                <h1 class="heading">Log in to Fursgo</h1>

                <!-- Session Status -->
                <x-auth-session-status :status="session('status')" />

                <form wire:submit="login" class="d-flex flex-column align-items-center justify-content-center mt-5">
                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" wire:model="email" wire:blur="checkEmail"
                                placeholder="email@example.com" required autofocus autocomplete="email">
                            @if ($errors->has('email') && !$loginFailed)
                                <span class="icon error" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                            fill="#FF6E6E" />
                                    </svg>
                                </span>
                            @elseif ($email && $emailExists)
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                    viewBox="0 0 19 19" fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            @endif
                        </div>
                        @error('email')
                            <span class="text-danger" style="font-size: 14px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" wire:model="password"
                                placeholder="••••••••••••••••••••" required autocomplete="current-password">
                            @if ($errors->has('password') || $loginFailed)
                                <span class="icon error" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                            fill="#FF6E6E" />
                                    </svg>
                                </span>
                            @elseif ($password)
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                    viewBox="0 0 19 19" fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            @endif
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
                </form>

                <div class="divider">— Or Sign in with —</div>

                <div class="social-login">
                    <button class="social-btn" aria-label="Sign in with Facebook">
                        <svg viewBox="0 0 24 24" fill="#1877F2">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </button>

                    <button class="social-btn" aria-label="Sign in with Google">
                        <svg viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                    </button>

                    <button class="social-btn" aria-label="Sign in with LinkedIn">
                        <svg viewBox="0 0 24 24" fill="#0A66C2">
                            <path
                                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </button>

                    <button class="social-btn" aria-label="Sign in with X">
                        <svg viewBox="0 0 24 24" fill="#000000">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </button>

                    <button class="social-btn" aria-label="Sign in with Apple">
                        <svg viewBox="0 0 24 24" fill="#000000">
                            <path
                                d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" />
                        </svg>
                    </button>
                </div>

                <div class="signup-text">
                    Not signed up yet? <a href="{{ route('signup') }}" wire:navigate>Sign up</a>
                </div>
            </div>
        </div>
    </div>
</section>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
@endpush
