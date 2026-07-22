<?php

use App\Models\AccountSetting;
use App\Models\User;
use App\Support\AccountBlocks;
use Livewire\Volt\Volt;

test('account settings page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('account-settings'))->assertOk();
});

test('guests are redirected from account settings', function () {
    $this->get(route('account-settings'))->assertRedirect();
});

test('legacy account settings url redirects to account settings', function () {
    $this->actingAs(User::factory()->create());

    $this
        ->get('/account-and-setting/settings')
        ->assertRedirect(route('account-settings'));
});

test('account settings are created with london and gbp defaults', function () {
    $user = User::factory()->create();

    $settings = AccountSetting::forOwner($user);

    expect($settings->timezone)
        ->toBe('Europe/London')
        ->and($settings->currency)
        ->toBe('GBP')
        ->and($settings->language)
        ->toBe('en_GB')
        ->and($settings->owner_id)
        ->toBe($user->id);

    expect(AccountSetting::forOwner($user)->id)->toBe($settings->id);
});

test('general account settings can be updated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('account.settings')
        ->call('updateGeneralSetting', 'timezone', 'America/New_York')
        ->call('updateGeneralSetting', 'currency', 'USD')
        ->call('updateGeneralSetting', 'language', 'fr_FR')
        ->assertHasNoErrors();

    $settings = AccountSetting::forOwner($user)->fresh();

    expect($settings->timezone)
        ->toBe('America/New_York')
        ->and($settings->currency)
        ->toBe('USD')
        ->and($settings->language)
        ->toBe('fr_FR');
});

test('saved account settings are displayed on the page', function () {
    $user = User::factory()->create();

    AccountSetting::forOwner($user)->update([
        'timezone' => 'America/New_York',
        'currency' => 'USD',
        'language' => 'fr_FR',
    ]);

    $this
        ->actingAs($user)
        ->get(route('account-settings'))
        ->assertOk()
        ->assertSee('America/New_York', false)
        ->assertSee('$ - USD', false)
        ->assertSee('French (France)', false);

    Volt::test('account.settings')
        ->assertSet('settings.timezone', 'America/New_York')
        ->assertSet('settings.currency', 'USD')
        ->assertSet('settings.language', 'fr_FR');
});

test('boolean account settings can be updated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('account.settings')
        ->call('updateBooleanSetting', 'notify_promotions', true)
        ->assertHasNoErrors();

    expect(AccountSetting::forOwner($user)->fresh()->notify_promotions)->toBeTrue();
});

test('account data can be downloaded as pdf', function () {
    $user = User::factory()->create(['name' => 'Jane Doe']);
    $this->actingAs($user);

    $this
        ->get(route('account-settings.download-data'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertDownload('Fursgo - Jane Doe Account data.pdf');
});

test('blocked users are listed from the database', function () {
    $owner = User::factory()->create();
    $blocked = User::factory()->create(['name' => 'Chloe D.']);

    AccountBlocks::block($owner, $blocked);

    $this->actingAs($owner);

    Volt::test('account.settings')
        ->assertSee('Chloe D.')
        ->assertSee('Includes other accounts they may have or create.');
});

test('blocked users are listed for the logged in account only', function () {
    $owner = User::factory()->create();
    $otherOwner = User::factory()->create();
    $blocked = User::factory()->create(['name' => 'Chloe D.']);
    $otherBlocked = User::factory()->create(['name' => 'Someone Else']);

    AccountBlocks::block($owner, $blocked);
    AccountBlocks::block($otherOwner, $otherBlocked);

    $this->actingAs($owner);

    Volt::test('account.settings')
        ->assertSee('Chloe D.')
        ->assertDontSee('Someone Else');
});

test('blocked users can be unblocked from account settings', function () {
    $owner = User::factory()->create();
    $blocked = User::factory()->create(['name' => 'Sarah W.']);
    $block = AccountBlocks::block($owner, $blocked);

    $this->actingAs($owner);

    Volt::test('account.settings')
        ->call('unblockUser', $block->id)
        ->assertDontSee('Sarah W.');

    expect(AccountBlocks::listFor($owner))->toBeEmpty();
});
