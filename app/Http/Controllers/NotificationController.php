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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

        // ... index, showbyuser, markasread stay the same ...
    
        public function sendnotification(Request $request)
        {
            // Wrap user_id in an array if it's a single ID
            $userIds = is_array($request->user_id) ? $request->user_id : [$request->user_id];
            
            $result = $this->notificationsend($userIds, $request->title, $request->body);
    
            if ($result['success']) {
                return response()->json(['message' => $result['message']], 200);
            }
            return response()->json(['message' => $result['message'], 'error' => $result['error'] ?? ''], 500);
        }
    
        public function notifyalluser(Request $request) {
            $userIds = User::pluck('id')->toArray();
            $result = $this->notificationsend($userIds, $request->title, $request->body);
    
            if ($result['success']) {
                return response()->json(['message' => 'Sent to all users'], 200);
            }
            return response()->json(['message' => 'Global send failed', 'error' => $result['error'] ?? ''], 500);
        }
    
        public function notificationsend(array $ids, $title, $body) {
            // Render standard path for secrets
            $credentialsPath = env('FIREBASE_CREDENTIALS', base_path('secret_key.json'));
    
            if (!file_exists($credentialsPath)) {
                return ['success' => false, 'message' => 'Credentials missing at ' . $credentialsPath];
            }
    
            try {
                $factory = (new Factory)->withServiceAccount($credentialsPath);
                $messaging = $factory->createMessaging();
                
                $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();
    
                if (empty($tokens)) {
                    return ['success' => false, 'message' => 'No FCM tokens found'];
                }
    
                // Send via Firebase
                $notification = Notification::create($title, $body);
                $message = CloudMessage::new()->withNotification($notification);
                $report = $messaging->sendMulticast($message, $tokens);
    
                // Database Transaction: Ensure DB is only updated if Firebase call didn't crash
                DB::transaction(function () use ($ids, $title, $body) {
                    $dbNotification = notifications::create([
                        'title' => $title,
                        'body' => $body,
                        'type' => 'general',
                        'is_read' => false,
                    ]);
    
                    $userData = collect($ids)->map(fn($userId) => [
                        'notification_id' => $dbNotification->id,
                        'user_id' => $userId,
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray();
    
                    notification_users::insert($userData);
                });
    
                return ['success' => true, 'message' => 'Notifications sent and saved'];
    
            } catch (\Exception $e) {
                Log::error('Firebase Error: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Process failed', 'error' => $e->getMessage()];
            }
        }
    }
    
