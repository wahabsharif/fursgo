<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Renderless;
use Livewire\Volt\Component;

new #[Layout('layouts.groomer-auth')]
    class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public bool $emailExists = false;

    #[Renderless]
    public function checkEmail(string $email = ''): bool
    {
        $email = Str::lower(trim($email !== '' ? $email : $this->email));
        $this->email = $email;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = false;

            return false;
        }

        $this->emailExists = GroomerSpacerProfile::query()->where('email', $email)->exists();

        return $this->emailExists;
    }

    public function register(string $name = '', string $email = '', string $password = ''): void
    {
        if ($name !== '') {
            $this->name = $name;
        }

        if ($email !== '') {
            $this->email = $email;
        }

        if ($password !== '') {
            $this->password = $password;
        }

        $this->email = Str::lower(trim($this->email));
        $this->name = trim($this->name);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:goormer_spacer_profiles,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[\d\W]/'],
        ]);

        $profile = GroomerSpacerProfile::create([
            'full_name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => null,
            'account_type' => null,
            'select_location_type' => null,
            'id_document_paths' => null,
            'business_details' => null,
            'payout_details' => null,
            'insurance_details' => null,
            'freelance_details' => null,
            'business_basics' => null,
            'groomer_business_profile' => null,
            'spacer_business_profile' => null,
            'legal_policy_agreements' => false,
            'information_accuracy_confirmed' => false,
            'profile_visit' => 0,
            'auto_accept_booking' => false,
        ]);

        event(new Registered($profile));

        Auth::guard('groomer_spacer')->login($profile);

        $this->redirectRoute('business-homepage-groomer-space-owner', navigate: true);
    }

    public function loginGroomerSpaceUrl(): string
    {
        return url('/login-groomer-space');
    }
};
?>

