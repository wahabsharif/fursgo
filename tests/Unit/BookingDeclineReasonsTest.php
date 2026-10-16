<?php

use App\Support\BookingDeclineReasons;

it('returns the default reason when the value is empty or unknown', function () {
    expect(BookingDeclineReasons::normalize(null))
        ->toBe('Changes in schedule')
        ->and(BookingDeclineReasons::normalize(''))
        ->toBe('Changes in schedule')
        ->and(BookingDeclineReasons::normalize('not a valid reason'))
        ->toBe('Changes in schedule');
});

it('accepts listed reasons including curly or straight apostrophes', function () {
    expect(BookingDeclineReasons::normalize('Other'))
        ->toBe('Other')
        ->and(BookingDeclineReasons::normalize('Customer did not show up'))
        ->toBe('Customer did not show up')
        ->and(BookingDeclineReasons::normalize("Can't accommodate this request"))
        ->toBe('Can’t accommodate this request')
        ->and(BookingDeclineReasons::normalize('Can’t accommodate this request'))
        ->toBe('Can’t accommodate this request');
});

it('exposes the figma reason options in order', function () {
    expect(BookingDeclineReasons::options())->toBe([
        'Changes in schedule',
        'Can’t accommodate this request',
        'Customer did not show up',
        'Other',
    ]);
});
