<?php
namespace App\Services;

use App\Models\User;
use App\Models\notifications;
use App\Models\notification_users;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    public function send(array $userIds, string $title, string $message, $orderId = null,$sender_user_id)
    {
        $messaging = $this->firebase();

        $tokens = User::whereIn('id', $userIds)
            ->pluck('FCMtoken')
            ->filter()
            ->toArray();

        if (empty($tokens)) {
            throw new \Exception('No FCM tokens found');
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $message));

        $messaging->sendMulticast($message, $tokens);

        DB::transaction(function () use ($userIds, $title, $message, $orderId,$sender_user_id) {
            $notification = notifications::create([
                'title' => $title,
                'sender_user_id'=>$sender_user_id,
                'message' => $message,
                'order_id' => $orderId,
                'type' => 'info',
            ]);

            notification_users::insert(
                collect($userIds)->map(fn ($id) => [
                    'notification_message_id' => $notification->id,
                    'recipient_user_id' => $id,
                    'is_read' => false,
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray()
            );
        });
    }

    private function firebase()
    {
        $credentials = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);
        return (new Factory)->withServiceAccount($credentials)->createMessaging();
    }
}
