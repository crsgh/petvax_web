<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function read($id){
        $Notifications = Notification::where('user_id', $id)->get();
        $Notifications->each(function($notification) {
            $notification->update(['is_read' => 1]);
        });
        return response()->json([
            'status' => 'success',
        ]);
    }

    public function readById($id){
        $notification = Notification::find($id);
        if($notification){
            $notification->update(['is_read' => 1]);
            return response()->json([
                'status' => 'success',
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Notification not found'
        ], 404);
    }

    public function getNotificationsByUser($id){
    $notifications = Notification::where('user_id', $id)
        ->where('is_read', 0)
        ->with('pet')
        ->get();
        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }
}