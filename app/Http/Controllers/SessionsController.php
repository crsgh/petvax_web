<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store(Request $request)
    {
        $attributes = $request->only(['email', 'password']);

        $user = User::where('email', $attributes['email'])->where('role_id', '!=', 5)->first();

        if ($user && Hash::check($attributes['password'], $user->password)) {
            session()->regenerate();
            auth()->login($user); 
            return redirect('dashboard')->with(['success' => 'You are logged in.']);
        }

        return back()->withErrors(['email' => 'Email or password invalid.']);
    }
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
