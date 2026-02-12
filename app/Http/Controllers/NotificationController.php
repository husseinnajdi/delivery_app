<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notifications;
use JsonException;
use App\Services\NotificationService;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\MulticastMessage;
use App\Models\notification_users;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class NotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    public function index()
    {
        return notifications::all();
    }
    public function showbyuser(Request $request)
    {
        $userId = $request->auth_user->id;

        $notifications = notifications::join(
            'notification_recipients',
            'notification_messages.id',
            '=',
            'notification_recipients.notification_message_id'
        )
            ->where('notification_recipients.recipient_user_id', $userId)
            ->select(
                'notification_messages.*',
                'notification_recipients.is_read'
            )
            ->orderBy('notification_messages.created_at', 'desc')
            ->paginate(10);

            return response()->json($notifications->items());
    }



    public function markasread(Request $request)
    {
        $notificationUser = notification_users::where('notification_message_id', $request->id)
            ->where('recipient_user_id', $request->auth_user->id)
            ->first();

        if ($notificationUser) {
            $notificationUser->is_read = true;
            $notificationUser->read_at = now();
            $notificationUser->save();

            return response()->json(['message' => 'Notification marked as read'], 200);
        } else {
            return response()->json(['message' => 'Notification not found for user'], 404);
        }
    }
    public function sendnotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'sender_user_id'=>'required|integer',
            'order_id'=>'nullable|integer',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $userIds = is_array($request->user_id)
            ? $request->user_id
            : [$request->user_id];

        $this->service->send(
            $userIds,
            $request->title,
            $request->message,
            $request->order_id,
            $request->sender_user_id
        );

        return response()->json(['message' => 'Notification sent']);
    }
    public function notifyAll(string $title, string $body, $orderId = null)
    {
        $userIds = User::pluck('id')->toArray();
    
        if (empty($userIds)) {
            throw new \Exception('No users found');
        }
    
        $this->service->send($userIds, $title, $body, $orderId);
    }
    

}

