<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public bool $terms = false;
    public bool $newsletter = false;
    public bool $emailExists = false;

    /**
     * Check if email already exists in database
     */
    public function checkEmail(): void
    {
        if ($this->email && filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->emailExists = \DB::table('users')->where('email', $this->email)->exists();
        } else {
            $this->emailExists = false;
        }
    }

    /**
     * Check if form is valid for enabling submit button
     */
    public function isFormValid(): bool
    {
        $emailValid = $this->email && preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $this->email) && !$this->emailExists;
        $passwordValid = $this->password &&
                         strlen($this->password) >= 8 &&
                         preg_match('/[A-Z]/', $this->password) &&
                         preg_match('/[\d\W]/', $this->password);

        return $this->name &&
               $emailValid &&
               $passwordValid &&
               $this->terms;
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[\d\W]/'],
            'terms' => ['accepted'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['user_type'] = 'customer';

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        redirect()->route('home')->navigate();
    }
};
?>


<section class="container-fluid mt-5 mb-5">
    <div class="row" style="gap: 4%;">
        <div class="col-lg-6">
            <div class="login-image-form">
                <div class="login-image-wrapper">
                    <img src="{{ asset('images/login-page.png') }}" alt="Sign Up">
                </div>
            </div>
        </div>
        <div class="col-lg-3 d-flex align-items-center">
            <div class="login-form">
                <h1 class="heading">Sign Up to FursGo</h1>

                <form wire:submit="register" class="mt-4">
                    <!-- Name -->
                    <div class="form-field mt-4">
                        <label>Full Name</label>
                        <div class="input-wrapper">
                            <input type="text" id="name" wire:model.live="name" required>
                            <span class="icon success"
                                style="display: {{ $name && !$errors->has('name') ? 'block' : 'none' }} !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                    viewBox="0 0 19 19" fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                            <span class="icon error"
                                style="display: {{ $errors->has('name') ? 'block' : 'none' }} !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                    viewBox="0 0 19 19" fill="none">
                                    <path
                                        d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                        fill="#FF6E6E" />
                                </svg>
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
                            <input type="email" id="email" wire:model.live="email" wire:blur="checkEmail" required>
                            @if ($email && !$errors->has('email') && !$emailExists)
                                <span class="icon success" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg>
                                </span>
                            @endif
                            @if ($errors->has('email') || $emailExists)
                                <span class="icon error" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                            fill="#FF6E6E" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                        @error('email')
                            <div class="error-text" style="display: block;">{{ $message }}</div>
                        @enderror
                        @if ($emailExists)
                            <div class="error-text" style="display: block;">This email is already registered.</div>
                        @endif
                    </div>

                    <!-- Password -->
                    <div class="form-field mt-4">
                        <label>Create a Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" wire:model.live="password" required>
                            @php
                                $passwordValid =
                                    $password &&
                                    strlen($password) >= 8 &&
                                    preg_match('/[A-Z]/', $password) &&
                                    preg_match('/[\d\W]/', $password);
                            @endphp
                            @if ($passwordValid && !$errors->has('password'))
                                <span class="icon success" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg>
                                </span>
                            @endif
                            @if ($errors->has('password'))
                                <span class="icon error" style="display: block !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C14.7467 0 19 4.25329 19 9.5C19 14.7467 14.7467 19 9.5 19C4.25329 19 0 14.7467 0 9.5C0 4.25329 4.25329 0 9.5 0ZM13.1973 6.22559C12.9044 5.9327 12.4296 5.9327 12.1367 6.22559L9.71094 8.65039L7.28613 6.22559C6.99324 5.93269 6.51848 5.93269 6.22559 6.22559C5.93294 6.5185 5.93277 6.99332 6.22559 7.28613L8.65039 9.71094L6.22559 12.1367C5.93295 12.4296 5.93278 12.9045 6.22559 13.1973C6.51841 13.4898 6.9933 13.4898 7.28613 13.1973L9.71094 10.7715L12.1367 13.1973C12.4296 13.4898 12.9044 13.4898 13.1973 13.1973C13.4901 12.9045 13.4899 12.4296 13.1973 12.1367L10.7715 9.71094L13.1973 7.28613C13.4901 6.99332 13.4899 6.5185 13.1973 6.22559Z"
                                            fill="#FF6E6E" />
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <div class="password-rules">
                                <p>Password requirements</p>
                                <span>• At least 8 characters</span>
                                <span>• Includes a capital letter</span>
                                <span>• Includes a number or symbol</span>
                            </div>

                            <div class="password-status" id="passwordStatus"
                                style="color: {{ $passwordValid ? '#C9DDA0' : ($password ? '#FFC97A' : '') }};">
                                @if ($password)
                                    @if ($passwordValid)
                                        Good Password
                                    @else
                                        Weak Password
                                    @endif
                                @endif
                            </div>
                        </div>
                        @error('password')
                            <div class="error-text" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Checkboxes -->
                    <div class="checkbox-group custom-check-group">
                        <label class="custom-check">
                            <input type="checkbox" id="terms" wire:model.live="terms" required>
                            <span class="checkmark"></span>
                            <span class="check-text">
                                I agree to the Terms of Service and Privacy Policy.
                            </span>
                        </label>
                        @error('terms')
                            <div class="error-text" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="checkbox-group custom-check-group">
                        <label class="custom-check">
                            <input type="checkbox" id="newsletter" wire:model="newsletter">
                            <span class="checkmark"></span>
                            <span class="check-text">
                                Sign up to receive updates, promotions, & personalised offers <br>
                                from FursGo.
                                <span style="color:#9D9B98">
                                    (You can unsubscribe at any time.)
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="submit-button d-flex justify-content-center mt-4">
                        <button type="submit"
                            class="btn-custom btn-active-bg btn-custom-hover btn-shadow login-width text-center"
                            id="submitBtn"
                            wire:loading.attr="disabled"
                            @if($this->isFormValid()) @else disabled @endif>Sign Up</button>
                    </div>
                </form>
                <div class="signup-divider"></div>
                <div class="footer-text"> Already have a FursGo account? <a href="{{ route('login') }}"
                        wire:navigate>Log in now</a> </div>

            </div>
        </div>
    </div>
</section>


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login_signup.css') }}">
@endpush
