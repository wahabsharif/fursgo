<?php

use App\Models\AccountLoginSession;
use App\Models\AccountSetting;
use App\Models\GroomerSpacerProfile;
use App\Models\User;
use App\Support\AccountBlocks;
use App\Support\AccountLanguages;
use App\Support\AccountLoginSessions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.dashboard')] class extends Component {
    public array $settings = [];

    public array $activeSessions = [];

    public array $blockedUsers = [];

    public string $passwordUpdatedLabel = 'Never updated.';

    public bool $showPasswordModal = false;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    private const BOOLEAN_SETTINGS = ['push_notifications', 'two_factor_enabled', 'notify_booking_updates', 'notify_groomer_messages', 'notify_space_owner_messages', 'notify_promotions', 'notify_reminder_alerts', 'profile_visibility', 'data_sharing_consent', 'email_marketing', 'sms_notifications', 'partner_offers', 'analytics_tracking'];

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function openPasswordModal(): void
    {
        $this->resetErrorBag();
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->showPasswordModal = true;
    }

    public function closePasswordModal(): void
    {
        $this->showPasswordModal = false;
        $this->reset('current_password', 'password', 'password_confirmation');
        $this->resetErrorBag();
    }

    private function loadSettings(): void
    {
        $owner = $this->owner();
        $accountSetting = AccountSetting::forOwner($owner);

        $this->settings = $accountSetting->only(['language', 'timezone', 'currency', 'theme', ...self::BOOLEAN_SETTINGS]);
        $this->passwordUpdatedLabel = $this->formatPasswordUpdatedLabel($accountSetting->password_updated_at);
        $this->activeSessions = AccountLoginSessions::listFor($owner);
        $this->blockedUsers = AccountBlocks::listFor($owner);
    }

    public function revokeLoginSession(int $loginSessionId): void
    {
        $owner = $this->owner();
        $wasCurrent = AccountLoginSessions::revoke($owner, $loginSessionId);

        if ($wasCurrent) {
            Auth::guard('web')->logout();
            Auth::guard('groomer_spacer')->logout();
            Session::invalidate();
            Session::regenerateToken();
            $this->redirect(route('home'), navigate: true);

            return;
        }

        $this->activeSessions = AccountLoginSessions::listFor($owner);
    }

    public function unblockUser(int $blockId): void
    {
        AccountBlocks::unblock($this->owner(), $blockId);
        $this->blockedUsers = AccountBlocks::listFor($this->owner());
    }

    public function updateGeneralSetting(string $key, string $value): void
    {
        $rules = [
            'language' => ['required', Rule::in(AccountLanguages::codes())],
            'timezone' => ['required', Rule::in(\DateTimeZone::listIdentifiers())],
            'currency' => ['required', 'alpha', 'size:3'],
            'theme' => ['required', Rule::in(['light', 'dark'])],
        ];

        if (!isset($rules[$key])) {
            throw ValidationException::withMessages([$key => 'Unknown account setting.']);
        }

        $validated = validator([$key => $value], [$key => $rules[$key]])->validate();
        $value = $key === 'currency' ? strtoupper($validated[$key]) : $validated[$key];

        AccountSetting::forOwner($this->owner())->update([$key => $value]);
        $this->settings[$key] = $value;
    }

    public function updateBooleanSetting(string $key, bool $value): void
    {
        if (!in_array($key, self::BOOLEAN_SETTINGS, true)) {
            throw ValidationException::withMessages([$key => 'Unknown account setting.']);
        }

        AccountSetting::forOwner($this->owner())->update([$key => $value]);
        $this->settings[$key] = $value;
    }

    public function updatePassword(): void
    {
        $this->validate(
            [
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[\d\W]/', 'confirmed'],
            ],
            [
                'password.regex' => 'Password must include an uppercase letter and a number or symbol.',
                'password.confirmed' => 'Passwords do not match.',
            ],
        );

        $owner = $this->owner();

        if (!Hash::check($this->current_password, (string) $owner->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        if ($owner instanceof User) {
            $owner
                ->forceFill([
                    'password' => $this->password,
                ])
                ->save();
        } else {
            $owner
                ->forceFill([
                    'password' => Hash::make($this->password),
                ])
                ->save();
        }

        $accountSetting = AccountSetting::forOwner($owner);
        $accountSetting
            ->forceFill([
                'password_updated_at' => now(),
            ])
            ->save();

        Auth::guard('web')->logout();
        Auth::guard('groomer_spacer')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function deletePersonalData()
    {
        $owner = $this->owner();

        if ($owner instanceof User) {
            $owner
                ->forceFill([
                    'name' => 'Deleted User',
                    'email' => 'deleted+' . $owner->getKey() . '+' . now()->timestamp . '@example.invalid',
                    'password' => Hash::make(str()->random(64)),
                    'profile_image' => null,
                    'user_status' => 'deleted',
                ])
                ->save();
        }

        if ($owner instanceof GroomerSpacerProfile) {
            $owner
                ->forceFill([
                    'full_name' => 'Deleted User',
                    'email' => 'deleted+' . $owner->getKey() . '+' . now()->timestamp . '@example.invalid',
                    'password' => Hash::make(str()->random(64)),
                    'user_status' => 'deleted',
                    'address' => null,
                    'phone' => null,
                    'payout_details' => null,
                    'business_details' => null,
                    'insurance_details' => null,
                    'freelance_details' => null,
                ])
                ->save();
        }

        AccountSetting::query()->where('owner_type', $owner->getMorphClass())->where('owner_id', $owner->getKey())->delete();
        AccountLoginSession::query()->where('owner_type', $owner->getMorphClass())->where('owner_id', $owner->getKey())->delete();
        AccountBlocks::clearFor($owner);

        Auth::guard('web')->logout();
        Auth::guard('groomer_spacer')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->redirect(route('home'), navigate: true);
    }

    private function formatPasswordUpdatedLabel(mixed $passwordUpdatedAt): string
    {
        if ($passwordUpdatedAt === null) {
            return 'Never updated.';
        }

        return 'Last updated ' . $passwordUpdatedAt->diffForHumans() . '.';
    }

    private function owner(): Model
    {
        $owner = Auth::guard('groomer_spacer')->user() ?? Auth::guard('web')->user();

        abort_unless($owner instanceof Model, 401);

        return $owner;
    }
}; ?>

<div wire:key="account-settings-root">
    <x-account.account-settings :settings="$settings" :password-updated-label="$passwordUpdatedLabel" :show-password-modal="$showPasswordModal" :active-sessions="$activeSessions"
        :blocked-users="$blockedUsers" />
</div>
