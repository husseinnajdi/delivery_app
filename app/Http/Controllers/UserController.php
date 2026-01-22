<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        return response()->json(User::all());
    }

    public function store(Request $request) {
        $user=User::create([
            'username' => $request->username,
            'full_name' => $request->full_name, 
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, 
            'remember_token	'=> null,
            'created_at'=> now(),
            'updated_at'=> now(),
            'role' => request()->has('role') ? $request->role : User::ROLE_USER,              
            'status' => true,
            'picture' => null,                           
        ]);
        dump($user);
        Log::info('User data:', $user->toArray());
        if($user->role === 'shop') {
            Shop::create([
                'name' => $request->shop_name,
                'address' => $request->adress,
                'phone' => $request->shop_phone,
                'city' => $request->shop_city,
                'user_id' => $user->id,
            ]);
        }
        return response()->json(['message' => 'User created successfully'], 201);
    }
    public function update(Request $request, $id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->update($request->only(['username', 'full_name', 'email', 'phone', 'password', 'role', 'status']));
        return response()->json(['message' => 'User updated successfully']);
    }
    public function show($id) {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }
}
