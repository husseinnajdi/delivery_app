<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\UserResources;
class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }
    public function store(Request $request)
    {
        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'role' => $request->role ?? User::ROLE_USER,
            'status' => $request->status ?? true,
            'picture' => null,
        ]);
    
        if ($user->role === 'shop') {
            Shop::create([
                'name' => $request->shop_name,
                'address' => $request->adress,
                'phone' => $request->shop_phone,
                'city' => $request->shop_city,
                'user_id' => $user->id,
            ]);
        }
        dump($user);
        Log::info('User data:', $user->toArray());
        dd($request->all());
    
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
    
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->update($request->only(['username', 'full_name', 'email', 'phone', 'password', 'role', 'status', 'picture']));
        return response()->json(['message' => 'User updated successfully']);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Reset link sent to your email'
            ]);
        }
        return response()->json([
            'error' => 'Email not found'
        ], 404);
    }



    public function show(Request $request)
    {
        $user = User::find($request->auth_user->id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return new UserResources($user);
    }
}
