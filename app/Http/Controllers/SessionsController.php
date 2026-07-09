<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Clinic;

class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store(Request $request)
    {
        $attributes = $request->only(['email', 'password']);

        $user = User::where('email', $attributes['email'])->first();

        if ($user && Hash::check($attributes['password'], $user->password)) {
            $clinic = Clinic::find($user->clinic_id);
            if ($user->role_id != 1 && $clinic->status != 'active') {
                return back()->withErrors(['email' => 'Your clinic account is inactive. Please contact support.']);
            }

            session()->regenerate();
            auth()->login($user);
            return auth()->user()->role_id == 5 
                ? redirect('owner')->with(['success' => 'You are logged in.'])
                : redirect('dashboard')->with(['success' => 'You are logged in.']);
        }

        return back()->withErrors(['email' => 'Email or password invalid.']);
    }
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }

    public function updatePassword(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password'
            ]);

            // Get the authenticated user
            $user = User::find(auth()->user()->id);

            // Check if old password matches
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'The current password is incorrect']);
            }

            // Update the password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Password has been updated successfully');

        } catch (\Exception $e) {
            dd([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
