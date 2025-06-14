<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Schedule;

class ScheduleController extends Controller
{
public function checkScheduleAvailability (Request $request)
{
    $request->validate([
        'clinic_id' => 'required|integer|exists:clinics,id',
        'service_id' => 'required|integer|exists:services,id',
        'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
    ]);

    $clinicId = $request->input('clinic_id');
    $serviceId = $request->input('service_id');
    $day = $request->input('day');

    $timeSlots = Schedule::where('clinic_id', $clinicId)
        ->where('service_id', $serviceId)
        ->where('clinic_id', $clinicId)
        ->where('day', $day)
        ->where('status', 1)
        ->first();

    return response()->json([
        'success' => true,
        'data' => $timeSlots->time_slots ?? [],
    ]);
}
}
