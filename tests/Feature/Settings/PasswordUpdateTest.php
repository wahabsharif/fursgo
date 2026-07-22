<?php

use App\Models\AccountSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('password settings page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/account-settings?tab=login_and_security')->assertOk();
});

test('authenticated user can update their password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword1!'),
    ]);

    $this->actingAs($user);

    Volt::test('account.settings')
        ->set('current_password', 'OldPassword1!')
        ->set('password', 'NewPassword1!')
        ->set('password_confirmation', 'NewPassword1!')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check('NewPassword1!', $user->fresh()->password))->toBeTrue();
    $this->assertGuest();

    $settings = AccountSetting::forOwner($user);
    expect($settings->password_updated_at)->not->toBeNull();
});

test('password update fails with incorrect current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword1!'),
    ]);

    $this->actingAs($user);

    Volt::test('account.settings')
        ->set('current_password', 'WrongPassword1!')
        ->set('password', 'NewPassword1!')
        ->set('password_confirmation', 'NewPassword1!')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);
});
