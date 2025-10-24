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
				'clinic_id' =>'nullable|exists:clinics,id',
				'added_by' =>'nullable|exists:users,id',
				'sku' => 'nullable|string',
				'unit_price' => 'nullable|numeric|min:0',
			]);

			$inventory = $id == null ? new InventoryItem : InventoryItem::findOrFail($id);
			$inventory->name = $validatedData['name'];
			$inventory->description = $validatedData['description'];
			$inventory->quantity = $validatedData['quantity'];
			$inventory->category_id = $validatedData['category_id'] ?? 1; // Default category
			$inventory->clinic_id = $validatedData['clinic_id'] ?? auth()->user()->clinic_id ?? 1;
			$inventory->added_by = $validatedData['added_by'] ?? auth()->id();
			$inventory->sku = $validatedData['sku'];
			$inventory->unit_price = $validatedData['unit_price'] ?? 0.00;

			// add record
		
			$inventory->save();
    	}catch(\Illuminate\Validation\ValidationException $e){
            return redirect()->back()->withErrors($e->errors())->withInput();
   	 	} catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save inventory item: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('inventory')->with('success', 'Inventory item saved successfully');
    }

    /**
     * Remove the specified pet.
     */
    public function delete($id)
    {
        try {
            $inventory = InventoryItem::findOrFail($id);
            $inventory->delete();
            
            return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete item: ' . $e->getMessage()], 500);
        }
    }
}
