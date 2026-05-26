<?php

use App\Models\User;

test('authenticated users visiting home are redirected to the shop', function () {
    $user = User::factory()->make();

    $response = $this->actingAs($user)->get('/home');

    $response->assertRedirect(route('shop.index'));
});
