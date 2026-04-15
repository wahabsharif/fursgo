<?php

use App\Models\User;

test('password settings page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/password')->assertOk();
});