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
    public function send(array $userIds, string $title, string $body, $orderId = null)
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
            ->withNotification(Notification::create($title, $body));

        $messaging->sendMulticast($message, $tokens);

        DB::transaction(function () use ($userIds, $title, $body, $orderId) {
            $notification = notifications::create([
                'title' => $title,
                'body' => $body,
                'order_id' => $orderId,
                'type' => 'general',
            ]);

            notification_users::insert(
                collect($userIds)->map(fn ($id) => [
                    'notification_id' => $notification->id,
                    'user_id' => $id,
                    'is_read' => false,
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
