<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function owners()
    {
        $users = User::with(['role', 'clinic'])
            ->where('role_id', 5)
            ->paginate(10);

        return view('users', [
            'users' => $users,
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
                'email' => 'required|email|unique:users,email,' . $id,
                'role_id' => 'required|exists:roles,id',
                'clinic_id' => 'nullable|exists:clinics,id',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($id);

            if ($id == null) {
                // Generate random password for new owners
                $password = Str::random(8);
                $user->password = bcrypt($password);
                            
                // Send password email to owner
                try {
                    Mail::raw("Your PetVax account password is: " . $password, function ($message) use ($validated) {
                        $message->to($validated['email'])
                                ->subject("PetVax Account Password");
                    });
                } catch (\Exception $e) {
                    // Log email error but don't fail the user creation
                    Log::error('Failed to send password email: ' . $e->getMessage());
                }
            }

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'] ?? 1; // Default to clinic 1 if not provided
            $user->is_verified = true; // Set as verified by default for admin-created users
            $user->save();

            return redirect('/owners')->with('success', 'Owner saved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save owner: ' . $e->getMessage())->withInput();
        }
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
                ->paginate(8),
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
                'email' => 'required|email|unique:users,email,' . $id,
                'role_id' => 'required|exists:roles,id',
                'clinic_id' => $request->role_id == 4 ? 'required|exists:clinics,id' : 'nullable|exists:clinics,id',
                'password' => 'nullable|string|min:8',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($id);

            if ($id == null) {
                // Generate random password for new staff
                $password = Str::random(8);
                $user->password = bcrypt($password);
                            
                // Send password email to staff
                try {
                    Mail::raw("Your PetVax account password is: " . $password, function ($message) use ($validated) {
                        $message->to($validated['email'])
                                ->subject("PetVax Account Password");
                    });
                } catch (\Exception $e) {
                    // Log email error but don't fail the user creation
                    Log::error('Failed to send password email: ' . $e->getMessage());
                }
            }

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'] ?? 1; // Default to clinic 1 if not provided
            $user->is_verified = true; // Set as verified by default for admin-created users
            $user->save();

            return redirect('/staffs')->with('success', 'Staff saved successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save staff: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteStaff($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        // add record
        return redirect()->route('staffs')->with('success', 'Staff deleted successfully');
    }
}
