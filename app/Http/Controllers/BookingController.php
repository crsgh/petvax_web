<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\MedicalHistory;
use App\Models\ActivityRecord;
use App\Models\Service;
use App\Models\Pet;

class BookingController extends Controller
{
    public function index () {
        $bookings = Booking::with(['pet' => function($query) {
                return $query->withTrashed();
            }, 'service:id,name,category', 'clinic:id,name'])
                ->when(auth()->user()->role_id != 1, function($query) {
                    if (auth()->user()->role_id == 4) {
                        return $query->where('staff_id', auth()->id());
                    }
                    if (auth()->user()->role_id == 5) {
                        return $query->where('client_id', auth()->user()->id);
                    }
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->orderBy('_id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $bookings->each(function($booking) {
                $homeService = \App\Models\HomeService::where('booking_id', $booking->id)
                            
                                ->first();
                if ($homeService) {
                    $booking->latitude = $homeService->latitude;
                    $booking->longitude = $homeService->longitude;
                    $booking->isHomeService = !is_null($homeService);
                }

                
            });
		return view('bookings',[
            'bookings' => $bookings,
			'clinics' => Clinic::all(),
            'veterinarians' => User::where('role_id', 4)
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->get(),
            'inventoryItems' => InventoryItem::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->get(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->where('for_user' , 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
		]);
	}

    public function salesReport(Request $request)
    {
       
       return view('sales-report',[
			'bookings' => Booking::with(['pet' => function($query) {
                    return $query->withTrashed();
                }, 'service:id,name', 'clinic:id,name'])
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->get(),
			
            'services' => Service::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->get(),
          
           
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->where('for_user' , 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
		]);
    }

    public function upsert(Request $request, $id = null)
    {
    
        // dd($request->all);
        try{
            $validatedData = $request->validate([
                'pet_id' => 'required|exists:pets,_id',
                'clinic_id' =>'required|exists:clinics,_id',
                'service_id' => 'required|exists:services,_id',
                'staff_id' => 'required|exists:users,_id',
                'appointment_date' => 'required|date',
                'notes' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'payment_reference' => 'nullable|string',
                'total_amount' => 'nullable|numeric'
            ]);
    
            $booking = $id == null ? new Booking : Booking::findOrFail($id);
            $pet = \App\Models\Pet::findOrFail($validatedData['pet_id']);
            $service = \App\Models\Service::findOrFail($validatedData['service_id']);
    
            $booking->pet_id = $validatedData['pet_id'];
            $booking->clinic_id = $validatedData['clinic_id'];
            $booking->client_id = $pet->owner_id;
            $booking->service_id = $validatedData['service_id'];
            $booking->staff_id = $validatedData['staff_id'];
            $booking->appointment_datetime = $validatedData['appointment_date'];
            $booking->notes = $validatedData['notes'] ?? '';
            $booking->total_amount = $validatedData['total_amount'] ?? $service->price;
            $booking->payment_method = $validatedData['payment_method'] ?? 'cash';
            $booking->payment_reference = $validatedData['payment_reference'] ?? null;
            
            // Handle payment proof upload
            if ($request->hasFile('proof')) {
                $booking->payment_proof = $this->uploadImage($request->file('proof'), 'payment_proofs');
            }
            
            $booking->save();   

            ActivityRecord::create([
                'user_id' => auth()->id(),
                'clinic_id' => $booking->clinic_id,
                'action' => $id === null ? 'created booking' : 'updated booking',
                'description' => $id === null 
                    ? 'Created a new booking for pet ' . Pet::find($booking->pet_id)->name 
                    : 'Updated booking ID ' . $booking->id,
            ]);
            
            // Create notification for new booking
            if ($id === null) {
                Notification::create([
                    'user_id' => $booking->client_id,
                    'clinic_id' => $booking->clinic_id,
                    'pet_id' => $booking->pet_id,
                    'title' => 'Booking Submitted',
                    'message' => $booking->payment_proof
                        ? 'Your booking with payment proof has been submitted.'
                        : 'Your booking has been submitted.',
                    'type' => 'booking',
                    'for_user' => 1,
                    'is_read' => 0,
                ]);
                
                // Notification for clinic staff
                Notification::create([
                    'clinic_id' => $booking->clinic_id,
                    'pet_id' => $booking->pet_id,
                    'title' => 'New Booking',
                    'message' => 'New booking has been submitted for your clinic.',
                    'type' => 'booking',
                    'for_user' => 0,
                    'is_read' => 0,
                ]);
            }

        }catch(\Illuminate\Validation\ValidationException $e){
            return redirect()->back()->withErrors($e->errors())->withInput();
       }
        

        return redirect()->route('bookings')->with('success', 'Booking saved successfully');
    }



    public function complete(Request $request, $id){
        try {
           
            $booking = Booking::findOrFail($id);
            
            $validatedData = $request->validate([
                'diagnosis' => 'required|string',
                'treatment' => 'required|string',
                //'inventory_id' => 'required|exists:inventory_items,_id',
            ]);

            // Decode the selected inventories JSON string
            $selectedInventories = json_decode($request->input('selected_inventories'), true);

            // Loop through each selected inventory and deduct quantities
            foreach ($selectedInventories as $inventory) {
                $inventoryItem = InventoryItem::find($inventory['id']);
                if ($inventoryItem) {
                    $inventoryItem->decrement('quantity', $inventory['quantity']);
                }
            }

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
            $medicalHistory->staff_id = $booking->staff_id;
            $medicalHistory->save();

        

            // add notif 
            Notification::create([
                'user_id'    => $booking->client_id,
                'clinic_id'  => $booking->clinic_id,
                'pet_id'     => $booking->pet_id,
                'title'      => 'Booking Completed',
                'message'    => 'Your booking for pet ' . Pet::find($booking->pet_id)->name . ' has been completed.',
                'type'       => 'booking',
                'for_user'   => 1,
                'is_read'    => 0,
            ]);

            // add record
            ActivityRecord::create([
                'user_id' => auth()->id(),
                'clinic_id' => $booking->clinic_id,
                'action' => 'completed booking',
                'description' => 'Completed booking ID ' . $booking->id . ' for pet ' . Pet::find($booking->pet_id)->name,
            ]);

            return redirect()->back()->with('success', 'Booking completed and medical history recorded successfully');

        } catch (\Throwable $e) {
            \Log::error('Complete booking failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete booking. Please try again.');
        }
    }

    public function action(Request $request){
        try {
            

            $booking = Booking::findOrFail($request->booking_id);
            
            
            $validatedData = $request->validate([
                'action' => 'required|in:confirmed,cancelled,completed,declined',
                'staff_id' => 'nullable|exists:users,_id',
            ]);

            
            $booking->status = $validatedData['action'];
            $booking->staff_id = $validatedData['staff_id'] ?? $booking->staff_id;
            $booking->save();

            // add record and notif
            ActivityRecord::create([
                'user_id' => auth()->id(),
                'clinic_id' => $booking->clinic_id,
                'action' => $validatedData['action'] . ' booking',
                'description' => ucfirst($validatedData['action']) . ' booking ID ' . $booking->id . ' for pet ' . Pet::find($booking->pet_id)->name,
            ]);

            Notification::create([
                'user_id'    => $booking->client_id,
                'clinic_id'  => $booking->clinic_id,
                'pet_id'     => $booking->pet_id,
                'title'      => 'Booking ' . ucfirst($validatedData['action']),
                'message'    => 'Your booking for pet ' . Pet::find($booking->pet_id)->name . ' has been ' . $validatedData['action'] . '.',
                'type'       => 'booking',
                'for_user'   => 1,
                'is_read'    => 0,
            ]);

            return redirect()->back()->with('success', 'Booking status updated successfully');
        } catch (\Throwable $e) {
            \Log::error('Update booking status failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update booking status');
        }
    }

    public function delete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        ActivityRecord::create([
            'user_id' => auth()->id(),
            'clinic_id' => $booking->clinic_id,
            'action' => 'deleted booking',
            'description' => 'Deleted booking ID ' . $booking->id . ' for pet ' . Pet::find($booking->pet_id)->name,
        ]);
        
        return redirect()->back()->with('success', 'Booking deleted successfully');
    }
    
    /**
     * Upload an image file to the specified directory
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string The path to the uploaded image
     */
    protected function uploadImage($file, $directory = 'uploads')
    {
        if ($directory === 'payment_proofs') {
            $directory = 'payments';
        }
        
        // Create directory if it doesn't exist
        $storage_path = storage_path('app/public/' . $directory);
        if (!file_exists($storage_path)) {
            mkdir($storage_path, 0755, true);
        }
        
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('public/' . $directory, $filename);
        return str_replace('public/', 'storage/', $path);
    }

public function decline(Request $request, $id)
{
    try {
        $booking = Booking::findOrFail($id);
        
        $validatedData = $request->validate([
            'notes' => 'required|string'
        ]);

        $booking->status = 'declined';
        $booking->notes = $validatedData['notes'];
        $booking->save();

        // Create activity record
        ActivityRecord::create([
            'user_id' => auth()->id(),
            'clinic_id' => $booking->clinic_id,
            'action' => 'declined booking',
            'description' => 'Declined booking ID ' . $booking->id . ' for pet ' . Pet::find($booking->pet_id)->name . ' with reason: ' . $validatedData['notes']

        ]);

        // Create notification
        Notification::create([
            'user_id' => $booking->client_id,
            'clinic_id' => $booking->clinic_id,
            'pet_id' => $booking->pet_id,
            'title' => 'Booking Declined',
            'message' => 'Your booking for pet ' . Pet::find($booking->pet_id)->name . ' has been declined. Reason: ' . $validatedData['notes'],
            'type' => 'booking',
            'for_user' => 1,
            'is_read' => 0,
        ]);

        return redirect()->back()->with('success', 'Booking declined successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to decline booking: ' . $e->getMessage());
    }
}
public function cancel(Request $request, $id)
{
    try {
        $booking = Booking::findOrFail($id);
        
        $validatedData = $request->validate([
            'notes' => 'required|string'
        ]);

        $booking->status = 'cancelled';
        $booking->notes = $validatedData['notes'];
        $booking->save();

        // Create activity record
        ActivityRecord::create([
            'user_id' => auth()->id(),
            'clinic_id' => $booking->clinic_id,
            'action' => 'cancelled booking',
            'description' => 'Cancelled booking ID ' . $booking->id . ' for pet ' . Pet::find($booking->pet_id)->name . ' with reason: ' . $validatedData['notes']

        ]);

        // Create notification
        Notification::create([
            'user_id' => $booking->client_id,
            'clinic_id' => $booking->clinic_id,
            'pet_id' => $booking->pet_id,
            'title' => 'Booking Cancelled',
            'message' => 'Your booking for pet ' . Pet::find($booking->pet_id)->name . ' has been cancelled. Reason: ' . $validatedData['notes'],
            'type' => 'booking',
            'for_user' => 1,
            'is_read' => 0,
        ]);

        return redirect()->back()->with('success', 'Booking cancelled successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
    }
}

}
