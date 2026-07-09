<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Notification;
use App\Models\Breed;
use App\Models\Specie;
use App\Models\Clinic;
use App\Models\Service;


class ScheduleController extends Controller
{
    public function index()
    {
        return view('slots',[
            'slots' => Schedule::with('clinic','service')
            ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })->get(),
                'clinics' => Clinic::all(),
            'clinic' => Clinic::when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('_id', auth()->user()->clinic_id);
                })->first(),
            'services' => Service::when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })->get(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsert(Request $request,$id = null){
    
        $schedule = Schedule::findOrNew($id);
        $schedule->day = $request->day;
        $schedule->time_slots = json_encode($request->time_slots);
        $schedule->clinic_id = auth()->user()->role_id != 1 ? auth()->user()->clinic_id : $request->clinic_id;
        $schedule->status = $request->status;
        $schedule->service_id = $request->service_id;
        $schedule->save();

        // add record

        return response()->json([
            'success' => true,
            'message' => 'Schedule saved successfully'
        ]);

        
    }

    public function delete($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    public function duplicate($id,$day)
    {
        $schedule = Schedule::findOrFail($id);
        
        $newSchedule = $schedule->replicate();
        $newSchedule->day = $day;
        $newSchedule->save();

        return redirect()->back()->with('success', 'Schedule duplicated successfully');
    }
}