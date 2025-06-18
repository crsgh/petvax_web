<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class MedicalHistoryController extends Controller
{
    public function getByPet(Request $request,$id){
    $medicalHistory = \App\Models\MedicalHistory::with(['clinic', 'pet', 'veterinarian', 'inventoryItem'])
            ->where('pet_id', $id)
            ->get();
        return response()->json([
            'success' => true,
            'data' => $medicalHistory
        ]);
    }
}