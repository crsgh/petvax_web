<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Specie;
use App\Models\Breed;
use App\Models\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;


class PetController extends Controller
{

    public function index()
    {
        // Get completed booking pet IDs for non-admin users
        $query = Booking::where('status', 'completed');
        if (auth()->user()->role_id != 1) {
            $query->where('clinic_id', auth()->user()->clinic_id);
        }
        $completedPetIds = $query->distinct()->pluck('pet_id')->toArray();
        

        // Build pets query with joins and conditions
        $petsQuery = Pet::select('pets.*', 'clinics.name as clinic_name', 'users.name as owners_name')
            ->leftJoin('users', 'pets.owner_id', '=', 'users.id')
            ->leftJoin('clinics', 'pets.clinic_id', '=', 'clinics.id')
            ->whereNull('pets.deleted_at');

        // Filter by completed pets for non-admin users    
        if (auth()->user()->role_id != 1) {
            $petsQuery->whereIn('pets.id', $completedPetIds);
        }

        return view('pets', [
            'pets' => auth()->user()->role_id == 5 
                ? Pet::where('owner_id', auth()->id())->whereNull('deleted_at')->paginate(8)
                : $petsQuery->paginate(8),
            'owners' => User::where('role_id', 5)->get(),
            'clinics' => Clinic::all(),
            'species' => Specie::all(), 
            'breeds' => Breed::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function show($id)
    {
        try {
            $pet = Pet::with(['petOwner', 'clinic', 'species', 'breed'])->findOrFail($id);
            return response()->json($pet);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Pet not found'], 404);
        }
    }

    public function getBreedsBySpecies($speciesId)
    {
        try {
            $breeds = Breed::where('species_id', $speciesId)->get(['id', 'name']);
            return response()->json($breeds);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load breeds'], 500);
        }
    }

    public function breeds()
    {
        return view('breeds', [
            
            'clinics' => Clinic::all(),
            'species' => Specie::all(),
            'breeds' => Breed::with(['clinic', 'species'])
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('breeds.clinic_id', auth()->user()->clinic_id);
                })->paginate(8),
                'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsertBreed(Request $request, $id = null)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'species_id' => 'required|exists:species,id',
                'clinic_id' => 'required|exists:clinics,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $breed = $id == null ? new Breed : Breed::findOrFail($id);

            $breed->name = $validatedData['name'];
            $breed->species_id = $validatedData['species_id'];
            $breed->clinic_id = $validatedData['clinic_id'];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('breeds', 'public');
                $breed->image = $imagePath;
            }

            $breed->save();

            return redirect()->route('breeds')->with('success', 'Breed saved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save breed: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteBreed($id)
    {
        $breed = Breed::findOrFail($id);
        $breed->delete();
        
        return response()->json(null, 204);
    }

    public function species()
    {
        return view('species', [
            
            'clinics' => Clinic::all(),
            'species' => Specie::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('species.clinic_id', auth()->user()->clinic_id);
            })->get(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsertSpecie(Request $request, $id = null)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'clinic_id' => 'required|exists:clinics,id',
        ]);

        $specie = $id == null ? new Specie : Specie::findOrFail($id);

        $specie->name = $validatedData['name'];
        $specie->clinic_id = $validatedData['clinic_id'];

        $specie->save();

        return redirect()->route('species')->with('success', 'Species saved successfully');
    }

    public function deleteSpecie(Request $request, $id)
    {
        try {
            $specie = Specie::findOrFail($id);
            $specie->delete();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Species deleted successfully']);
            }
            
            return redirect()->route('species')->with('success', 'Species deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete species: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete species: ' . $e->getMessage());
        }
    }


    /**
     * Store or update a pet record.
     */
    public function upsert(Request $request, $id = null)
    {
       
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'species_id' => 'nullable|exists:species,id',
                'species' => 'nullable|exists:species,id', 
                'breed_id' => 'nullable|exists:breeds,id',
                'breed' => 'nullable|exists:breeds,id',
                'birth_date' => 'nullable|date',
                'owner_id' => 'required|exists:users,id',
                'clinic_id' =>'nullable|exists:clinics,id',
                'weight' => 'nullable|numeric',
                'gender' => 'nullable|in:male,female,unspecified',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            $pet = $id == null ? new Pet : Pet::findOrFail($id);
            
            // Handle species - convert ID to name
            $speciesId = $validatedData['species_id'] ?? $validatedData['species'] ?? null;
            if ($speciesId) {
                try {
                    $species = Specie::findOrFail($speciesId);
                    $pet->species = $species->name;
                } catch (\Exception $e) {
                    $pet->species = 'Unknown';
                }
            }
            
            // Handle breed - convert ID to name
            $breedId = $validatedData['breed_id'] ?? $validatedData['breed'] ?? null;
            if ($breedId) {
                try {
                    $breed = Breed::findOrFail($breedId);
                    $pet->breed = $breed->name;
                } catch (\Exception $e) {
                    $pet->breed = 'Mixed';
                }
            }

            $pet->name = $validatedData['name'];
            $pet->birth_date = $validatedData['birth_date'];
            $pet->owner_id = $validatedData['owner_id'];
            $pet->clinic_id = $validatedData['clinic_id'] ?? auth()->user()->clinic_id ?? 1; 
            $pet->weight = $validatedData['weight'];
            $pet->gender = $validatedData['gender'];

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('pets', 'public');
                $pet->image = $imagePath;
            }

            $pet->save();
            
            return redirect()->route('pets')->with('success', 'Pet saved successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saving pet: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Remove the specified pet.
     */
    public function delete($id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
        
        return response()->json([
            'success' => true
        ], 200);
    }
}
