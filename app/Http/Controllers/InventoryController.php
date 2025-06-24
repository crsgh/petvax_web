<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\Notification;

class InventoryController extends Controller
{
    public function index () {
		return view('inventory',[
			'inventoryItems' => InventoryItem::leftJoin('categories', 'inventory_items.category_id', '=', 'categories.id')
			
				->select('inventory_items.*', 'categories.name as category_name')
				->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('inventory_items.clinic_id', auth()->user()->clinic_id);
                })
				->get(),
			'categories' => Category::when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })->get(),
			'clinics' => Clinic::all(),
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
		
       	try{
			$validatedData = $request->validate([
				'name' => 'required|string|max:255',
				'description' => 'nullable|string',
				'quantity' => 'required|integer|min:0',
				'category_id' => 'required|exists:categories,id',
				'clinic_id' =>'required|exists:clinics,id',
				'added_by' =>'required|exists:users,id',
			]);

			$inventory = $id == null ? new InventoryItem : InventoryItem::findOrFail($id);
			$inventory->name = $validatedData['name'];
			$inventory->description = $validatedData['description'];
			$inventory->quantity = $validatedData['quantity'];
			$inventory->category_id = $validatedData['category_id'];
			$inventory->clinic_id = $validatedData['clinic_id'];
			$inventory->added_by = $validatedData['added_by'];

			// add record
		
			$inventory->save();
    	}catch(\Illuminate\Validation\ValidationException $e){
            dd($e->errors());
   	 	}

        return redirect()->route('inventory')->with('success', 'Pet saved successfully');
    }

    /**
     * Remove the specified pet.
     */
    public function delete($id)
    {
        $inventory = InventoryItem::findOrFail($id);
        $inventory->delete();
        
        return redirect()->route('inventory')->with('success', 'Item deleted successfully');
    }
}
