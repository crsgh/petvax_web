<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Clinic;
use App\Models\Notification;

class UserController extends Controller
{
    public function owners()
    {
        return view('users', [
            'users' => User::with(['role', 'clinic'])
                ->where('role_id', 5)
                // ->when(auth()->user()->role_id != 1, function($query) {
                //     return $query->where(function($q) {
                //         $q->where('clinic_id', auth()->user()->clinic_id)
                //           ->orWhere('role_id', 5);
                //     });
                // })
                ->get(),
            'roles' => Role::all(),
            'clinics' => Clinic::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsertOwner(Request $request, $id = null)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'role_id' => 'required|exists:roles,id',
                'clinic_id' => 'required|exists:clinics,id',
                'password' => 'nullable|string|min:8',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($request->id);

            if ($validated['password']) {
                $user->password = bcrypt($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->uploadImage($request->file('avatar'),'avatars');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'];
            $user->save();

            // add record

        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }
     
        return redirect('/owners')->with('success', 'Owner saved successfully');
    }

    public function deleteOwner($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        //add record

        return redirect()->route('owners')->with('success', 'Owner deleted successfully');
    }

    public function staffs()
    {
        return view('users', [
            'users' => User::with(['role', 'clinic'])
                ->whereIn('role_id', [2, 3, 4])
                ->when(auth()->user()->role_id != 1, function($query) {
                    return $query->where('clinic_id', auth()->user()->clinic_id);
                })
                ->get(),
            'roles' => Role::all(),
            'clinics' => Clinic::all(),
            'notifications' => match(auth()->user()->role_id) {
                1 => collect([]),
                2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
            },
        ]);
    }

    public function upsertStaff(Request $request, $id = null)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'role_id' => 'required|exists:roles,id',
                'clinic_id' => 'required|exists:clinics,id',
                'password' => 'nullable|string|min:8',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($request->id);

            if ($validated['password']) {
                $user->password = bcrypt($validated['password']);
            }

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->uploadImage($request->file('avatar'),'avatars');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'];
            $user->save();


            // add record
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }
     
        return redirect('/stafss')->with('success', 'Staff saved successfully');
    }

    public function deleteStaff($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        // add record
        return redirect()->route('staffs')->with('success', 'Staff deleted successfully');
    }
}
