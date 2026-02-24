<?php

namespace App\Services;
use App\Models\User;

use Firebase\JWT\JWT;
class AuthService{
    public function generatetoken(User $user){
        $payload = [
            'id' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (10 * 365 * 24 * 60 * 60),
        ];
        return JWT::encode($payload, config('jwt.key'), 'HS256');
    }
}