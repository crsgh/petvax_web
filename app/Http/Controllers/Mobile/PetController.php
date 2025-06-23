<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Clinic;
use App\Models\Specie;
use App\Models\Breed;


class PetController extends Controller
{
    public function index(Request $request)
    {
        $pets = Pet::with(['owner', 'clinic'])
            ->when($request->owner_id, function($query, $owner_id) {
                return $query->where('owner_id', $owner_id);
            })
            ->when($request->clinic_id, function($query, $clinic_id) {
                return $query->where('clinic_id', $clinic_id);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pets
        ]);
    }

    public function getByOwner($ownerId)
    {
        $pets = Pet::with(['owner', 'clinic'])
            ->where('owner_id', $ownerId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pets
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:100',
            'breed' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|string|in:male,female',
            'weight' => 'required|numeric|min:0',
            'owner_id' => 'required|exists:users,id',
            'clinic_id' => 'nullable'

        ]);

        $clinic = Clinic::find($request->clinic_id) ?? Clinic::first();
        $request->merge(['clinic_id' => $clinic->id]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pet = Pet::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $pet
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'species' => 'string|max:100',
            'breed' => 'string|max:100',
            'birth_date' => 'date',
            'owner_id' => 'exists:users,id',
            'clinic_id' => 'exists:clinics,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $pet->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $pet
        ]);
    }

    public function destroy($id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pet deleted successfully'
        ]);
    }

    public function show($id)
    {
        $pet = Pet::with(['owner', 'clinic'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $pet
        ]);
    }

    function getDetails(){
        $breeds = Breed::all()->select(['id','name']);
        $species = Specie::all()->select(['id','name']);
        return response()->json([
            'status' => 'success',
            'data' => [
                'species' => $species,
                'breeds' => $breeds
            ]
        ]);
    }
}