<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Clinic;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

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

    public function updateAppearance(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'site_primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'site_font_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'site_button_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'site_button_text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'site_font_family' => 'required|string',
                'site_font_size' => 'required|integer|min:12|max:18',
            ]);

            // Update appearance settings
            $settings = [
                'site_primary_color' => $request->site_primary_color,
                'site_font_color' => $request->site_font_color,
                'site_button_color' => $request->site_button_color,
                'site_button_text_color' => $request->site_button_text_color,
                'site_font_family' => $request->site_font_family,
                'site_font_size' => $request->site_font_size,
            ];

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => is_numeric($value) ? 'integer' : 'string', 'group' => 'appearance']
                );
            }

            return redirect()->back()->with('success', 'Appearance settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update appearance settings: ' . $e->getMessage());
        }
    }

    public function updateBranding(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'site_name' => 'required|string|max:255',
                'site_description' => 'nullable|string|max:1000',
                'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'site_favicon' => 'nullable|image|mimes:ico,png,jpg,gif|max:1024',
            ]);

            // Update branding settings
            $settings = [
                'site_name' => $request->site_name,
                'site_description' => $request->site_description,
            ];

            // Handle logo upload
            if ($request->hasFile('site_logo')) {
                $logoPath = $request->file('site_logo')->store('logos', 'public');
                $settings['site_logo'] = $logoPath;
            }

            // Handle favicon upload
            if ($request->hasFile('site_favicon')) {
                $faviconPath = $request->file('site_favicon')->store('favicons', 'public');
                $settings['site_favicon'] = $faviconPath;
            }

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => 'string', 'group' => 'branding']
                );
            }

            return redirect()->back()->with('success', 'Branding settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update branding settings: ' . $e->getMessage());
        }
    }

    public function updateSystem(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'app_env' => 'required|string|in:production,staging,development',
                'app_debug' => 'nullable|boolean',
                'cache_enabled' => 'nullable|boolean',
                'session_timeout' => 'required|integer|min:15|max:480',
                'two_factor_enabled' => 'nullable|boolean',
                'password_min_length' => 'required|integer|min:6|max:32',
            ]);

            // Update system settings
            $settings = [
                'app_env' => $request->app_env,
                'app_debug' => $request->has('app_debug') ? '1' : '0',
                'cache_enabled' => $request->has('cache_enabled') ? '1' : '0',
                'session_timeout' => $request->session_timeout,
                'two_factor_enabled' => $request->has('two_factor_enabled') ? '1' : '0',
                'password_min_length' => $request->password_min_length,
            ];

            foreach ($settings as $key => $value) {
                $type = in_array($key, ['session_timeout', 'password_min_length']) ? 'integer' : 'boolean';
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'type' => $type, 'group' => 'system']
                );
            }

            return redirect()->back()->with('success', 'System settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update system settings: ' . $e->getMessage());
        }
    }
}
