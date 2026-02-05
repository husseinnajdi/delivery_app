<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notifications;
use JsonException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\MulticastMessage;
use App\Models\notification_users;
class NotificationController extends Controller
{

    public function index()
    {
        return notifications::all();
    }
    public function showbyuser(Request $request)
    {
        $id = $request->auth_user->id;
    
        $notificationIds = notification_users::where('user_id', $id)
            ->pluck('notification_id');
        $notifications = notifications::whereIn('id', $notificationIds)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return $notifications;
    }
    
    public function sendnotification(Request $request)
    {
        try {
            $this->notificationsend($request->user_id, $request->title, $request->body, );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send notification', 'error' => $e->getMessage()], 500);
        }
        return response()->json([
            'message' => 'Notification sent successfully',
        ], 200);
    }
    public function notifyalluser(Request $request){
        $userIds = User::pluck('id')->toArray();
        try {
            $this->notificationsend($userIds, $request->title, $request->body);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send notification', 'error' => $e->getMessage()], 500);
        }
        return response()->json([
            'message' => 'Notification sent successfully to all users',
        ], 200);
    }

    public function markasread(Request $request)
    {
        $id = $request->id;
        $notificationUser = notification_users::where('notification_id', $id)
            ->where('user_id', $request->auth_user->id)
            ->first();
    
        if ($notificationUser) {
            $notificationUser->is_read = true;
            $notificationUser->save();
    
            return response()->json(['message' => 'Notification marked as read'], 200);
        } else {
            return response()->json(['message' => 'Notification not found for user'], 404);
        }
    }
    public function notificationsend(array $ids, $title, $body) {
        // 1. Get path from Env (defaulting to the Render secret path)
        $credentialsPath = env('FIREBASE_CREDENTIALS', base_path('secret_key.json'));
    
        if (!file_exists($credentialsPath)) {
            return response()->json(['message' => 'Firebase credentials not found at ' . $credentialsPath], 500);
        }
        
        try {
            // 2. Initialize using the Factory
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Firebase initialization failed', 'error' => $e->getMessage()], 500);
        }
        
        $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();
        
        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }
        
        // 3. Optimized Sending: Use sendMulticast for better performance
        $notification = Notification::create($title, $body);
        $message = CloudMessage::new()->withNotification($notification);
        
        try {
            // sendMulticast is much faster than a foreach loop for multiple tokens
            $report = $messaging->sendMulticast($message, $tokens);
            
            // Log failures if any
            if ($report->hasFailures()) {
                \Log::warning('Some FCM notifications failed: ' . $report->failures()->count());
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'FCM sending failed', 'error' => $e->getMessage()], 500);
        }
    
        // 4. Save to Database (Consider using a Database Transaction here)
        try {
            $dbNotification = notifications::create([
                'title' => $title,
                'body' => $body,
                'type' => 'general',
                'created_at' => now(),
                'is_read' => false,
            ]);
            
            $userData = collect($ids)->map(fn($userId) => [
                'notification_id' => $dbNotification->id,
                'user_id' => $userId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();
    
            notification_users::insert($userData); // Bulk insert is better
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save to DB', 'error' => $e->getMessage()], 500);
        }
        
        return response()->json(['message' => 'Notifications processed'], 200);
    }
    
    
}
