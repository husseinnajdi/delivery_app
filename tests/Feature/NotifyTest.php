<?php


use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
test('user can send notification', function (){
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
    ])->postJson('/api/sendnotification', [
        'user_id' => ["4"],
        'title' => 'Test Notification',
        'type'=> 'info',
        'body' => 'This is a test notification message.'
    ]);
    $response->assertStatus(200);
});