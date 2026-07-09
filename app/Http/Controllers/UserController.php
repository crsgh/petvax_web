<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Clinic;
use App\Models\Notification;
use App\Models\Booking;


class UserController extends Controller
{
    /**
     * Email a newly-created account its password. Failures are logged, never
     * thrown, so a slow/unavailable mail server can't block account creation.
     */
    private function sendAccountPasswordEmail(string $email, string $password): void
    {
        try {
            \Mail::raw("Your PetVax account password is: " . $password, function ($message) use ($email) {
                $message->to($email)->subject("PetVax Account Password");
            });
        } catch (\Throwable $e) {
            \Log::error('Account password email failed for ' . $email . ': ' . $e->getMessage());
        }
    }

    public function owners()
    {

        $query = Booking::where('status', 'completed');
    
        $completedPetIds = $query->distinct()->pluck('client_id')->toArray();
        $users = User::with(['role', 'clinic'])
            ->where('role_id', 5)
            ->whereIn('_id', $completedPetIds)
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
            // Roles use integer _ids in Mongo; a form sends role_id as a string
            // which won't match, so normalise it before validating/storing.
            if ($request->filled('role_id') && is_numeric($request->role_id)) {
                $request->merge(['role_id' => (int) $request->role_id]);
            }
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id . ',_id',
                'role_id' => 'required|exists:roles,_id',
                'clinic_id' => 'required|exists:clinics,_id',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($request->id);

            $plainPassword = null;
            if ($id == null) {
                // Generate a random password for the new owner account.
                $plainPassword = \Str::random(8);
                $user->password = bcrypt($plainPassword);
            }

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->uploadImage($request->file('avatar'),'avatars');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'];
            $user->save();

            // Email the password AFTER the account exists; a mail failure must
            // never prevent the account from being created.
            if ($plainPassword !== null) {
                $this->sendAccountPasswordEmail($validated['email'], $plainPassword);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
            // Roles use integer _ids in Mongo; a form sends role_id as a string
            // which won't match, so normalise it before validating/storing.
            if ($request->filled('role_id') && is_numeric($request->role_id)) {
                $request->merge(['role_id' => (int) $request->role_id]);
            }
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id . ',_id',
                'role_id' => 'required|exists:roles,_id',
                'clinic_id' => 'required|exists:clinics,_id',
                'password' => 'nullable|string|min:8',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
            $user = $id == null ? new User : User::findOrFail($request->id);

            $plainPassword = null;
            if ($id == null) {
                // Generate a random password for the new staff account.
                $plainPassword = \Str::random(8);
                $user->password = bcrypt($plainPassword);
            }

            if ($request->hasFile('avatar')) {
                $user->avatar = $this->uploadImage($request->file('avatar'),'avatars');
            }

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->clinic_id = $validated['clinic_id'];
            $user->save();

            // Email the password AFTER the account exists; a mail failure must
            // never prevent the account from being created.
            if ($plainPassword !== null) {
                $this->sendAccountPasswordEmail($validated['email'], $plainPassword);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect('/staffs')->with('success', 'Staff saved successfully');
    }

    public function deleteStaff($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        // add record
        return redirect()->route('staffs')->with('success', 'Staff deleted successfully');
    }
}
