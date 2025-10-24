<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\Specie;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(){
        return view('services',[
			'services' => Service::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('clinic_id', auth()->user()->clinic_id);
            })->with('clinic')->get(),
			'clinics' => Clinic::all(),
            'species' => Specie::all(),
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
                'category' => 'required|string|max:50',
                'price' => 'required|numeric',
                'duration' => 'required|integer|min:1',
                'status' => 'required|in:active,inactive',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $service = $id == null ? new Service : Service::findOrFail($id);
            
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($service->image) {
                    Storage::delete('public/' . $service->image);
                }
                
                $imagePath = $request->file('image')->store('services', 'public');
                $service->image = $imagePath;
            }

            $service->name = $validatedData['name'];
            $service->category = $validatedData['category'];
            $service->price = $validatedData['price']; 
            $service->duration = $validatedData['duration'];
            $service->description = $validatedData['description']; 
            $service->status = $validatedData['status'];
            $service->clinic_id = auth()->user()->clinic_id ?? 1; // Use current user's clinic
            $service->species = null; // Set to null for now
            $service->size = 'medium'; // Default size
            $service->home_service = false; // Default to false
           
            $service->save();

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Service saved successfully']);
            }
            
            return redirect()->route('services')->with('success', 'Service saved successfully');

        } catch(\Illuminate\Validation\ValidationException $e) {
            \Log::error('Service validation failed:', $e->errors());
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Service save failed:', ['error' => $e->getMessage(), 'request' => $request->all()]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save service: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to save service: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified pet.
     */
    public function delete(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);
            
            // Delete associated image if exists
            if ($service->image) {
                Storage::delete('public/' . $service->image);
            }
            
            $service->delete();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Service deleted successfully']);
            }
            
            return redirect()->route('services')->with('success', 'Service deleted successfully');
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete service: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }
}
