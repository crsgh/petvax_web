<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use App\Models\Notification;

class ClinicController extends Controller
{
    public function index()
    {
        if(auth()->user()->role_id != 1) {
            return view('clinic-profile',[
                'clinic' => Clinic::where('id', auth()->user()->clinic_id)->first(),
                'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
            ]); 
        }
        return view('clinics', [
            'clinics' => Clinic::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsert(Request $request, $id = null)
    {
       
       
     
        try {
            $validatedData = $request->validate([
                'clinic_name' => 'required|max:255',
                'clinic_address' => 'required|max:255',
                'clinic_phone' => 'required|max:255',
                'clinic_email' => 'required|email|max:255',
                'operating_days' => 'required|array',
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'clinic_status' => 'required|in:active,inactive',
            ]);
            $clinic = $id == null ? new Clinic() : Clinic::findOrFail($id);
            
            if ($request->hasFile('clinic_image')) {
                $clinic->image = $this->uploadImage($request->file('clinic_image'), 'clinics');
            }
            $clinic->name = $validatedData['clinic_name'];
            $clinic->address = $validatedData['clinic_address'];
            $clinic->contact = $validatedData['clinic_phone'];
            $clinic->email = $validatedData['clinic_email'];
            $clinic->operation_days = json_encode($validatedData['operating_days']);
            $clinic->opening_time = $validatedData['opening_time'];
            $clinic->closing_time = $validatedData['closing_time'];
            $clinic->latitude = $validatedData['latitude'];
            $clinic->longitude = $validatedData['longitude'];
            //$clinic->image = $validatedData['clinic_image'];
            $clinic->status = $validatedData['clinic_status'];
            $clinic->description = "";

            $clinic->save();

            // add record
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }
        
       
        
       
      

        return redirect()->route('clinics');
    }

    public function delete($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->delete();
   
        
        return redirect()->route('clinics')->with('success', 'Clinic deleted successfully');
    }
}
