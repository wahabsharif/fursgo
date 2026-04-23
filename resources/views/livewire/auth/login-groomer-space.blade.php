<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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

    public bool $loginFailed = false;

    public function checkEmail(): void
    {
        $this->loginFailed = false;

        $email = Str::lower(trim($this->email));
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = GroomerSpacerProfile::query()
                ->where('email', $email)
                ->exists();

            if ($this->emailExists) {
                $this->resetValidation('email');
            } else {
                $this->addError('email', 'No groomer spacer account found with this email.');
            }
        } else {
            $this->emailExists = false;
        }
    }

    public function login(): void
    {
        $this->loginFailed = false;
        $this->email = Str::lower(trim($this->email));
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (!Auth::guard('groomer_spacer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            $this->loginFailed = true;
            $this->dispatch('groomer-login-failed');

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        request()->session()->regenerate();

        $default = route('business-homepage-groomer-space-owner');
        $target = session()->pull('url.intended', $default);
        $this->redirect(is_string($target) && $target !== '' ? $target : $default, navigate: true);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $this->dispatch('groomer-login-failed');

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<section class="container">
    <div class="gs-login-form">
        <h1 class="heading">Log in to FursGo Business.</h1>

        <x-auth-session-status :status="session('status')" />

        <form wire:submit="login" class="gs-login-form-inner"
            x-data="{ email: @entangle('email').live, password: @entangle('password').live, submitting: false }"
            x-on:submit="submitting = true" x-on:groomer-login-failed.window="submitting = false">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" wire:model.live="email" x-model="email" wire:blur="checkEmail"
                        placeholder="email@example.com" required autofocus autocomplete="email">
                    @if ($errors->has('email') && !$loginFailed)
                        <span class="icon error" style="display: block !important;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                    fill="#FF6E6E" />
                            </svg>
                        </span>
                    @elseif ($email && $emailExists)
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                            fill="none">
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

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" wire:model.live="password" x-model="password"
                        placeholder="••••••••••••••••••••" required autocomplete="current-password">
                    @if ($errors->has('password') || $loginFailed)
                        <span class="icon error" style="display: block !important;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path
                                    d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                    fill="#FF6E6E" />
                            </svg>
                        </span>
                    @elseif ($password)
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                            fill="none">
                            <path
                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                fill="#C9DDA0" />
                        </svg>
                    @endif
                </div>
                @error('password')
                    <span class="text-danger" style="font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-custom"
                :class="{ 'btn-disabled': !(email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && password.trim().length > 0) }"
                :disabled="!(email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && password.trim().length > 0)"
                wire:loading.attr="disabled" wire:target="login">
                <span class="btn-label" x-show="!submitting">Log in</span>
                <span class="btn-loading" x-show="submitting">
                    <span class="btn-spinner" aria-hidden="true"></span>
                </span>
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
            Not signed up yet? <a href="{{ route('signup-groomer-space') }}" wire:navigate>Sign up now</a>
        </div>
    </div>
</section>
@push('styles')
    <style>
        .gs-login-form {
            margin: 7rem auto;
            width: 100%;
            max-width: 400px;
        }

        .gs-login-form .heading {
            margin: 0 0 28px;
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .gs-login-form-inner {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
        }

        .gs-login-form .form-group {
            width: 1010%;
        }

        .gs-login-form label {
            display: block;
            margin-bottom: 8px;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .gs-login-form .input-wrapper {
            position: relative;
        }

        .gs-login-form .input-wrapper input {
            width: 100%;
            height: 52px;
            border-radius: 10px;
            border: 1px solid #e2d2b4;
            background: #fff;
            padding: 12px 44px 12px 16px;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            color: #2f2a26;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .gs-login-form .input-wrapper input:focus {
            border-color: #d7a659;
        }

        .gs-login-form .icon.error,
        .gs-login-form .checkmark {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
        }

        .gs-login-form .text-danger {
            display: block;
            margin-top: 6px;
            color: #ff6e6e;
        }

        .gs-login-form .btn-custom {
            margin-top: 8px;
            width: 105px;
            height: 48px;
            padding: 0;
            border: 0;
            border-radius: 96px;
            background: #FFC97A;
            box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
            color: #fff;
            font-size: 20px;
            font-weight: 500;
            line-height: 1;
            cursor: pointer;
            transition: filter 0.2s ease;
        }

        .gs-login-form .btn-custom:hover {
            filter: brightness(0.95);
        }

        .gs-login-form .btn-custom.btn-disabled,
        .gs-login-form .btn-custom:disabled {
            background: #ccc;
            color: #fff;
            cursor: not-allowed;
            filter: none;
        }

        .gs-login-form .btn-loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .gs-login-form .btn-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            min-width: 16px;
            min-height: 16px;
            box-sizing: border-box;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #fff;
            border-radius: 50%;
            animation: gs-btn-spin 0.7s linear infinite;
        }

        @keyframes gs-btn-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .gs-login-form .divider {
            margin: 26px 0 18px;
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .gs-login-form .social-login {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .gs-login-form .social-btn {
            width: 42px;
            height: 42px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .gs-login-form .social-btn svg {
            width: 20px;
            height: 20px;
        }

        .gs-login-form .signup-text {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 2px solid #D4D4D4;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .gs-login-form .signup-text a {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
        }
    </style>
@endpush