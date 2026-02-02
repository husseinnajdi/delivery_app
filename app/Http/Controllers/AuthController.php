<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $payload = [
            'id' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (10 * 365 * 24 * 60 * 60),
        ];

        $token = JWT::encode($payload, config('jwt.key'), 'HS256');
        $user->update(['FCMtoken' => $request->FCM_token]);
        return response()->json([
            'success' => true,
        ])->header('Authorization', 'Bearer ' . $token);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        User::update(['FCM_token' => null]);
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refreshtoken(Request $request)
    {
        $user = $request->user();
        $payload = [
            'id' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (10 * 365 * 24 * 60 * 60),
        ];

        $token = JWT::encode($payload, config('jwt.key'), 'HS256');
        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
            ]
        ]);
    }

}
