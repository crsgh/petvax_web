<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\Specie;

class ServiceController extends Controller
{
    public function index(){
        $species = Specie::all();

        // The service form requires a species to attach; without one the page
        // can't be used, so guide the user instead of erroring.
        if ($species->isEmpty()) {
            return redirect()->route('species')
                ->with('warning', 'Please add at least one species before managing services.');
        }

        return view('services',[
			'services' => Service::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->with('clinic')->get(),
			'clinics' => Clinic::all(),
            'species' => $species,
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
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'pet_size' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'clinic_id' =>'required|exists:clinics,_id',
            'status' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'nullable|string|max:255',
            'home_service' => 'nullable|string|max:255',
            // 'gcash_number' => 'nullable|string|max:255',
        ]);

        $service = $id == null ? new Service : Service::findOrFail($id);
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $service->image);
            }
            
            $image = $request->file('image');
            $imagePath = $this->uploadImage($image, 'services');
            $service->image = $imagePath;
        }

        $service->name = $validatedData['name'];
        $service->species = $validatedData['species'];
        $service->clinic_id = $validatedData['clinic_id']; 
        $service->size = $validatedData['pet_size']; 
        $service->description = $validatedData['description']; 
        $service->price = $validatedData['price']; 
        $service->status = $validatedData['status'];
        $service->category = $validatedData['category'];
        $service->home_service = isset($validatedData['home_service']) ? 1 : 0;
       
        $service->save();

       } catch(\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
       } catch (\Throwable $e) {
            \Log::error('Service save failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving the service.')->withInput();
       }

        return redirect()->route('services')->with('success', 'Service saved successfully');
    }

    /**
     * Remove the specified pet.
     */
    public function delete($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        
        return redirect()->route('services')->with('success', 'Pet deleted successfully');
    }
}
