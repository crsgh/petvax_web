<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityRecord;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\User;

class ActivityRecordController extends Controller
{
    public function index()
    {

    
         return view('activity-records',[
            'activities' => ActivityRecord::with(['user', 'clinic'])->when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->get(),
            'users' => User::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }
}
