<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\UserResources;
use App\Services\ActivityLog;
class UserController extends Controller
{
    public function __construct(private ActivityLog $activityLog)
    {
    }
    public function index()
    {
        return response()->json(User::all());
    }
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string',
            'password' => 'required',
        ]);
        try {
            $user = User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'role_id' => $request->role_id ?? User::ROLE_USER,
                'role_subtype_id' => $request->role_subtype_id,
                'status' => $request->status ?? true,
                'image' => null,
            ]);
            $this->activityLog->log($user->id, 'create user', 'User created', '1');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create user', $e->getMessage()], 500);
        }
        dump($user);
        Log::info('User data:', $user->toArray());
        dd($request->all());
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
    public function update(Request $request)
    {
        $user = User::find($request->auth_user->id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $data = $request->only(['full_name', 'email', 'phone']);
        $image_data=base64_decode($request->picture);
        $user->update([
            'full_name' => $data['full_name'] ?? $user->full_name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'image'=>$image_data??$user->picture
        ]);
        
        $this->activityLog->log($user->id, 'update user', 'User updated his profile', '1');   
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
