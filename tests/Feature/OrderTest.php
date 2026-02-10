<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class);
test('get orders api', function () {
    $user=User::factory()->create([
        'password' => bcrypt('password123')
    ]);
    $loginresponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);
    $token = $loginresponse->headers->get('authorization');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/order/allbydriver');
    $response->assertStatus(200);
});

test('get driver archive',function(){
    $user=User::factory()->create([
        'role' => 'driver',
        'password' => bcrypt('password123')
    ]);
    $loginresponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);
    $token = $loginresponse->headers->get('authorization');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson('/api/order/driverarchive');
    $response->assertStatus(200);
});
