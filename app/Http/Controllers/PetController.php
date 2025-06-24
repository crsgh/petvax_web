<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Specie;
use App\Models\Breed;
use App\Models\Notification;

class PetController extends Controller
{
    /**
     * Display a listing of pets.
     */
    public function index()
    {
        return view('pets', [
            'pets' => Pet::select('pets.*', 'clinics.name as clinic_name', 'users.name as owners_name')
                // ->when(auth()->user()->role_id != 1, function($query) {
                //     return $query->where('pets.clinic_id', auth()->user()->clinic_id);
                // })
                ->leftJoin('users', 'pets.owner_id', '=', 'users.id')
                ->leftJoin('clinics', 'pets.clinic_id', '=', 'clinics.id')
                ->paginate(8),
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
            $breed->image = uploadImage($request->file('image'), 'pets');
        }

        $breed->save();

        // add record

        return redirect()->route('breeds')->with('success', 'Breed saved successfully');
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

    public function deleteSpecie($id)
    {
        $specie = Specie::findOrFail($id);
        $specie->delete();
        
        return redirect()->route('species')->with('success', 'Species deleted successfully');
    }


    /**
     * Store or update a pet record.
     */
    public function upsert(Request $request, $id = null)
    {
       
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'species' => 'required|string|max:255', 
                'breed' => 'nullable|string|max:255',
                'birth_date' => 'nullable|date',
                'owner_id' => 'required|exists:users,id',
                'clinic_id' =>'required|exists:clinics,id',
                'weight' => 'nullable|numeric',
                'gender' => 'nullable|in:male,female,unspecified',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $pet = $id == null ? new Pet : Pet::findOrFail($id);

            $pet->name = $validatedData['name'];
            $pet->species = $validatedData['species'];
            $pet->breed = $validatedData['breed'];
            $pet->birth_date = $validatedData['birth_date'];
            $pet->owner_id = $validatedData['owner_id'];
            $pet->clinic_id = $validatedData['clinic_id']; 
            $pet->weight = $validatedData['weight'];
            $pet->gender = $validatedData['gender'];

            if ($request->hasFile('image')) {
                $pet->image = $this->uploadImage($request->file('image'), 'pets');
            }

            $pet->save();
            
        } catch (\Exception $e) {
            dd($e->getMessage());
            //return redirect()->back()->with('error', 'Error saving pet: ' . $e->getMessage())->withInput();
        }
        return redirect()->route('pets')->with('success', 'Pet saved successfully');
    }
    /**
     * Remove the specified pet.
     */
    public function delete($id)
    {
        $pet = Pet::findOrFail($id);
        $pet->delete();
        
        return response()->json(null, 204);
    }
}
