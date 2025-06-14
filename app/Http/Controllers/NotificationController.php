<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index () {
		return view('notifications',[
            'notifications' => Notification::where('user_id', auth()->id())->get(),
		]);
	}
}
