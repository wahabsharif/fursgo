<?php

use App\Models\FaqArticle;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketSubmitted;
use Database\Seeders\FaqSeeder;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

beforeEach(function () {
    /** @var TestCase $this */
    $this->seed(FaqSeeder::class);
});

test('help centre page shows seeded faqs', function () {
    $this
        ->get('/support-and-assistance/help-and-support')
        ->assertOk()
        ->assertSee('Help & Support Center')
        ->assertSee('How do I book a grooming appointment?')
        ->assertSee('Please provide as much detail as possible', false);
});

test('help centre search filters articles in realtime', function () {
    Volt::test('help.centre')
        ->set('query', 'reschedule')
        ->assertSee('Can I cancel or reschedule my booking?')
        ->assertDontSee('What is FursGo?');
});

test('help centre can switch faq topics', function () {
    Volt::test('help.centre')
        ->call('selectTopic', 'payments')
        ->assertSet('category', 'payments')
        ->assertSee('How is pricing determined?');
});

test('opening a search result switches back to the matching faq', function () {
    $article = FaqArticle::query()->where('question', 'What is FursGo?')->firstOrFail();

    Volt::test('help.centre')
        ->set('query', 'FursGo')
        ->call('openArticle', $article->id)
        ->assertSet('query', '')
        ->assertSet('category', 'account')
        ->assertSet('openFaqId', $article->id)
        ->assertSee('What is FursGo?');
});

test('guest can submit a support ticket with an email', function () {
    Notification::fake();

    Volt::test('help.centre')
        ->set('showTicketModal', true)
        ->set('guestEmail', 'guest@example.com')
        ->set('ticketSubject', 'Cannot change booking')
        ->set('ticketCategory', 'bookings')
        ->set('ticketDescription', 'I need to move my appointment to next week please.')
        ->call('submitRequest')
        ->assertHasNoErrors()
        ->assertSet('showTicketModal', false);

    $ticket = SupportTicket::query()->first();

    expect($ticket)
        ->not
        ->toBeNull()
        ->and($ticket->email)
        ->toBe('guest@example.com')
        ->and($ticket->subject)
        ->toBe('Cannot change booking')
        ->and($ticket->ticket_number)
        ->toStartWith('FG-');

    Notification::assertSentOnDemand(SupportTicketSubmitted::class);
});

test('help centre from business chrome shows business text next to the logo', function () {
    $this
        ->get('/support-and-assistance/help-and-support?chrome=business')
        ->assertOk()
        ->assertSee('logo-b-text', false)
        ->assertSee('>Business</span>', false)
        ->assertSee('Business Help & Support Centre')
        ->assertSee('Find answers, manage issues, or contact the FursGo team.')
        ->assertSee('How do I accept a booking request?')
        ->assertSee('When a new booking request arrives, you will receive a notification.')
        ->assertSee('Payments & Payouts')
        ->assertSee('When do I receive payouts?')
        ->assertSee('How do I update my business profile?')
        ->assertSee('Where do my service policies appear?')
        ->assertSee('Account & Profile')
        ->assertSee('Policies & Safety')
        ->assertDontSee('How do I book a grooming appointment?')
        ->assertDontSee('How do I change or cancel my booking?')
        ->assertSee('A short summary of your issue.')
        ->assertDontSee('Where should we send our reply?');
});

test('help centre from the pet-owner site does not show business logo text', function () {
    $this
        ->get('/support-and-assistance/help-and-support')
        ->assertOk()
        ->assertDontSee('logo-b-text', false);
});

test('navigating from a business page to help centre keeps the business logo text', function () {
    $this->get('/business-landing-page')->assertOk();

    $this
        ->get('/support-and-assistance/help-and-support')
        ->assertOk()
        ->assertSee('logo-b-text', false)
        ->assertSee('>Business</span>', false);
});

test('authenticated user can submit a support ticket', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('help.centre')
        ->set('showTicketModal', true)
        ->set('ticketSubject', 'Payment receipt missing')
        ->set('ticketCategory', 'payments')
        ->set('ticketDescription', 'I completed a booking but cannot find the receipt.')
        ->call('submitRequest')
        ->assertHasNoErrors();

    $ticket = SupportTicket::query()->first();

    expect($ticket->user_id)
        ->toBe($user->id)
        ->and($ticket->email)
        ->toBe($user->email)
        ->and($ticket->category)
        ->toBe('payments');
});
