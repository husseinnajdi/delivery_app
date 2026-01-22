<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\notifications;
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
}
