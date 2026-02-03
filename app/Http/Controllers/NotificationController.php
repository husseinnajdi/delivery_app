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
    public function showbyuser($id)
    {
        $notificationIds = notification_users::where('user_id', $id)->pluck('notification_id');
        return notifications::whereIn('id', $notificationIds)->get();
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

    public function sendtomultyusers()
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->notificationsend($user->id, 'Announcement', 'This is a notification to all users');
            notifications::create([
                'user_id' => $user->id,
                'title' => 'Announcement',
                'body' => 'This is a notification to all users',
                'created_at' => now(),
                'type' => 'announcement',
                'is_read' => false,
            ]);
        }
        return response()->json([
            'message' => 'Notifications sent to all users successfully'
        ], 200);
    }

    public function notificationsend(array $ids, $title, $body)
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $messaging = $factory->createMessaging();
        $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();

        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body));

        foreach ($tokens as $token) {
            $messaging->send($message->withTarget('token', $token));
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
            return response()->json(['message' => 'Failed to save notification', 'error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Notifications sent successfully', 'count' => count($tokens)], 200);
    }

}
