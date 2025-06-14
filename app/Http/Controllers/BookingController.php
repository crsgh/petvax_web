<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\MedicalHistory;

class BookingController extends Controller
{
    public function index () {
		return view('bookings',[
			'bookings' => Booking::with(['pet:id,name', 'service:id,name', 'clinic:id,name'])
                ->select('bookings.*')
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('bookings.clinic_id', auth()->user()->clinic_id);
                })
                ->get(),
			'clinics' => Clinic::all(),
            'veterinarians' => User::where('role_id', 4)->get(),
            'inventoryItems' => InventoryItem::all(),
           'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
		]);
	}

    public function upsert(Request $request, $id = null)
    {
    
       
        try{
            $validatedData = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'clinic_id' =>'required|exists:clinics,id',
                'service_id' => 'required|exists:services,id',
                'staff_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date',
                'notes' => 'nullable|string'
            ]);
    
            $booking = $id == null ? new Booking : Booking::findOrFail($id);
    
            $booking->pet_id = $validatedData['pet_id'];
            $booking->clinic_id = $validatedData['clinic_id'];
            $booking->client_id = '1';
            $booking->service_id = $validatedData['service_id'];
            $booking->staff_id = $validatedData['staff_id'];
            $booking->appointment_datetime = $validatedData['appointment_date'];
            $booking->notes = $validatedData['notes'];
            $booking->total_amount = 0;
            $booking->save();   

            // Add record 
        }catch(\Illuminate\Validation\ValidationException $e){
            dd($e->errors());
       }
        

        return redirect()->route('bookings')->with('success', 'Pet saved successfully');
    }

    public function complete(Request $request, $id){
        try {
            $booking = Booking::findOrFail($id);
            
            $validatedData = $request->validate([
                'diagnosis' => 'required|string',
                'treatment' => 'required|string',
                'inventory_id' => 'required|exists:inventory_items,id',
            ]);

            // Update booking status and amount
            $booking->status = 'completed';
            $booking->save();

            // Create medical history record
            $medicalHistory = new MedicalHistory();
            $medicalHistory->pet_id = $booking->pet_id;
            $medicalHistory->clinic_id = $booking->clinic_id;
            $medicalHistory->diagnosis = $validatedData['diagnosis'];
            $medicalHistory->treatment = $validatedData['treatment'];
            $medicalHistory->treatment_date = now();
            $medicalHistory->item_used = $request->inventory_id;
            $medicalHistory->attending_vet = $booking->staff_id;
            $medicalHistory->save();

            // add notif 
            // add record

            return redirect()->back()->with('success', 'Booking completed and medical history recorded successfully');

        } catch (\Exception $e) {
            dd($e); 
            return redirect()->back()->with('error', 'Failed to complete booking: ' . $e->getMessage());
        }
    }

    public function action(Request $request){
        try {
            

            $booking = Booking::findOrFail($request->booking_id);
            
            
            $validatedData = $request->validate([
                'action' => 'required|in:confirmed,cancelled,completed,declined',
                'staff_id' => 'nullable|exists:users,id',
            ]);

            
            $booking->status = $validatedData['action'];
            $booking->staff_id = $validatedData['staff_id'] ?? $booking->staff_id;
            $booking->save();

            // add record and notif

            return redirect()->back()->with('success', 'Booking status updated successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to update booking status');
        }
    }

    public function delete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        // add record
        
        return redirect()->back()->with('success', 'Booking deleted successfully');
    }
}
