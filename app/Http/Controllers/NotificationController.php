<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notifications;
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
    public function notificationsend(array $ids, $title, $body)
    {
        // 1️⃣ Read the JSON string from environment
        $firebaseJson = env('FIREBASE_CREDENTIALS');
    
        if (!$firebaseJson) {
            return response()->json(['message' => 'Firebase credentials not set in env'], 500);
        }
    
        // 2️⃣ Save to a temporary file
        $tempPath = storage_path('app/firebase_key.json');
        file_put_contents($tempPath, $firebaseJson);
    
        // 3️⃣ Initialize Firebase using the temp file
        $factory = (new Factory)->withServiceAccount($tempPath);
        $messaging = $factory->createMessaging();
    
        // 4️⃣ Get FCM tokens
        $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();
    
        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }
    
        // 5️⃣ Create the notification message
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body));
    
        // 6️⃣ Send to each token
        foreach ($tokens as $token) {
            try {
                $messaging->send($message->withTarget('token', $token));
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // Log the error but continue sending
                info("Failed to send to token $token: ".$e->getMessage());
            }
        }
    
        // 7️⃣ Save notifications in DB
        try {
            $notification = notifications::create([
                'title' => $title,
                'body' => $body,
                'type' => 'general',
                'created_at' => now(),
                'is_read' => false,
            ]);
    
            foreach ($ids as $userId) {
                notification_users::create([
                    'notification_id' => $notification->id,
                    'user_id' => $userId,
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save notification',
                'error' => $e->getMessage()
            ], 500);
        }
    
        return response()->json([
            'message' => 'Notifications sent successfully',
            'count' => count($tokens)
        ], 200);
    }
    
}
