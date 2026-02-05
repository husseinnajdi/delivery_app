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
    public function notificationsend(array $ids, $title, $body) {
        // Get the JSON string from environment variable
        $firebaseCredentials = env('FIREBASE_CREDENTIALS');
        
        if (!$firebaseCredentials) {
            return response()->json(['message' => 'Firebase credentials not configured'], 500);
        }
        
        // Create a temporary file with the credentials
        $tempFile = tempnam(sys_get_temp_dir(), 'firebase_credentials');
        file_put_contents($tempFile, $firebaseCredentials);
        
        // Now use the temp file path
        $factory = (new Factory)->withServiceAccount($tempFile);
        $messaging = $factory->createMessaging();
        
        $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();
        
        if (empty($tokens)) {
            // Clean up temp file
            unlink($tempFile);
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }
        
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body));
        
        foreach ($tokens as $token) {
            try {
                $messaging->send($message->withTarget('token', $token));
            } catch (\Exception $e) {
                // Log the error but continue with other tokens
                \Log::error('Failed to send notification to token: ' . $token . ' - ' . $e->getMessage());
            }
        }
        
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
            // Clean up temp file
            unlink($tempFile);
            return response()->json(['message' => 'Failed to save notification', 'error' => $e->getMessage()], 500);
        }
        
        // Clean up the temporary file
        unlink($tempFile);
        
        return response()->json(['message' => 'Notifications sent successfully', 'count' => count($tokens)], 200);
    }
    
}
