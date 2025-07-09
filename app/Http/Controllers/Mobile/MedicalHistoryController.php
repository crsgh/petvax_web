<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class MedicalHistoryController extends Controller
{
    public function getByPet(Request $request,$id){
    $query = \App\Models\MedicalHistory::with(['clinic', 'pet', 'veterinarian', 'inventoryItem'])
            ->where('pet_id', $id);
            
    if ($request->clinic && $request->role != 1 && $request->role != 5) {
        $query->where('clinic_id', $request->clinic);
    }
    
    $medicalHistory = $query->get();
        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }
}