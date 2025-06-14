<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;

class AppointmentController extends Controller
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
}