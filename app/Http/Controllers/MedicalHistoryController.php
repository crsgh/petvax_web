<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory;
use App\Models\Clinic;
use App\Models\Pet;
use App\Models\User;
use App\Models\Notification;
use App\Models\InventoryItem;
use App\Models\Specie;
use App\Models\Booking;



class MedicalHistoryController extends Controller
{
    public function index () {
        
        // Get completed booking pet IDs for non-admin users
        $query = Booking::where('status', 'completed');
        if (auth()->user()->role_id != 1) {
            $query->where('clinic_id', auth()->user()->clinic_id);
        }
        $completedPetIds = $query->distinct()->pluck('pet_id')->toArray();

        // Build pets query with joins and conditions
        $petsQuery = Pet::with(['owner']);

        if (auth()->user()->role_id != 1) {
            $petsQuery->whereIn('_id', $completedPetIds);
        }

		return view('medical-histories',[
            'medicalHistories' => MedicalHistory::with(['pet' => function($query) {
                    $query->withTrashed()->with(['owner']);
                }, 'veterinarian'])
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->orderBy('_id', 'desc')
                ->get(),
			'clinics' => Clinic::all(),
			'pets' => auth()->user()->role_id == 5 
                ? Pet::where('owner_id', auth()->id())->paginate(8)
                : $petsQuery->get(),
			'veterinarians' => User::where('role_id', 4)
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->get(),
            'species' => Specie::all(),
            'inventories' => InventoryItem::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->get(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
		]);
	}

    /**
     * Store or update a pet record.
     */
    public function upsert(Request $request, $id = null)
    {
        try {
            $validatedData = $request->validate([
                'pet_id' =>'required|exists:pets,_id',
                'diagnosis' => 'required|string|max:255',
                'treatment' => 'required|string|max:255',
               
                //'inventory_id' => 'required|exists:inventory_items,_id',
                'notes' => 'required|string|max:255',
                'treatment_date' => 'required|date',
                 'vet_id' => 'nullable|exists:users,_id',
                 'veterinarian' => 'nullable',
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
            $history = $id == null ? new MedicalHistory : MedicalHistory::findOrFail($id);

            $history->pet_id = $validatedData['pet_id'];
            $history->diagnosis = $validatedData['diagnosis'];
            $history->treatment = $validatedData['treatment'];
             $history->staff_id = $validatedData['vet_id'];
            $history->attending_vet = $validatedData['veterinarian'];
            $history->notes = $validatedData['notes'];
            // Ensure treatment_date is in Y-m-d format
            $history->treatment_date = \Carbon\Carbon::parse($validatedData['treatment_date'])->format('Y-m-d');
            $history->clinic_id = $request->input('clinic_id');
            $history->save();

            //$inventory = InventoryItem::find($validatedData['inventory_id']);
            //$inventory->decrement('quantity');

            // add record
           
        } catch(\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        } 

        return redirect()->route('medical-histories')->with('success', 'Pet saved successfully');
    }

public function followup(Request $request)
{
   
    try {
        $validatedData = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes' => 'required|string|max:255'
        ]);
       
        $history = MedicalHistory::findOrFail((int)$request->id);
        
        $history->notes = $validatedData['notes'];
        $history->followup = \Carbon\Carbon::parse($validatedData['appointment_date'])
            ->setTimeFromTimeString($validatedData['appointment_time'])
            ->format('Y-m-d H:i:s');
            
        $history->save();

        return redirect()->route('medical-histories')->with('success', 'Follow-up appointment created successfully');

    } catch(\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    }
}
    public function delete($id)
    {
        $history = MedicalHistory::findOrFail($id);
        $history->delete();
        
        return redirect()->back()->with('success', 'Medical history deleted successfully');
    }
}