<section class="container signup-groomer-section">
    <div class="login-form login-form-container">
        <h1>Just a few details<br /> to get started.</h1>

        <form @submit.prevent="submit()" class="mt-4" x-data="{
            name: '',
            email: '',
            password: '',
            emailExists: false,
            showPassword: false,
            emailTimer: null,
            emailFormatValid() {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((this.email || '').trim());
            },
            get hasLength() {
                return (this.password || '').length >= 8;
            },
            get hasUpper() {
                return /[A-Z]/.test(this.password || '');
            },
            get hasNumSym() {
                return /[\d\W]/.test(this.password || '');
            },
            get passwordValid() {
                return this.hasLength && this.hasUpper && this.hasNumSym;
            },
            get canSubmit() {
                return (this.name || '').trim() !== '' && this.emailFormatValid() && !this.emailExists && this.passwordValid;
            },
            get emailValid() {
                return this.emailFormatValid() && !this.emailExists;
            },
            get emailInvalid() {
                return (this.email || '').trim() !== '' && !this.emailValid;
            },
            onEmailInput() {
                this.emailExists = false;
                clearTimeout(this.emailTimer);
                const value = (this.email || '').trim().toLowerCase();
                if (value !== this.email) {
                    this.email = value;
                }
                if (!this.emailFormatValid()) {
                    return;
                }
                this.emailTimer = setTimeout(async () => {
                    this.emailExists = await $wire.checkEmail(this.email);
                }, 400);
            },
            async submit() {
                if (!this.canSubmit) {
                    return;
                }
                await $wire.register(
                    (this.name || '').trim(),
                    (this.email || '').trim().toLowerCase(),
                    this.password
                );
            },
        }">

            <!-- Name -->
            <div class="form-field mt-4">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <input type="text" id="name" x-model="name" required autocomplete="name"
                        :class="{ success: (name || '').trim() !== '', error: {{ $errors->has('name') ? 'true' : 'false' }} }">
                    <span class="icon success" :class="{ show: (name || '').trim() !== '' }" @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-success.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                    <span class="icon error" :class="{ show: {{ $errors->has('name') ? 'true' : 'false' }} }"
                        @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-error.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                </div>
                @error('name')
                    <div class="error-text" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-field mt-4">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" x-model="email" x-on:input="onEmailInput()" required
                        autocomplete="email"
                        :class="{ success: emailValid, error: emailInvalid || {{ $errors->has('email') ? 'true' : 'false' }} }">
                    <span class="icon success" :class="{ show: emailValid }" @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-success.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                    <span class="icon error"
                        :class="{ show: emailInvalid || {{ $errors->has('email') ? 'true' : 'false' }} }"
                        @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-error.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                </div>
                @error('email')
                    <div class="error-text" style="display: block;">{{ $message }}</div>
                @enderror
                <div class="error-text" x-cloak x-show="emailExists" style="display: none;">This email is already
                    registered.</div>
                <div class="error-text" x-cloak
                    x-show="(email || '').trim() !== '' && !emailFormatValid() && !emailExists" style="display: none;">
                    Please enter valid email address</div>
            </div>

            <!-- Password -->
            <div class="form-field mt-4">
                <label>Create a Password</label>
                <div class="input-wrapper" style="position: relative;">
                    <input :type="showPassword ? 'text' : 'password'" id="password" x-model="password" required
                        autocomplete="new-password"
                        :class="{ success: passwordValid, warning: password && !passwordValid }">

                    {{-- Show / Hide Toggle --}}
                    <span class="password-toggle" @mousedown.prevent @click="showPassword = !showPassword" role="button"
                        tabindex="0" @keydown.enter.prevent="showPassword = !showPassword">
                        <svg x-cloak x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                        <svg x-cloak x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </span>

                    <span class="icon success icon--password" :class="{ show: passwordValid }" @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-success.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                    <span class="icon error icon--password"
                        :class="{ show: {{ $errors->has('password') ? 'true' : 'false' }} }" @mousedown.prevent>
                        <img src="{{ asset('images/signup/icon-error.svg') }}" width="19" height="19" alt=""
                            draggable="false">
                    </span>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <div class="password-rules">
                        <p>Password requirements</p>
                        <span :style="{ color: hasLength ? '#C9DDA0' : 'inherit' }">
                            • At least 8 characters
                        </span>
                        <span :style="{ color: hasUpper ? '#C9DDA0' : 'inherit' }">
                            • Includes a capital letter
                        </span>
                        <span :style="{ color: hasNumSym ? '#C9DDA0' : 'inherit' }">
                            • Includes a number or symbol
                        </span>
                    </div>
                    <div class="password-status" :style="{ color: password ? '#FFC97A' : '' }">
                        <template x-if="password">
                            <span x-text="passwordValid ? 'Good Password' : 'Weak Password'"></span>
                        </template>
                    </div>
                </div>

                @error('password')
                    <div class="error-text" style="display: block;">{{ $message }}</div>
                @enderror
            </div>

            <div class="submit-button d-flex justify-content-center mt-4">
                <button type="submit"
                    style="width: 105px;height: 48px;padding:0;color: #FFF;text-align: center;font-family: Lato;font-size: 16px;font-style: normal;font-weight: 600;line-height: normal;"
                    class="btn-custom btn-custom-hover btn-shadow login-width text-center" id="submitBtn"
                    :class="canSubmit ? 'btn-active-bg' : 'btn-disabled'" :disabled="!canSubmit"
                    wire:loading.attr="disabled" wire:target="register">Sign
                    Up</button>
            </div>
        </form>
        <div class="social-divider">— Or Sign up with —</div>

        <div class="social-login">
            <button class="social-btn" aria-label="Sign up with Facebook">
                <svg viewBox="0 0 24 24" fill="#1877F2">
                    <path
                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
            </button>

            <button class="social-btn" aria-label="Sign up with Google">
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

            <button class="social-btn" aria-label="Sign up with LinkedIn">
                <svg viewBox="0 0 24 24" fill="#0A66C2">
                    <path
                        d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
            </button>

            <button class="social-btn" aria-label="Sign up with X">
                <svg viewBox="0 0 24 24" fill="#000000">
                    <path
                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                </svg>
            </button>

            <button class="social-btn" aria-label="Sign up with Apple">
                <svg viewBox="0 0 24 24" fill="#000000">
                    <path
                        d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z" />
                </svg>
            </button>
        </div>
        <div class="login-cta-text">
            Already have a Fursgo business account?
            <a href="{{ $this->loginGroomerSpaceUrl() }}" wire:navigate>Log in now</a>
        </div>

    </div>
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .signup-groomer-section .login-form .input-wrapper {
            height: 48px;
            overflow: visible;
        }

        .signup-groomer-section .icon {
            display: none;
            top: 50%;
            right: 14px;
            width: 19px;
            height: 19px;
            transform: translateY(-50%);
            line-height: 0;
            overflow: visible;
            z-index: 2;
            pointer-events: auto;
            cursor: default;
            user-select: none;
            -webkit-user-select: none;
            align-items: center;
            justify-content: center;
        }

        .signup-groomer-section .icon.success,
        .signup-groomer-section .icon.error {
            display: none;
        }

        .signup-groomer-section .input-wrapper .icon.show {
            display: flex !important;
        }

        .signup-groomer-section .icon img {
            display: block;
            width: 19px;
            height: 19px;
            max-width: 19px;
            max-height: 19px;
            flex-shrink: 0;
            pointer-events: none;
            user-select: none;
            -webkit-user-select: none;
            -webkit-user-drag: none;
        }

        .signup-groomer-section .icon--password {
            right: 40px;
        }

        .signup-groomer-section .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            color: #9D9B98;
            z-index: 2;
            user-select: none;
            -webkit-user-select: none;
        }

        .signup-groomer-section input.success {
            border-color: #C9DDA0;
        }

        .signup-groomer-section input.error {
            border-color: #FF6E6E;
        }

        .signup-groomer-section input.warning {
            border-color: #FFC97A;
        }

        .login-form-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .signup-groomer-section {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .login-form>h1 {
            color: #3B3731;
            text-align: center;
            font-family: "Playfair Display";
            font-size: 50px;
            font-style: normal;
            font-weight: 700;
            line-height: 1.05;
            margin-bottom: 0.5rem;
        }

        .login-form form.mt-4 {
            margin-top: 1rem !important;
        }

        .login-form .form-field.mt-4 {
            margin-top: 1rem !important;
        }

        .login-form .submit-button.mt-4 {
            margin-top: 1rem !important;
        }

        .social-divider {
            margin: 14px 0 12px;
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .social-login {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .social-btn {
            width: 42px;
            height: 42px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
        }

        .social-btn svg {
            width: 20px;
            height: 20px;
        }

        .social-btn:hover {
            filter: brightness(0.98);
        }

        .social-btn:focus-visible {
            outline: 2px solid #FFC97A;
            outline-offset: 2px;
        }

        .btn-disabled {
            background: #ccc;
            cursor: not-allowed !important;
        }

        .btn-disabled:hover {
            background: #ccc !important;
        }

        .login-cta-text {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            padding-top: 1.25rem;
            border-top: 2px solid #D4D4D4;
            margin-top: 1.25rem;
            text-align: center;
        }

        .login-cta-text a {
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