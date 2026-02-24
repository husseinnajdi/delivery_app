<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\ActivityLog;
use Illuminate\Support\Facades\Hash;
use App\Services\AuthService;
use App\Services\UserService;
class AuthController extends Controller
{
    protected $activityLog;
    protected $authService;
    protected $userService;

    public function __construct(ActivityLog $activityLog, AuthService $authService, UserService $userService)
    {
        $this->activityLog = $activityLog;
        $this->authService = $authService;
        $this->userService = $userService;
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $token = $this->authService->generatetoken($user);
        $user->update(['FCMtoken' => $request->FCM_token]);
        $this->activityLog->log($user->id, 'login', 'User logged in', '1');
        return response()->json([
            'success' => true,
            'full_name' => $user->full_name,
        ])->header('Authorization', 'Bearer ' . $token);
    }
    public function loginwithgoogle(Request $request)
    {
        $credentials = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);
        $factory = (new Factory)->withServiceAccount(base_path('secret_key.json'));
        $auth = $factory->createAuth();
        try {
            $verifiedIdToken = $auth->verifyIdToken($request->id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $userRecord = $auth->getUser($uid);
            $email = $verifiedIdToken->claims()->get('email');
            $user = $this->userService->getuserbyemail($email);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $token = $this->authService->generatetoken($user);
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
        User::where('id', $request->user()->id)->update(['FCMtoken' => null]);
        $this->activityLog->log(
            $request->user()->id,
            'logout',
            'User logged out',
            '1'
        );
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function forgetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user =$this->userService->getuserbyemail($request->email);
    if (!$user) {
        return response()->json(['error' => 'Email not found'], 404);
    }

    $otp = rand(1000, 9999);
    $user->otp= $otp;
    $user->save();

    Mail::to($user->email)->send(new OTPMail($otp) );
    $this->activityLog->log($user->id, 'password_reset_request', 'User requested password reset', '1');
    return response()->json(['message' => 'OTP sent to your email'], 200);
}
public function resetPassword(Request $request)
{
    $user = $this->userService->getuserbyemail($request->email);

    if (!$user) {
        return response()->json(['error' => 'User not found.'], 404);
    }

    if ((string)$user->otp !== (string)$request->otp) {
        return response()->json(['error' => 'Invalid OTP.'], 400);
    }

    $user->password = bcrypt($request->password);
    $user->otp = null;
    $user->save();
    $this->activityLog->log($user->id, 'password_reset', 'User reset password', '1');
    return response()->json(['message' => 'Password successfully reset.']);
}

    public function refreshtoken(Request $request)
    {
        $user = $request->user();
        $token = $this->authService->generatetoken($user);
        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
            ]
        ]);
    }

}
