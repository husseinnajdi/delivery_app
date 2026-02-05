<?php


use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
test('user can send notification', function () {

    $user = \App\Models\User::factory()->create([
        'password' => bcrypt('password123')
    ]);

    $loginresponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);

    $token = $loginresponse->headers->get('authorization');
    $taken=str_replace('Bearer ', '', $token);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->postJson('/api/sendnotification', [
        'user_id' => [$user->id],
        'title' => 'Test Notification',
        'type'=> 'info',
        'body' => 'This is a test notification message.'
    ]);

    expect($response->status())->toBe(200, $response->getContent());
});
