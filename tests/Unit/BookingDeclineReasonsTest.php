<?php

use App\Support\BookingDeclineReasons;

it('returns the default reason when the value is empty or unknown', function () {
    expect(BookingDeclineReasons::normalize(null))->toBe('Changes in schedule')
        ->and(BookingDeclineReasons::normalize(''))->toBe('Changes in schedule')
        ->and(BookingDeclineReasons::normalize('not a valid reason'))->toBe('Changes in schedule');
});

it('accepts listed reasons including curly or straight apostrophes', function () {
    expect(BookingDeclineReasons::normalize('Other'))->toBe('Other')
        ->and(BookingDeclineReasons::normalize("Can't accommodate this request"))
        ->toBe('Can’t accommodate this request')
        ->and(BookingDeclineReasons::normalize('Can’t accommodate this request'))
        ->toBe('Can’t accommodate this request');
});
