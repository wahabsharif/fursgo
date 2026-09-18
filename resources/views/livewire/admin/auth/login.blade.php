<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin-guest')]
    class extends Component {
    public string $email = '';

    public string $password = '';

    public bool $loginFailed = false;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user && $user->isAdmin() && $user->isActive()) {
            $this->redirect(route('admin.overview'), navigate: false);
        }
    }

    public function login(): void
    {
        $this->loginFailed = false;

        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter valid email address',
            'email.email' => 'Please enter valid email address',
        ]);

        $email = strtolower(trim($this->email));
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->rejectLogin();
        }

        if (!$user->isAdmin() || !$user->isActive()) {
            $this->rejectLogin();
        }

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirect(route('admin.overview'), navigate: false);
    }

    protected function rejectLogin(): void
    {
        $this->loginFailed = true;

        throw ValidationException::withMessages([
            'email' => 'Invalid credentials',
        ]);
    }
}; ?>

<div class="admin-login-page">
    <header class="admin-login-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <a href="{{ url('/admin/login') }}" class="admin-login-brand" aria-label="FursGo Admin">
                        <img src="{{ asset('images/header/logo-fursgo.svg') }}" alt="fursgo" class="admin-login-logo"
                            width="98" height="27">
                        <span class="admin-pill">Admin</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-login-main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 d-flex justify-content-center align-items-center">
                    <div class="admin-login-panel">
                        <div class="admin-login-form-wrap">
                            <h1 class="heading">Log in to <br> FursGo Admin.</h1>

                        <form wire:submit="login" class="admin-login-form d-flex flex-column align-items-stretch"
                            x-data="{
                                email: @entangle('email').live,
                                password: @entangle('password').live,
                                loginFailed: @entangle('loginFailed'),
                                emailTouched: false,
                                emailFocused: false,
                                emailValid() {
                                    const value = (this.email || '').trim();
                                    return value !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                                },
                                emailInvalid() {
                                    return this.emailTouched && !this.emailValid();
                                },
                                emailHasError() {
                                    return this.loginFailed || this.emailInvalid() || {{ $errors->has('email') ? 'true' : 'false' }};
                                },
                                emailErrorMessage() {
                                    if (this.loginFailed) {
                                        return 'Invalid credentials';
                                    }
                                    return 'Please enter valid email address';
                                },
                                passwordReady() {
                                    return (this.password || '').trim().length > 0;
                                },
                                clearLoginFailed() {
                                    if (this.loginFailed) {
                                        this.loginFailed = false;
                                    }
                                },
                            }">
                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}"
                                x-bind:class="{ 'has-error': emailHasError() }">
                                <label for="admin-email">Email Address</label>
                                <div class="input-wrapper">
                                    <input type="email" id="admin-email" x-model="email"
                                        x-on:focus="emailFocused = true; clearLoginFailed()"
                                        x-on:input="clearLoginFailed()"
                                        x-on:blur="emailTouched = true; emailFocused = false"
                                        x-bind:class="{ 'is-invalid': emailHasError() }"
                                        x-bind:placeholder="emailHasError() && !emailFocused ? '' : 'email@example.com'"
                                        required autofocus autocomplete="email">

                                    <span class="field-error-inside"
                                        x-show="emailHasError() && !emailFocused"
                                        x-text="emailErrorMessage()"
                                        x-cloak>
                                    </span>

                                    <span class="field-status"
                                        x-show="emailHasError()"
                                        x-cloak aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                            viewBox="0 0 19 19" fill="none">
                                            <path
                                                d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                                fill="#FF6E6E" />
                                        </svg>
                                    </span>

                                    <span class="field-status"
                                        x-show="emailValid() && !emailHasError()"
                                        x-cloak aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                            viewBox="0 0 19 19" fill="none">
                                            <path
                                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                fill="#C9DDA0" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="admin-password">Password</label>
                                <div class="input-wrapper">
                                    <input type="password" id="admin-password" x-model="password"
                                        x-on:input="clearLoginFailed()"
                                        placeholder="••••••••••••••••••••" required autocomplete="current-password">

                                    <span class="field-status" x-show="passwordReady()" x-cloak aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                            viewBox="0 0 19 19" fill="none">
                                            <path
                                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                fill="#C9DDA0" />
                                        </svg>
                                    </span>
                                </div>
                                @error('password')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="admin-login-submit" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="login">Log in</span>
                                <span wire:loading wire:target="login">Logging in...</span>
                            </button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
@endpush
