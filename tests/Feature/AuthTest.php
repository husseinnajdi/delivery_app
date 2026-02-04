<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login', function () {

    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password123')
    ]);

    $loginresponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);
    $token=$loginresponse->headers->get('authorization');
    $token = str_replace('Bearer ', '', $token);
    $response= $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/order');
    $response->assertStatus(200);
});
test('unauthenticated user cannot access orders', function () {

    $response = $this->getJson('/api/order');

    $response->assertStatus(401);
});
