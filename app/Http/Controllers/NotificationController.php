<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index () {
		return view('notifications',[
			'notifications' => Notification::where('user_id', auth()->id())->where('type', 'clinic')->where('for_user' , 0)->where('is_read' , 0)->get(),
			'notifs' => Notification::where('user_id', auth()->id())->where('for_user' , 0)->get(),
		]);
	}
}
