<?php

namespace App\Http\Controllers;

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
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['FCMtoken' => $request->FCM_token]);
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        User::update(['FCM_token' => null]);
        return response()->json(['message' => 'Successfully logged out']);
    }
    
}
