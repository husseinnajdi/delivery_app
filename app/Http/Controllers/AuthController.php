<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Console\View\Components\Secret;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Models\activity_log;
use App\Services\ActivityLog;
class AuthController extends Controller
{
    protected $activityLog;

    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }
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
        $this->activityLog->log($user->id, 'login', 'User logged in', '1');
        Log::info('User logged in', ['user_id' => $user->id, 'email' => $user->email]);
        return response()->json([
            'success' => true,
            'full_name' => $user->full_name,
        ])->header('Authorization', 'Bearer ' . $token);
    }
    public function loginwithgoogle(Request $request)
    {
        $credentials = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);
        //$factory = (new Factory)->withServiceAccount($credentials);
        $factory = (new Factory)->withServiceAccount(base_path('secret_key.json'));

        $auth = $factory->createAuth();
        try {
            $verifiedIdToken = $auth->verifyIdToken($request->id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userRecord = $auth->getUser($uid);
            $email = $verifiedIdToken->claims()->get('email');
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            //$user = Auth::user();
            $payload = [
                'id' => $user->id,
                'role' => $user->role,
                'iat' => time(),
                'exp' => time() + (10 * 365 * 24 * 60 * 60),
            ];
            $token = JWT::encode($payload, config('jwt.key'), 'HS256');
            $user->update(['FCMtoken' => $request->FCM_token]);
            $this->activityLog->log($user->id, 'login', 'User logged in with Google', '1');
            Log::info('User logged in with Google', ['user_id' => $user->id, 'email' => $user->email]);
            return response()->json([
                'success' => true,
                'full_name' => $user->full_name,
            ])->header('Authorization', 'Bearer ' . $token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid ID token', 'meesage' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        User::update(['FCM_token' => null]);
        $this->activityLog->log(
            $request->user()->id,
            'logout',
            'User logged out',
            '1'
        );
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent to your email.'
            ]);
        } else {
            return response()->json([
                'message' => 'Unable to send reset link.'
            ], 500);
        }
        return response()->json(['message' => 'Password reset link sent to your email']);
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
