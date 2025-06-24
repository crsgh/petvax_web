<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory;
use App\Models\Clinic;
use App\Models\Pet;
use App\Models\User;
use App\Models\Notification;
use App\Models\InventoryItem;

class MedicalHistoryController extends Controller
{
    public function index () {
		return view('medical-histories',[
            'medicalHistories' => MedicalHistory::
                with(['pet', 'veterinarian'])
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('medical_histories.clinic_id', auth()->user()->clinic_id);
                })
                ->orderBy('medical_histories.treatment_date', 'desc')
                ->get(),
			'clinics' => Clinic::all(),
			'pets' => Pet::with('owner')->get(),
			'veterinarians' => User::where('role_id', 4)->get(),
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
                'pet_id' =>'required|exists:pets,id',
                'diagnosis' => 'required|string|max:255',
                'treatment' => 'required|string|max:255',
               
                'inventory_id' => 'required|exists:inventory_items,id',
                'notes' => 'required|string|max:255',
                'treatment_date' => 'required|date',
                 'vet_id' => 'nullable|exists:users,id',
                 'veterinarian' => 'nullable|exists:users,id',
            ]);

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

            $inventory = InventoryItem::find($validatedData['inventory_id']);
            $inventory->decrement('quantity');

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
