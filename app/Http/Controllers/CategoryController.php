<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Category;
use App\Models\Notification;

class CategoryController extends Controller
{
    public function index(){
        return view('categories',[
            'clinics' => Clinic::all(),
            'categories' => Category::when(auth()->user()->role_id != 1, function($query) {
                return $query->where('categories.clinic_id', auth()->user()->clinic_id);
            })->get(),
           'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsert(Request $request, $id = null)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'clinic_id' => 'required|integer|exists:clinics,id',
                'status' => 'required|in:active,inactive,default',
                'description' => 'nullable|string|max:255',
            ]);

            if ($id) {
                $category = Category::findOrFail($id);
                $category->update($validated);
                $message = 'Category updated successfully';
            } else {
                $category = Category::create($validated);
                $message = 'Category created successfully';
            }

            return redirect()->route('categories')->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save category: ' . $e->getMessage())->withInput();
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            // add record

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
            }

            return redirect()->route('categories')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete category: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}
