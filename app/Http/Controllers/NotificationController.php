<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notifications;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\MulticastMessage;

class NotificationController extends Controller
{

    public function index()
    {
        return notifications::all();
    }
    public function showbyuser($id)
    {
        $notification = notifications::find('user_id', $id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        return response()->json($notification);
    }
    public function sendnotification(Request $request)
    {
        try {
            $this->notificationsend($request->user_id, $request->title, $request->body, );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send notification', 'error' => $e->getMessage()], 500);
        }
        $notification = notifications::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
            'created_at' => now(),
            'type' => $request->type,
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Notification sent successfully',
            $notification
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
        $factory = (new Factory)->withServiceAccount(base_path('secret_key.json'));
        $messaging = $factory->createMessaging();

        // get all FCM tokens of users
        $tokens = User::whereIn('id', $ids)->pluck('FCMtoken')->filter()->toArray();

        if (empty($tokens)) {
            return response()->json(['message' => 'No FCM tokens found'], 404);
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body));

        foreach ($tokens as $token) {
            $messaging->send($message->withTarget('token', $token));
        }

        return response()->json(['message' => 'Notifications sent successfully', 'count' => count($tokens)], 200);
    }

}
