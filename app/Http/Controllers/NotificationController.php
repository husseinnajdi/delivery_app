<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\notifications;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

class NotificationController extends Controller
{
    
    public function index()
    {
        return notifications::all();
    }
    public function showbyuser($id)
    {
        $notification = notifications::find('user_id',$id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }
        return response()->json($notification);
    }
    public function sendnotification(Request $request)
{
    $factory = (new Factory)
        ->withServiceAccount(base_path('secret_key.json'));

    $messaging = $factory->createMessaging();
    $fcmtoken=User::where('id',$request->user_id)->pluck('FCMtoken')->first();
    if( !$fcmtoken){
        return response()->json(['message' => 'FCM token not found for the user'], 404);
    }
    $message = CloudMessage::withTarget('token', $fcmtoken)
        ->withNotification(
            Notification::create($request->title, $request->body)
        );

    $messaging->send($message);
    $notification = notifications::create([
        'user_id' => $request->user_id,
        'title' => $request->title,
        'body' => $request->body,
        'created_at' => now(),
        'type' => $request->type,
        'is_read' => false,
    ]);

    return response()->json([
        'message' => 'Notification sent successfully', $notification
    ], 200);
}
}
